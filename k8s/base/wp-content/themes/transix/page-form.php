<?php
/*
Template Name: メールフォームページ用
Template Post Type: page
*/
; ?>
<?php get_header('form'); ?>
<main class="l-main">
  <div class="c-pageTitle --small">
    <div class="c-pageTitle__inner c-inner">
      <h1 class="c-pageTitle__title">
        <small>IPoE接続サービス transix（VNE）</small>
        <strong>資料ダウンロード</strong>
      </h1>
    </div>
  </div>
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <?php
      // コンテンツが空かどうかをチェック
      $content = get_the_content();
      if ( !empty( $content ) ) :
        the_content();
      else : ?>
        <p class="u-txt">
          このページはただいま準備中です。
        </p>
      <?php endif; ?>
    <?php endwhile; ?>
  <?php endif; ?>
</main>
<!-- .l-main -->
<?php get_footer('form'); ?>
