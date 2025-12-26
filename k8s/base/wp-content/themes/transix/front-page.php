<?php get_header(); ?>
  <main class="l-main">
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
    <?php include("_include/cta.php"); ?>
  </main>
<?php get_footer(); ?>
