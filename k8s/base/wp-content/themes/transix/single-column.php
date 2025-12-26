<?php get_header(); ?>
<main class="l-main">
  <div class="c-columnTitle">
    <div class="c-columnTitle__inner c-inner">
      <div class="c-columnTitle__contents">
        <h1 class="c-headingEnJa">
          <span class="c-headingEnJa__en --ja">VNEの先駆者としての実績を持つ、インターネットマルチフィードが解説</span>
          <span class="c-headingEnJa__ja">
            <strong><?php the_title(); ?></strong>
          </span>
        </h1>
      </div>
      <picture class="c-columnTitle__img">
        <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/column.webp" alt="">
      </picture>
    </div>
  </div>
  <section class="l-main__section --lightgray --column">
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
    <div class="c-btnWrap">
      <a href="<?php echo home_url(); ?>" class="c-btn --more"><span>トップページへ戻る</span></a>
    </div>
  </section>
  <?php include('_include/cta.php'); ?>
</main>
<!-- .l-main -->
<?php get_footer(); ?>
