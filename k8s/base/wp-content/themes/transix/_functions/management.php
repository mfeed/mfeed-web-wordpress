<?php

/*======================
WP管理画面
======================*/
//デフォルトの「投稿」を非表示
function remove_menus () {
	global $menu;
	remove_menu_page( 'edit.php' ); // 投稿を非表示
}
add_action('admin_menu', 'remove_menus');

//デフォルトの「投稿」のスラッグ変更
function post_has_archive( $args, $post_type ) {
	if ( 'post' == $post_type ) {
		$args['rewrite'] = true;
		$args['has_archive'] = 'news'; //任意のスラッグ名
	}
	return $args;
}
// add_filter( 'register_post_type_args', 'post_has_archive', 10, 2 );

// アーカイブをパンくずに追加
function my_static_breadcrumb_adder( $breadcrumb_trail ) {

  if (is_post_type_archive('post')) { // デフォルトの投稿一覧ページの場合
    $item = new bcn_breadcrumb('NEWS', null, array('post'));
  } elseif (get_post_type() === 'post') { // デフォルトの投稿ページの場合
    $item = new bcn_breadcrumb('NEWS', null, array('post'), home_url('news/'), null, true);
  } elseif (is_category()) {
    $item = new bcn_breadcrumb('NEWS', null, array('post'));
  } else {
    $item = NULL;
  }

  $stuck = array_pop( $breadcrumb_trail->breadcrumbs ); // HOME 一時退避
  $breadcrumb_trail->breadcrumbs[] = $item; // 任意の名前 追加
  $breadcrumb_trail->breadcrumbs[] = $stuck; // HOME 戻す

}
// add_action('bcn_after_fill', 'my_static_breadcrumb_adder');


// 投稿からカテゴリー・タグを非表示
function hide_taxonomy_from_menu() {
	global $wp_taxonomies;
	// カテゴリー
	if ( !empty( $wp_taxonomies['category']->object_type ) ) {
		foreach ( $wp_taxonomies['category']->object_type as $i => $object_type ) {
			if ( $object_type == 'post' ) {
				unset( $wp_taxonomies['category']->object_type[$i] );
			}
		}
	}

	// タグ
	if ( !empty( $wp_taxonomies['post_tag']->object_type ) ) {
		foreach ( $wp_taxonomies['post_tag']->object_type as $i => $object_type ) {
			if ( $object_type == 'post' ) {
				unset( $wp_taxonomies['post_tag']->object_type[$i] );
			}
		}
	}
	return true;
}
// add_action( 'init', 'hide_taxonomy_from_menu' );

//デフォルトの「投稿」の名前変更
function Change_menulabel() {
  global $menu;
  global $submenu;
  $name = 'NEWS';
  $menu[5][0] = $name;
  $submenu['edit.php'][5][0] = $name.'一覧';
  $submenu['edit.php'][10][0] = '新規'.$name.'投稿';
}
function Change_objectlabel() {
  global $wp_post_types;
  $name = 'NEWS';
  $labels = &$wp_post_types['post']->labels;
  $labels->name = $name;
  $labels->singular_name = $name;
  $labels->add_new = _x('追加', $name);
  $labels->add_new_item = $name.'の新規追加';
  $labels->edit_item = $name.'の編集';
  $labels->new_item = '新規'.$name;
  $labels->view_item = $name.'を表示';
  $labels->search_items = $name.'を検索';
  $labels->not_found = $name.'が見つかりませんでした';
  $labels->not_found_in_trash = 'ゴミ箱に'.$name.'は見つかりませんでした';
}
// add_action( 'init', 'Change_objectlabel' );
// add_action( 'admin_menu', 'Change_menulabel' );

//デフォルトの「投稿」のアイコン変更
// https://developer.wordpress.org/resource/dashicons/
function ChangeAdminIcons() {
  ?>
  <style>
    #menu-posts .dashicons-admin-post:before { content: '\f119';}

    /* MW WP Form */
    #menu-posts-mw-wp-form .dashicons-admin-post:before { content: '\f465';}
  </style>
  <?php
}
// add_action( 'admin_head', 'ChangeAdminIcons' );

//SVGをアップロード可能にする
function add_file_types_to_uploads($file_types){
	$new_filetypes = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$file_types = array_merge($file_types, $new_filetypes );
	return $file_types;
}
add_action('upload_mimes', 'add_file_types_to_uploads');

// 外観：メニュー
// function register_my_menus() {
//   register_nav_menus( array( //複数のナビゲーションメニューを登録する関数
//     //'「メニューの位置」の識別子' => 'メニューの説明の文字列',
//     'main-nav' => 'メインナビゲーション',
//   ) );
// }
// add_action( 'after_setup_theme', 'register_my_menus' );

// 外観＞カスタマイズ内の項目を非表示
add_action( 'customize_register', function ( $wp_customize ) {
  $wp_customize->remove_section( 'nav_menus' );
  $wp_customize->remove_panel( 'nav_menus' ); // メニュー項目
});

// カスタムロゴ機能を有効化
add_action('after_setup_theme', function(){
  add_theme_support('custom-logo');
});

// 管理画面の「よくあるご質問」一覧にカテゴリー列を追加
add_filter( 'manage_edit-faq_columns', function( $columns ) {

  // 追加したい位置を調整したければここで並び替えしてもOK
  $columns['faq_category'] = 'カテゴリー';

  return $columns;
} );

// 各行にカテゴリー名を出力
add_action( 'manage_faq_posts_custom_column', function( $column, $post_id ) {

  if ( $column === 'faq_category' ) {

    // ★タクソノミースラッグをここで指定
    $terms = get_the_terms( $post_id, 'faq-category' );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $names = wp_list_pluck( $terms, 'name' );
        echo esc_html( implode( ', ', $names ) );
    } else {
        echo '—';
    }
  }

}, 10, 2 );

// 「よくあるご質問」一覧にカテゴリー絞り込みプルダウンを追加
add_action( 'restrict_manage_posts', function() {
  global $typenow;

  if ( $typenow !== 'faq' ) {
    return;
  }

  $taxonomy = 'faq-category'; // ←タクソノミースラッグ

  wp_dropdown_categories( [
    'show_option_all' => 'すべてのカテゴリー',
    'taxonomy'       => $taxonomy,
    'name'           => $taxonomy,
    'orderby'        => 'name',
    'selected'       => isset( $_GET[$taxonomy] ) ? $_GET[$taxonomy] : '',
    'hierarchical'   => true,
    'depth'          => 0,
    'show_count'     => false,
    'hide_empty'     => false,
  ] );
} );

// プルダウンで選択したカテゴリーで、一覧を絞り込む
add_filter( 'parse_query', function( $query ) {
  global $pagenow;

  $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
  $taxonomy  = 'faq-category';

  if ( $pagenow === 'edit.php'
    && $post_type === 'faq'
    && ! empty( $query->query_vars[$taxonomy] )
    && $query->query_vars[$taxonomy] != '0'
  ) {
    $term = get_term_by( 'id', $query->query_vars[$taxonomy], $taxonomy );
    if ( $term ) {
        $query->query_vars[$taxonomy] = $term->slug;
    }
  }

  return $query;
} );

