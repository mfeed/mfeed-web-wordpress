<?php

/*======================
WP投稿画面
======================*/
//アイキャッチ画像の有効化
add_action('after_setup_theme', function() {
  add_theme_support('post-thumbnails', ['post', 'page']);
});

// すべての固定ページのエディタを非表示にする
// function post_output_css() {
// 	$pt = get_post_type();
// 	if ($pt == 'page') {
// 		$hide_postdiv_css = '<style type="text/css">#postdiv, #postdivrich { display: none; }</style>';
// 		echo $hide_postdiv_css;
// 	}
// }
// add_action('admin_head', 'post_output_css');

// 記事の自動整形を無効にする
// remove_filter('the_content', 'wpautop');

//標準エディター非表示
// function my_custom_init() {
// 	remove_post_type_support( 'page', 'editor' );
// }
// add_action( 'init', 'my_custom_init' );


/*======================
WPブロックエディタ用
======================*/
// add_action('after_setup_theme', function(){
// 	// ブロックエディタ用スタイル機能をテーマに追加
// 	add_theme_support('editor-styles');
// 	// ブロックエディタ用CSSの読み込み
// 	add_editor_style('/assets/css/editor-style.css');
// });

// add_action('admin_enqueue_scripts', function ($hook_suffix) {
// 	// 新規・編集投稿ページのみ読み込み
// 	if ('post.php' === $hook_suffix || 'post-new.php' === $hook_suffix) {
// 		// CSSディレクトリの設定
// 		$uri = get_template_directory_uri() . "/assets/css/editor-style.css";
// 		// CSSファイルの読み込み
// 		wp_enqueue_style("smart-style", $uri, array(), wp_get_theme()->get('Version'));
// 	}
// });

function custom_rewrite_flush() {
  flush_rewrite_rules();
}
add_action('after_switch_theme', 'custom_rewrite_flush');
