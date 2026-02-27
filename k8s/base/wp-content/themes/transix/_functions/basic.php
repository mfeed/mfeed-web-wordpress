<?php

/*======================
基本設定
======================*/
function theme_setup() {
	/* タイトルの表示 */
	add_theme_support( 'title-tag' );

	// タイトルの区切り文字を「|」にする (必要な場合)
	add_filter('document_title_separator', function(){ return '|'; });

	/* HTML5に対応 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

}
add_action( 'after_setup_theme', 'theme_setup' );

// /* WordPressの自動更新を停止する */
// add_filter( 'automatic_updater_disabled', '__return_true' );

/* WordPressの更新通知を非表示 */
function hide_update_alerts() {
  remove_action('admin_notices','maintenance_nag', 10 );
  remove_action('admin_notices','update_nag', 3 );

  // 管理者以外は非表示にする場合
  if ( current_user_can('update_core') ){
    remove_action('admin_notices','maintenance_nag', 10 );
    remove_action('admin_notices','update_nag', 3 );
  }
}
add_action( 'admin_head', 'hide_update_alerts', 1 );

/* head */
function hook_head() {
  ?>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- GoogleFonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
  <!-- CSS -->
  <link rel="stylesheet" href="<?php echo get_theme_file_uri('/_assets/scss/style.css'); ?>?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://unpkg.com/scroll-hint@latest/js/scroll-hint.min.js"></script>
  <script src="<?php echo get_theme_file_uri('/_assets/js/common.js'); ?>?<?php echo date('Ymd-His'); ?>" defer></script>
  <!-- www.mfeed.ad.jp に対する OneTrust Cookie 同意通知の始点 -->
  <script src="https://cdn-au.onetrust.com/scripttemplates/otSDKStub.js"  type="text/javascript" charset="UTF-8" data-domain-script="019b48f0-46cc-7465-80b5-2b0d148872c8" ></script>
  <script type="text/javascript">
  function OptanonWrapper() { }
  </script>
  <!-- www.mfeed.ad.jp に対する OneTrust Cookie 同意通知の終点 -->
  <?php
  }
add_action('wp_head', 'hook_head');


/* 親ページのスラッグを取得する */
function is_parent_slug() {
	global $post;
	if ($post->post_parent) {
		$post_data = get_post($post->post_parent);
		return $post_data->post_name;
	}
}


/*======================
サイト上の表示
======================*/
// タイトル
add_filter( 'document_title_parts', function( $title ) {
  if ( is_404() ) {
    // 404ページ
    $notfound_title = 'ページが見つかりませんでした';
    if ( ! empty( $notfound_title ) ) {
      $title['title'] = $notfound_title;
    }
  } elseif ( is_search() ) {
    // 検索結果ページ
    $search_title = '「' . get_search_query() . '」の検索結果';
    if ( ! empty( $search_title ) ) {
      $title['title'] = $search_title;
    }
  } elseif ( is_singular() ) {
    // 投稿・固定ページ・CPT共通
    $seo_title = get_field( 'seo_title' );
    if ( ! empty( $seo_title ) ) {
      $title['title'] = $seo_title;
    }
  }
  return $title;
} );

// ディスクリプション
// add_action( 'wp_head', function() {
//   if ( is_404() ) {
//     // 404ページ
//     $notfound_description = 'お探しのページは見つかりませんでした。URLが変更されたか、削除された可能性があります。';
//     if ( ! empty( $notfound_description ) ) {
//       echo '<meta name="description" content="' . esc_attr( $notfound_description ) . '">' . "\n";
//     }
//   } elseif ( is_search() ) {
//     // 検索結果ページ
//     $keyword            = get_search_query();
//     $search_description = '当サイト内で「' . $keyword . '」に関するページを検索した結果です。';
//     if ( ! empty( $search_description ) ) {
//       echo '<meta name="description" content="' . esc_attr( $search_description ) . '">' . "\n";
//     }
//   } elseif ( is_singular() ) {
//     // 投稿・固定ページ・CPT
//     $seo_description = get_field( 'seo_description' );
//     if ( ! empty( $seo_description ) ) {
//       echo '<meta name="description" content="' . esc_attr( $seo_description ) . '">' . "\n";
//     }
//   }
// } );

// <br />を<br>に変換
function brHTML5( $content ) {
	$content = str_replace('<br />', '<br>', $content);
	return $content;
}
add_filter( 'the_content', 'brHTML5' );

// 投稿記事内の最初の画像をアイキャッチ画像代わりにする
function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  if($output) {
    $first_img = $matches [1] [0];
  } else {
    $first_img = get_template_directory_uri().'/assets/img/common/noimage.png';
  }
  return $first_img;
}

// ページコード
function pageCode_function($pageCode) {
	if (is_page()) {
		$page = get_post();
    $pageCode[] = 'pageCode-'. $page->post_name;
  } elseif (is_search()) {
    $pageCode[] = 'pageCode-search';
	} elseif (is_404()) {
    $pageCode[] = 'pageCode-404';
	} elseif (is_category()) {
    $pageCode[] = 'pageCode-category';
  } elseif (is_single()) {
    $pageCode[] = 'pageCode-single';
  } elseif (is_archive() || is_home('works')) {
		$pageCode[] = 'pageCode-archive';
	} elseif (is_home() || is_front_page()) {
		$pageCode[] = 'pageCode-top';
	}
	return $pageCode;
}
add_filter('body_class', 'pageCode_function');

// ツールバー非表示
// add_filter('show_admin_bar', '__return_false');

// サニタイズコールバックで<br>タグを許可
function sanitize_custom_html( $input ) {
  // 許可するHTMLタグを定義
  $allowed_tags = array(
      'br' => array(),  // <br>タグを許可
      'p'  => array(),  // <p> タグを許可
  );
  return wp_kses( $input, $allowed_tags );
}

// /faq/glossary/ を「faq/glossary 固定ページ」に強制マッピング
add_action( 'init', function() {
  add_rewrite_rule(
    '^faq/glossary/?$',             // URLパターン (ドメイン・サイトパスは不要)
    'index.php?pagename=faq/glossary', // 固定ページのパスを指定 (親/子)
    'top'
  );
});
