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
// canonical リダイレクトの調整
add_filter( 'redirect_canonical', function( $redirect, $request ) {

    // ?author= のときはリダイレクトしない（ユーザー列挙対策）
    if ( isset( $_GET['author'] ) ) {
        return false;
    }

    // ★ ホーム / トップページでは canonical リダイレクト自体を無効化
    if ( is_front_page() || is_home() ) {
        return false;
    }

    // ★ /en/ 配下でもリダイレクトしない（english_page / english_press 用URLを壊さない）
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ( strpos( $request_uri, '/en/' ) === 0 ) {
        return false;
    }

    // それ以外はデフォルトの挙動に任せる
    return $redirect;
}, 10, 2 );


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

add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
});

add_filter( 'manage_media_columns', function ( $columns ) {
    echo '<style>.media-icon img[src$=".svg"]{width:100%;}</style>';
    return $columns;
});



// 固定ページ rpki と ntp をサブページへリダイレクト
function redirect_rpki_ntp_pages() {

   // 管理画面 / AJAX / CLI はリダイレクトを回避
    if ( is_admin() || wp_doing_ajax() || php_sapi_name() === 'cli' ) {
        return;
    }

    // ★ /en/ から始まるURLは日本語用リダイレクトの対象外にする
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ( strpos( $request_uri, '/en/' ) === 0 ) {
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


// 英語プレスリリース & 英語固定ページ用のカスタム投稿タイプ
function register_english_post_types() {

    // ▼ 英語プレスリリース
    $press_labels = array(
        'name'               => 'English Press',
        'singular_name'      => 'English Press',
        'menu_name'          => 'English Press',
        'add_new'            => '新規追加',
        'add_new_item'       => '英語プレスリリースを追加',
        'edit_item'          => '英語プレスリリースを編集',
        'new_item'           => '新しい英語プレスリリース',
        'view_item'          => '英語プレスリリースを表示',
        'search_items'       => '英語プレスリリースを検索',
        'not_found'          => '英語プレスリリースはありません',
        'not_found_in_trash' => 'ゴミ箱に英語プレスリリースはありません',
    );

    register_post_type(
    'english_press',
    array(
        'labels'        => $press_labels,
        'public'        => true,
        'has_archive'   => true, 
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-megaphone',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
        'show_in_rest'  => true,
        'rewrite'       => array(
            'slug'       => 'en-press',   // ← 内部用。英語ページと被らなければOK
            'with_front' => false,
        ),
        'taxonomies'    => array( 'category' ),
    )
);

    // ▼ 英語固定ページ相当
    $page_labels = array(
        'name'               => 'English Pages',
        'singular_name'      => 'English Page',
        'menu_name'          => 'English Pages',
        'add_new'            => '新規追加',
        'add_new_item'       => '英語ページを追加',
        'edit_item'          => '英語ページを編集',
        'new_item'           => '新しい英語ページ',
        'view_item'          => '英語ページを表示',
        'search_items'       => '英語ページを検索',
        'not_found'          => '英語ページはありません',
        'not_found_in_trash' => 'ゴミ箱に英語ページはありません',
    );

    register_post_type(
        'english_page',
        array(
            'labels'        => $page_labels,
            'public'        => true,
            'has_archive'   => 'en', // /en/ が english_page のアーカイブ（英語トップ）
            'menu_position' => 6,
            'menu_icon'     => 'dashicons-translation',
            'supports'      => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'revisions',
                'page-attributes', // ← 親子関係・並び順
            ),
            'show_in_rest'  => true,
            'hierarchical'  => true,  // ← 固定ページと同じく親子を持てる
            'rewrite'       => array(
                'slug'         => 'en',   // /en/ 以下にぶら下げる
                'with_front'   => false,
                'hierarchical' => true,   // ← /en/parent/child/ 形式のURLを許可
            ),
        )
    );
}
add_action( 'init', 'register_english_post_types' );

// /en 以下のURLを english_press / english_page に振り分ける
function my_register_english_rewrite_rules() {

    // ① 英語トップ /en/ → english_page のアーカイブ
    add_rewrite_rule(
        '^en/?$',
        'index.php?post_type=english_page',
        'top'
    );

    // ② プレスリリース一覧 /en/archives/
    add_rewrite_rule(
        '^en/archives/?$',
        'index.php?post_type=english_press',
        'top'
    );

    // ③ プレスリリース年別アーカイブ /en/archives/2025/
    add_rewrite_rule(
        '^en/archives/([0-9]{4})/?$',
        'index.php?post_type=english_press&year=$matches[1]',
        'top'
    );
     // ⑤ プレスリリース 日別アーカイブ /en/2025/01/01/
    add_rewrite_rule(
        '^en/([0-9]{4})/([0-9]{2})/([0-9]{2})/?$',
        'index.php?post_type=english_press&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]',
        'top'
    );

    // ④ プレスリリースのシングル /en/2025/slug/
    //    → クエリ的には name=slug だけ見ればよい
    add_rewrite_rule(
        '^en/([0-9]{4})/([^/]+)/?$',
        'index.php?post_type=english_press&name=$matches[2]',
        'top'
    );


}
add_action( 'init', 'my_register_english_rewrite_rules' );




// スラッグから日本語側の投稿を探すヘルパー
function my_find_japanese_post_by_slug( $slug, $post_types = array( 'post', 'page' ) ) {
    if ( empty( $slug ) ) {
        return null;
    }

    $args = array(
        'name'        => $slug,
        'post_type'   => $post_types,
        'post_status' => 'publish',
        'numberposts' => 1,
    );
    $posts = get_posts( $args );

    if ( ! empty( $posts ) ) {
        return $posts[0];
    }

    return null;
}
// 日本語 ⇔ 英語のURLだけを返す（header.php で利用）
function my_get_lang_switch_urls() {
    global $post;

    // デフォルト（対応先が見つからなかったとき）
    $result = array(
        'ja' => home_url( '/' ),
        'en' => home_url( '/en/' ),
    );

    // ★ トップページ（日本語ホーム）のときは必ず / ↔ /en/
    if ( is_front_page() || is_home() ) {
        return $result;
    }

    // ★ 英語トップ（english_page アーカイブ）のときは固定
    if ( is_post_type_archive( 'english_page' ) ) {
        return $result; // / → /en/ のみ
    }

    // ★ 英語プレスリリースのアーカイブ（/en/archives/ や /en/archives/2025/）
    if ( is_post_type_archive( 'english_press' ) ) {
        $year = (int) get_query_var( 'year' );

        if ( $year ) {
            $result['ja'] = home_url( "/archives/{$year}/" );
            $result['en'] = home_url( "/en/archives/{$year}/" );
        } else {
            $result['ja'] = home_url( '/archives/' );
            $result['en'] = home_url( '/en/archives/' );
        }
        return $result;
    }

    // ★ 日本語側の「プレスリリース一覧」固定ページ（/archives/）
    if ( is_page( 'archives' ) ) {
        $result['en'] = home_url( '/en/archives/' );
        return $result;
    }

    // ↓ ここから下は、今まで書いていた page/post/english_page/english_press の対応ロジック
    if ( ! $post ) {
        return $result;
    }

    $type = get_post_type( $post );

    // ① 日本語 固定ページ（page）→ 英語 english_page
    if ( $type === 'page' ) {
        $path         = get_page_uri( $post );
        $english_page = get_page_by_path( $path, OBJECT, 'english_page' );
        if ( $english_page ) {
            $result['en'] = get_permalink( $english_page->ID );
        }

    // ② 日本語 投稿(post) → 英語プレス(english_press)
    } elseif ( $type === 'post' ) {
        $slug          = $post->post_name;
        $english_press = get_page_by_path( $slug, OBJECT, 'english_press' );
        if ( $english_press ) {
            $result['en'] = get_permalink( $english_press->ID );
        }

    // ③ 英語ページ english_page → 日本語 固定ページ page
    } elseif ( $type === 'english_page' ) {
        $path   = get_page_uri( $post );
        $jp_page = get_page_by_path( $path, OBJECT, 'page' );
        if ( $jp_page ) {
            $result['ja'] = get_permalink( $jp_page->ID );
        }

    // ④ 英語プレス english_press → 日本語 投稿 post
    } elseif ( $type === 'english_press' ) {
        $slug = $post->post_name;
        $jp_posts = get_posts( array(
            'name'        => $slug,
            'post_type'   => 'post',
            'post_status' => 'publish',
            'numberposts' => 1,
        ) );
        if ( ! empty( $jp_posts ) ) {
            $result['ja'] = get_permalink( $jp_posts[0]->ID );
        }
    }

    return $result;
}


function custom_redirects() {
    // /en/about/ -> /en/about/message/
    if (preg_match('#^/en/about/?$#', $_SERVER['REQUEST_URI'])) {
        wp_redirect(home_url('/en/about/message/'), 301);
        exit;
    }

    // /en/rpki/ -> /en/rpki/whatisrpki/
    if (preg_match('#^/en/rpki/?$#', $_SERVER['REQUEST_URI'])) {
        wp_redirect(home_url('/en/rpki/whatisrpki/'), 301);
        exit;
    }
}
add_action('template_redirect', 'custom_redirects');

function my_press_archive_order( $query ) {

  // 管理画面またはメインクエリ以外は除外
  if ( is_admin() || ! $query->is_main_query() ) {
    return;
  }

  // 英語版のプレスリリースアーカイブ（/en/archives/...）
  if ( $query->is_archive() && strpos( $_SERVER['REQUEST_URI'], '/en/archives/' ) !== false ) {
    $query->set( 'orderby', 'date' );
    $query->set( 'order', 'DESC' ); // ← 新しい順
  }
}
add_action( 'pre_get_posts', 'my_press_archive_order' );

// 英語プレスリリースのパーマリンクを /en/年/スラッグ/ にする
function my_english_press_link( $permalink, $post ) {

    // 対象は english_press だけ
    if ( $post->post_type !== 'english_press' ) {
        return $permalink;
    }

    // まだスラッグが決まっていない or サンプルパーマリンク（%postname%）が含まれているときは触らない
    if ( empty( $post->post_name ) || strpos( $permalink, '%postname%' ) !== false ) {
        return $permalink;
    }

    // 投稿日から年を取得（ない場合は現在時刻）
    $year = $post->post_date ? mysql2date( 'Y', $post->post_date ) : current_time( 'Y' );
    $slug = $post->post_name;

    // /en/2025/slug/ の形式で返す
    return home_url( sprintf( 'en/%s/%s/', $year, $slug ) );
}
add_filter( 'post_type_link', 'my_english_press_link', 10, 2 );
