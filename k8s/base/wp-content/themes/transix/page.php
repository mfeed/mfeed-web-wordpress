<?php
/*
Template Name: 固定ページ (汎用)
Template Post Type: page
*/
; ?>
<?php get_header(); ?>
<main class="l-main">
  <?php include('_include/pageTitle.php'); ?>
  <?php include('_include/breadcrumbs.php'); ?>
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
  <?php if ( is_page('features') ): ?>
    <section class="l-main__section--cta">
      <div class="p-ctaHead">
        <div class="p-ctaHead__inner c-inner">
          <div class="p-ctaHead__img">
            <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/cta_img.webp" alt="">
          </div>
          <strong class="p-ctaHead__txt">
            サービス詳細や料金など、<span class="u-dib">詳細については</span><br>資料ダウンロードもしくは<span class="u-dib">お問い合わせをお願いします</span>
          </strong>
        </div>
      </div>
      <?php include('_include/cta.php'); ?>
    </section>
  <?php else: ?>
    <?php include('_include/cta.php'); ?>
  <?php endif; ?>
</main>
<!-- .l-main -->
<?php get_footer(); ?>
