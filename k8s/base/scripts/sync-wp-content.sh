#!/bin/sh
# ------------------------------------------------------------------------------
# sync-wp-content.sh
#
# イメージに焼き込まれた wp-content のシード (/seed/wp-content) を、
# 全 Pod が共有する RWX PVC (/var/www/html/wp-content) へ反映する。
#
#   - themes / plugins / mu-plugins / languages などのコードディレクトリは
#     「ミラー」: シードに無いファイルは消える（GitHub が正）。
#     退避 → 差し替えの 2 回の mv で切り替えるので、配信中に themes/ が
#     一瞬空になることがない。
#   - uploads など WordPress 側で増え続けるディレクトリは「マージ」: 消さない。
#   - 同期後に .seed-version を書く。initContainer はこの値が一致していれば
#     何もしないので、Pod ごとに PVC を書き換える競合が起きない。
#
# 環境変数:
#   WP_CONTENT_SEED_DIR     シード元           (default: /seed/wp-content)
#   WP_CONTENT_TARGET_DIR   同期先 (PVC)       (default: /var/www/html/wp-content)
#   WP_CONTENT_SEED_VERSION 期待バージョン     (default: 空 = 毎回同期する)
#   WP_CONTENT_SEED_FORCE   1 ならバージョンが一致していても同期 (default: 0)
#   WP_CONTENT_MERGE_DIRS   マージ扱いにするディレクトリ名（空白区切り）
#   WP_CONTENT_PRUNE        1 ならシードに無いトップレベル項目も消す (default: 0)
#   WP_CONTENT_OWNER        chown 先。空文字なら chown しない
#   WP_CONTENT_LOCK_TIMEOUT ロック待ち／stale 判定の秒数 (default: 600)
# ------------------------------------------------------------------------------
set -eu

SEED_DIR="${WP_CONTENT_SEED_DIR:-/seed/wp-content}"
TARGET_DIR="${WP_CONTENT_TARGET_DIR:-/var/www/html/wp-content}"
VERSION_FILE="${TARGET_DIR}/.seed-version"
LOCK_DIR="${TARGET_DIR}/.seed-lock"

DESIRED="${WP_CONTENT_SEED_VERSION:-}"
FORCE="${WP_CONTENT_SEED_FORCE:-0}"
MERGE_DIRS="${WP_CONTENT_MERGE_DIRS:-uploads upgrade upgrade-temp-backup cache}"
PRUNE="${WP_CONTENT_PRUNE:-0}"
OWNER="${WP_CONTENT_OWNER-www-data:www-data}"
LOCK_TIMEOUT="${WP_CONTENT_LOCK_TIMEOUT:-600}"

RUN_ID="$$"
LOCK_HELD=0

log() { echo "[sync-wp-content] $*"; }

# --- ロック ------------------------------------------------------------------
# RWX PVC を複数 Pod が同時に書き換えないようにする。mkdir は NFS 上でも
# アトミックなので、これをロックとして使う。

lock_age_seconds() {
  _mtime="$(stat -c %Y "$LOCK_DIR" 2>/dev/null || echo "")"
  if [ -z "$_mtime" ]; then
    echo 0
    return 0
  fi
  echo $(( $(date +%s) - _mtime ))
}

release_lock() {
  if [ "$LOCK_HELD" = "1" ]; then
    rm -rf "$LOCK_DIR" || true
    LOCK_HELD=0
  fi
}

acquire_lock() {
  _waited=0
  while : ; do
    if mkdir "$LOCK_DIR" 2>/dev/null; then
      LOCK_HELD=1
      echo "${HOSTNAME:-unknown}" > "${LOCK_DIR}/owner" 2>/dev/null || true
      return 0
    fi

    # 異常終了で置き去りにされたロックを回収する
    if [ "$(lock_age_seconds)" -gt "$LOCK_TIMEOUT" ]; then
      log "stale lock found (age > ${LOCK_TIMEOUT}s), removing ${LOCK_DIR}"
      rm -rf "$LOCK_DIR" || true
      continue
    fi

    if [ "$_waited" -ge "$LOCK_TIMEOUT" ]; then
      log "ERROR: could not acquire ${LOCK_DIR} within ${LOCK_TIMEOUT}s"
      return 1
    fi

    log "waiting for another pod to finish syncing... (${_waited}s)"
    sleep 5
    _waited=$(( _waited + 5 ))
  done
}

trap release_lock EXIT INT TERM

# --- ヘルパ ------------------------------------------------------------------

current_version() {
  if [ -f "$VERSION_FILE" ]; then
    cat "$VERSION_FILE" 2>/dev/null || true
  fi
}

is_merge_dir() {
  for _d in $MERGE_DIRS; do
    if [ "$1" = "$_d" ]; then
      return 0
    fi
  done
  return 1
}

