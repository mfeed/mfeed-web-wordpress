<?php

/*======================
投稿用ショートコード
======================*/
//パス
function pathCode_function() {
	return get_template_directory_uri();
}
add_shortcode('pathCode', 'pathCode_function');

//サイトURL
function urlCode_function() {
	return home_url();
}
add_shortcode('urlCode', 'urlCode_function');

// サイトタイトル
function nameCode_function() {
	return esc_html(get_bloginfo('name'));
}
add_shortcode('nameCode', 'nameCode_function');

// ディスクリプション
function dscCode_function() {
	return esc_html(get_bloginfo('description'));
}
add_shortcode('dscCode', 'dscCode_function');
