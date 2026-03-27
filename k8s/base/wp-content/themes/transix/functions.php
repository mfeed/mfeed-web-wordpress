<?php

// 分割したファイルパスを配列に追加
$function_files = [
  '/_functions/customizer.php', // カスタマイザー
  '/_functions/basic.php', // 基本設定
  '/_functions/shortcode.php', // ショートコード
  '/_functions/widgets.php', // ウィジェット
  '/_functions/management.php', // 管理画面
  '/_functions/post.php', // 投稿画面
  '/_functions/plugins.php', // その他のプラグイン
];

foreach ($function_files as $file) {
  if ((file_exists(__DIR__ . $file))) { // ファイルが存在する場合
    // ファイルを読み込む
    locate_template($file, true, true);
  } else { // ファイルが見つからない場合
    // エラーメッセージを表示
    trigger_error("`$file`ファイルが見つかりません", E_USER_ERROR);
  }
}

function relevanssi_ngram_tokenizer($content) {

    $content = preg_replace('/\s+/u', '', $content);
    $length = mb_strlen($content);
    $tokens = [];

    for ($i = 0; $i < $length - 1; $i++) {
        $tokens[] = mb_substr($content, $i, 2);
    }

    return implode(' ', $tokens);
}

add_filter('relevanssi_content_to_index', 'relevanssi_ngram_tokenizer');

// Inside-word matching
function rlv_partial_inside_words($query) {
    return "(relevanssi.term LIKE '%#term#%')";
}
add_filter('relevanssi_fuzzy_query', 'rlv_partial_inside_words');

add_filter('relevanssi_highlight_query', function($query) {
    $q = trim(get_search_query());
    if ($q === '') return $query;

    return '"' . $q . '"';
});


function mfeed_term_to_regex(string $term): string {
    $term = trim($term);
    if ($term === '') return '';

    $q = preg_quote($term, '/');

    $hyphens = '[-‐-‒–—―−－]';
    $q = str_replace('\-', $hyphens, $q);

    return $q;
}

function mfeed_mark_highlight_html(string $html, string $query): string {
    $query = trim($query);
    if ($query === '' || $html === '') return $html;

    $terms = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
    usort($terms, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

    $parts = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($parts as &$part) {
        if ($part === '' || $part[0] === '<') continue;

        foreach ($terms as $t) {
            $rx = mfeed_term_to_regex($t);
            if ($rx === '') continue;

            $part = preg_replace('/' . $rx . '/iu', '<mark>$0</mark>', $part);
        }
    }
    unset($part);

    return implode('', $parts);
}
function mfeed_normalize_for_find(string $s): string {
    $s = mb_strtolower($s);
    $s = preg_replace('/[‐-‒–—―−－]/u', '-', $s);
    return $s;
}

function mfeed_context_excerpt_from_content(int $post_id, string $query, int $radius = 140, int $maxlen = 320): string {
    $query = trim($query);

    $content = get_post_field('post_content', $post_id);
    $text = wp_strip_all_tags($content, true);
    $text = preg_replace('/\s+/u', ' ', $text);
    $text = trim($text);
    if ($text === '') return '';

    if ($query === '') {
        $out = mb_substr($text, 0, $maxlen);
        if (mb_strlen($text) > $maxlen) $out .= '...';
        return esc_html($out);
    }

    $terms = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
    usort($terms, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

    $norm_text = mfeed_normalize_for_find($text);

    $pos = null;
    $hit_term = '';

    foreach ($terms as $t) {
        $norm_t = mfeed_normalize_for_find($t);
        $p = mb_strpos($norm_text, $norm_t);
        if ($p !== false && ($pos === null || $p < $pos)) {
            $pos = $p;
            $hit_term = $t;
        }
    }

    if ($pos === null) {
        $out = mb_substr($text, 0, $maxlen);
        if (mb_strlen($text) > $maxlen) $out .= '...';
        return esc_html($out);
    }

    $start = max(0, $pos - $radius);
    $end   = min(mb_strlen($text), $pos + mb_strlen($hit_term) + $radius);

    $snippet = mb_substr($text, $start, $end - $start);

    if (mb_strlen($snippet) > $maxlen) {
        $snippet = mb_substr($snippet, 0, $maxlen);
    }

    if ($start > 0) $snippet = '...' . $snippet;
    if ($end < mb_strlen($text)) $snippet .= '...';

    return esc_html($snippet);
}
add_filter('relevanssi_highlight_regex', function($regex) {
    return str_replace('-', '\-', $regex);
});