apply_owner() {
  if [ -z "$OWNER" ]; then
    return 0
  fi
  chown -R "$OWNER" "$1" 2>/dev/null || log "warn: chown failed for $1 (continuing)"
}

# ミラー: 一旦 stage に展開し、退避 → 差し替えで切り替える。
mirror_dir() {
  _name="$1"
  _src="${SEED_DIR}/${_name}"
  _dst="${TARGET_DIR}/${_name}"
  _stage="${TARGET_DIR}/.seed-stage-${_name}-${RUN_ID}"
  _retired="${TARGET_DIR}/.seed-retired-${_name}-${RUN_ID}"

  rm -rf "$_stage" "$_retired"
  mkdir -p "$_stage"
  cp -af "${_src}/." "${_stage}/"
  apply_owner "$_stage"

  if [ -e "$_dst" ]; then
    mv "$_dst" "$_retired"
  fi
  mv "$_stage" "$_dst"
  rm -rf "$_retired"
}

# マージ: シードにあるものを上書きするだけで、PVC 側の余分は消さない。
merge_dir() {
  _name="$1"
  _src="${SEED_DIR}/${_name}"
  _dst="${TARGET_DIR}/${_name}"

  mkdir -p "$_dst"
  cp -af "${_src}/." "${_dst}/"
  apply_owner "$_dst"
}

copy_file() {
  _name="$1"
  cp -af "${SEED_DIR}/${_name}" "${TARGET_DIR}/${_name}"
  apply_owner "${TARGET_DIR}/${_name}"
}

# --- 本体 --------------------------------------------------------------------

if [ ! -d "$SEED_DIR" ]; then
  log "ERROR: seed directory not found: ${SEED_DIR}"
  log "k8s/base をビルドコンテキストにしてイメージを作り、wp-content が"
  log "${SEED_DIR} に入るようにしてください。"
  exit 1
fi

mkdir -p "$TARGET_DIR"

_current="$(current_version)"
if [ "$FORCE" != "1" ] && [ -n "$DESIRED" ] && [ "$DESIRED" = "$_current" ]; then
  log "wp-content already up to date (version=${_current}) - nothing to do"
  exit 0
fi

acquire_lock

# ロック取得までの間に他の Pod が同期を終えているかもしれないので、もう一度見る
_current="$(current_version)"
if [ "$FORCE" != "1" ] && [ -n "$DESIRED" ] && [ "$DESIRED" = "$_current" ]; then
  log "another pod already synced wp-content (version=${_current}) - nothing to do"
  exit 0
fi

log "sync ${SEED_DIR} -> ${TARGET_DIR}"
log "  desired=${DESIRED:-<empty>} current=${_current:-<empty>} force=${FORCE}"
log "  merge dirs: ${MERGE_DIRS}"

# 前回の異常終了で残った作業ディレクトリを掃除する（ロック保持中なので安全）
rm -rf "${TARGET_DIR}"/.seed-stage-* "${TARGET_DIR}"/.seed-retired-* 2>/dev/null || true

# ドットファイルは対象外。.seed-version などの管理ファイルを
# シードに取り込んでしまう事故を防ぐため、あえて glob のままにしている。
for _path in "${SEED_DIR}"/*; do
  [ -e "$_path" ] || continue
  _name="$(basename "$_path")"

  case "$_name" in
    .seed-version|debug.log|.DS_Store)
      log "skip   ${_name}"
      continue
      ;;
  esac

  if [ -d "$_path" ]; then
    if is_merge_dir "$_name"; then
      log "merge  ${_name}/"
      merge_dir "$_name"
    else
      log "mirror ${_name}/"
      mirror_dir "$_name"
    fi
  else
    log "file   ${_name}"
    copy_file "$_name"
  fi
done

# シードに無いトップレベル項目の削除。既定では行わない。
# マージ扱いのディレクトリ（uploads など）は PRUNE=1 でも消さない。
if [ "$PRUNE" = "1" ]; then
  for _path in "${TARGET_DIR}"/*; do
    [ -e "$_path" ] || continue
    _name="$(basename "$_path")"

    if is_merge_dir "$_name"; then
      continue
    fi
    if [ -e "${SEED_DIR}/${_name}" ]; then
      continue
    fi

    log "prune  ${_name} (シードに存在しない)"
    rm -rf "$_path"
  done
fi

# シードに uploads が無い場合でも WordPress が書ける場所は用意しておく
if [ ! -d "${TARGET_DIR}/uploads" ]; then
  mkdir -p "${TARGET_DIR}/uploads"
  apply_owner "${TARGET_DIR}/uploads"
fi

printf '%s' "$DESIRED" > "$VERSION_FILE"
log "seed version updated: ${DESIRED:-<empty>}"

log "== result =="
ls -la "$TARGET_DIR" || true
for _d in themes plugins uploads; do
  echo "-- ${_d} --"
  ls -la "${TARGET_DIR}/${_d}" 2>/dev/null | head -30 || true
done

log "sync complete"
