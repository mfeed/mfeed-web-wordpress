<?php 
remove_action('wp_head','wp_generator');
remove_action('wp_head','rsd_link');
remove_action('wp_head','wlwmanifest_link');
remove_action('wp_head','rest_output_link_wp_head');
remove_action('wp_head','wp_oembed_add_discovery_links');
remove_action('wp_head','wp_oembed_add_host_js');
add_filter( 'author_rewrite_rules', '__return_empty_array' );
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
// XML-RPC を無効化
add_filter('xmlrpc_enabled', '__return_false');

// REST API：未ログインはブロック（必要なら許可するルートを追加）
add_filter('rest_authentication_errors', function($result){
    if ( !empty($result) ) return $result;
    if ( is_user_logged_in() ) return $result;
    return new WP_Error('rest_forbidden', 'REST API is restricted.', array('status' => 401));
});

// author=1 等のユーザー列挙をブロック（?author= のリダイレクト抑止）
add_filter('redirect_canonical', function($redirect, $request){
    if ( isset($_GET['author']) ) return false;
    return $redirect;
}, 10, 2);

// 短縮URL、フィード、隣接投稿リンクなど
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
// dns-prefetch の出力を最小化
add_filter('wp_resource_hints', function($urls, $rel){
    if ($rel === 'dns-prefetch') {
        return array(
            'https://fonts.gstatic.com',
            'https://www.googletagmanager.com',
        );
    }
    return $urls;
}, 10, 2);

add_action('init', function(){
    // ルート削除
    remove_action('rest_api_init', 'wp_oembed_register_route');
    // 自動発見・結果フィルタ無効
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    // フィードからの埋め込みも停止
    add_filter('pre_oembed_result', '__return_false');
    // 書き換えルールを削除
    add_filter('rewrite_rules_array', function($rules){
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) unset($rules[$rule]);
        }
        return $rules;
    });
});

add_filter('the_generator', '__return_empty_string');

// ピンバック機能を停止
add_filter('xmlrpc_methods', function($methods){
    unset($methods['pingback.ping']);
    return $methods;
});

// コメントフィードを無効化（不要なら）
foreach (['do_feed_rdf','do_feed_rss','do_feed_rss2','do_feed_atom'] as $feed) {
    add_action($feed, function(){ wp_die('No feed available.', '', 404); }, 1);
}

function shortcode_template_dir() {
    return get_stylesheet_directory_uri();
}
add_shortcode( 'template', 'shortcode_template_dir' );

function shortcode_get_template( $atts ) {

    // ショートコード属性
    $atts = shortcode_atts(
        array(
            'file' => '',   // 読み込みたいテンプレートファイル名
        ),
        $atts
    );

    // ファイル名が指定されていなければ終了
    if ( empty( $atts['file'] ) ) return '';

    // テンプレートファイルのパス（テーマフォルダ内）
    $template_path = locate_template( $atts['file'], false, false );

    if ( ! $template_path ) {
        return '<!-- template not found: ' . esc_html( $atts['file'] ) . ' -->';
    }

    // 出力をバッファリングしてテンプレを読み込み、内容を返す
    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode( 'tpl', 'shortcode_get_template' );

// 固定ページ rpki と ntp をサブページへリダイレクト
function redirect_rpki_ntp_pages() {

    // 管理画面 / AJAX / CLI はリダイレクトを回避
    if ( is_admin() || wp_doing_ajax() || php_sapi_name() === 'cli' ) {
        return;
    }

    if ( is_page( 'rpki' ) ) {
        wp_redirect( home_url( '/rpki/whatisrpki/' ), 301 );
        exit;
    }

    if ( is_page( 'ntp' ) ) {
        wp_redirect( home_url( '/ntp/overview/' ), 301 );
        exit;
    }
    
    if ( is_page( 'about' ) ) {
        wp_redirect( home_url( '/about/message/' ), 301 );
        exit;
    }

}
add_action( 'template_redirect', 'redirect_rpki_ntp_pages' );

// /archives/2024/ → pagename=archives & archive_year=2024 にする
function mf_archive_rewrite() {
  add_rewrite_rule(
    '^archives/([0-9]{4})/?$',
    'index.php?pagename=archives&archive_year=$matches[1]',
    'top'
  );
}
add_action('init', 'mf_archive_rewrite');

// archive_year というクエリ変数を許可
function mf_add_query_vars($vars) {
  $vars[] = 'archive_year';
  return $vars;
}
add_filter('query_vars', 'mf_add_query_vars');
