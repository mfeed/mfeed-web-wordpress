<?php

/*======================
All-in-One WP Migration
======================*/
// 一部フォルダ除外
add_filter(
  'ai1wm_exclude_themes_from_export',
  function ( $exclude_filters ) {
      $current_theme = get_template(); // 現在有効なテーマのディレクトリ名を取得
      $exclude_filters = array(
          $current_theme . '/node_modules',
          $current_theme . '/_src',
          $current_theme . '/.git',
          $current_theme . '/.vscode',
      );
      return $exclude_filters;
  }
);

/*======================
contact-form-7
======================*/
// 自動整形無効
add_filter('wpcf7_autop_or_not', '__return_false');
