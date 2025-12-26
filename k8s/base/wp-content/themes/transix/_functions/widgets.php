<?php

/*======================
ウィジェットを外観に表示
======================*/
//functions.php
function my_theme_widgets_init() {
  register_sidebar(array(
    'name' => '【共通】ヘッダーの下',
    'id' => 'header-under',
    'before_widget' => '<section id="%1$s" class="wpWidget-headerUnder">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-headerUnder__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【共通】フッターの上',
    'id' => 'footer-over',
    'before_widget' => '<section id="%1$s" class="wpWidget-footerOver">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-footerOver__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【共通】フッターの中身',
    'id' => 'footer-inner',
    'before_widget' => '<section id="%1$s" class="wpWidget-footerInner">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-footerInner__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【共通】スマホナビ内',
    'id' => 'sp-contents',
    'before_widget' => '<section id="%1$s" class="wpWidget-spContents">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-spContents__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【トップ】キービジュアル',
    'id' => 'kv',
    'before_widget' => '<section id="%1$s" class="wpWidget-kv">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-kv__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【トップ】コンテンツ上部',
    'id' => 'contents-over',
    'before_widget' => '<section id="%1$s" class="wpWidget-contentsOver">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-contentsOver__title">',
    'after_title'  => '</h2>',
  ));
  register_sidebar(array(
    'name' => '【トップ】コンテンツ下部',
    'id' => 'contents-under',
    'before_widget' => '<section id="%1$s" class="wpWidget-contentsUnder">',
    'after_widget' => '</section>',
    'before_title' => '<h2 class="wpWidget-contentsUnder__title">',
    'after_title'  => '</h2>',
  ));
}
// add_action('widgets_init', 'my_theme_widgets_init');
