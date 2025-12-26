<?php
/**
 * Plugin Name: FAQ Feedback (per site)
 */
//DBテーブル作成（有効化時）
defined('ABSPATH') || exit;

register_activation_hook(__FILE__, function () {
  global $wpdb;
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';

  $table = $wpdb->prefix . 'faq_feedback';
  $charset = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE {$table} (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id BIGINT UNSIGNED NOT NULL,
    choice VARCHAR(50) NOT NULL,
    page_url TEXT NULL,
    user_ip VARBINARY(16) NULL,
    user_agent TEXT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY post_id (post_id),
    KEY choice (choice),
    KEY created_at (created_at)
  ) {$charset};";

  dbDelta($sql);
});

//送信API（REST）を用意してDBにINSERT


add_action('rest_api_init', function () {
  register_rest_route('faq-feedback/v1', '/submit', [
    'methods'  => 'POST',
    'permission_callback' => '__return_true', // 公開ページから送るため
    'callback' => 'faq_feedback_submit',
  ]);
});

function faq_feedback_submit($req) {
  try {
    // nonceチェック
    $nonce = $req->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
      return new WP_REST_Response(['ok' => false, 'message' => 'nonce error'], 403);
    }

    // パラメータ取得
    $post_id  = (int) $req->get_param('post_id');
    $choice   = sanitize_key($req->get_param('choice'));
    $page_url = esc_url_raw($req->get_param('page_url'));

    $allowed = ['solved','solved_but_unclear','not_solved','not_found'];
    if (!$post_id || !in_array($choice, $allowed, true)) {
      return new WP_REST_Response(['ok' => false, 'message' => 'invalid params'], 400);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'faq_feedback';

    // IP（IPv4/IPv6対応・関数未存在対策）
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ip_bin = null;
    if ($ip && function_exists('inet_pton')) {
      $packed = @inet_pton($ip);
      if ($packed !== false) {
        $ip_bin = $packed;
      }
    }

    $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);

    // INSERTデータ
    $data = [
      'post_id'    => $post_id,
      'choice'     => $choice,
      'page_url'   => $page_url,
      'user_agent' => $ua,
      'created_at' => current_time('mysql'),
    ];
    $format = ['%d','%s','%s','%s','%s'];

    if ($ip_bin !== null) {
      $data['user_ip'] = $ip_bin;
      $format[] = '%s';
    }

    // DB INSERT
    $result = $wpdb->insert($table, $data, $format);
    if ($result === false) {
      return new WP_REST_Response([
        'ok' => false,
        'message' => 'db insert failed',
        'db_error' => $wpdb->last_error,
        'table' => $table,
      ], 500);
    }

    return new WP_REST_Response(['ok' => true], 200);

  } catch (Throwable $e) {
    return new WP_REST_Response([
      'ok' => false,
      'message' => 'exception',
      'error' => $e->getMessage(),
    ], 500);
  }

  $post_id = (int) $req->get_param('post_id');
  $choice  = sanitize_key($req->get_param('choice'));
  $page_url = esc_url_raw($req->get_param('page_url'));
  $result = $wpdb->insert($table, $data, $format);

  $allowed = ['solved','solved_but_unclear','not_solved','not_found'];
  if ($result === false) {
  return new WP_REST_Response([
    'ok' => false,
    'message' => 'db insert failed',
    'db_error' => $wpdb->last_error,
  ], 500);
}

return new WP_REST_Response(['ok' => true], 200);

  global $wpdb;
  $table = $wpdb->prefix . 'faq_feedback';

  // IP（不要なら丸ごと削除）
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  $ip_bin = null;
  if ($ip) {
    $packed = @inet_pton($ip);
    if ($packed !== false) $ip_bin = $packed;
  }

  $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);

  $data = [
  'post_id'    => $post_id,
  'choice'     => $choice,
  'page_url'   => $page_url,
  'user_agent' => $ua,
  'created_at' => current_time('mysql'),
];
$format = ['%d','%s','%s','%s','%s'];

if ($ip_bin !== null) {
  $data['user_ip'] = $ip_bin;
  $format[] = '%s';
}

