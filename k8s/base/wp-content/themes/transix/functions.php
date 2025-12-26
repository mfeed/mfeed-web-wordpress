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