$wpdb->insert($table, $data, $format);

  return new WP_REST_Response(['ok' => true], 200);
}



//フロントJS（ボタンクリック → RESTへ送信）

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script(
    'faq-feedback',
    plugin_dir_url(__FILE__) . 'faq-feedback.js',
    [],
    '1.0.0',
    true
  );
  wp_localize_script('faq-feedback', 'FaqFeedback', [
    'endpoint' => rest_url('faq-feedback/v1/submit'),
    'nonce'    => wp_create_nonce('wp_rest'),
  ]);
});


//管理画面：CSVダウンロード機能を追加

add_action('admin_menu', function () {
  add_menu_page(
    'FAQ Feedback',
    'FAQ Feedback',
    'manage_options',
    'faq-feedback',
    'faq_feedback_admin_page',
    'dashicons-chart-bar',
    80
  );
});

function faq_feedback_admin_page() {
  if (!current_user_can('manage_options')) return;

  global $wpdb;
  $table = $wpdb->prefix . 'faq_feedback';

  // choice → 日本語ラベル
  $choice_map = [
    'solved'             => '解決した',
    'solved_but_unclear' => '解決したが分かりにくかった',
    'not_solved'         => '解決しなかった',
    'not_found'          => '探した内容ではなかった',
  ];

  // 総数
  $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

  // 選択肢別件数
  $rows = $wpdb->get_results("
    SELECT choice, COUNT(*) AS cnt
    FROM {$table}
    GROUP BY choice
    ORDER BY cnt DESC
  ", ARRAY_A);

  // choiceキーで引けるように整形
  $counts = [];
  foreach ($rows as $r) {
    $counts[$r['choice']] = (int) $r['cnt'];
  }

  $download_url = wp_nonce_url(
    admin_url('admin-post.php?action=faq_feedback_download'),
    'faq_feedback_download'
  );

  echo '<div class="wrap">';
  echo '<h1>FAQ Feedback</h1>';

  // 件数表示
  echo '<h2>件数サマリー</h2>';
  echo '<p><strong>総件数：</strong>' . esc_html(number_format_i18n($total)) . '</p>';

  echo '<table class="widefat striped" style="max-width:720px;">';
  echo '<thead><tr><th>選択肢</th><th style="width:160px;">件数</th></tr></thead>';
  echo '<tbody>';

  // 表示順は固定（見やすく）
  foreach ($choice_map as $key => $label) {
    $cnt = $counts[$key] ?? 0;
    echo '<tr>';
    echo '<td>' . esc_html($label) . '</td>';
    echo '<td>' . esc_html(number_format_i18n($cnt)) . '</td>';
    echo '</tr>';
  }

  echo '</tbody></table>';

  echo '<hr>';
  echo '<p><a class="button button-primary" href="'.esc_url($download_url).'">CSVをダウンロード</a></p>';
  echo '</div>';
}


add_action('admin_post_faq_feedback_download', function () {
  if (!current_user_can('manage_options')) wp_die('forbidden');
  check_admin_referer('faq_feedback_download');

  global $wpdb;
  $table = $wpdb->prefix . 'faq_feedback';

  $rows = $wpdb->get_results("SELECT id, post_id, choice, page_url, created_at FROM {$table} ORDER BY id DESC", ARRAY_A);

  header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=faq-feedback-' . date('Ymd-His') . '.csv');
echo "\xEF\xBB\xBF";

  $out = fopen('php://output', 'w');

  // ヘッダ
fputcsv($out, ['ID','投稿ID','投稿タイトル','選択結果','ページURL','日時']);

  $choice_map = [
  'solved'             => '解決した',
  'solved_but_unclear' => '解決したが分かりにくかった',
  'not_solved'         => '解決しなかった',
  'not_found'          => '探した内容ではなかった',
];

foreach ($rows as $r) {
  $title = get_the_title((int)$r['post_id']) ?: '(削除済み)';
  $choice = $choice_map[$r['choice']] ?? $r['choice'];

  fputcsv($out, [
    $r['id'],
    $r['post_id'],
    $title,
    $choice,
    $r['page_url'],
    $r['created_at'],
  ]);
}


  fclose($out);
  exit;
});

