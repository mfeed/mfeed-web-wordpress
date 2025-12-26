<?php get_header(); ?>
<main class="l-main">
  <section class="l-main__section">
    <div class="c-pageTitle">
      <div class="c-pageTitle__inner c-inner">
        <h1 class="c-pageTitle__title">
          <small>404</small>
          <strong>ページが見つかりません</strong>
        </h1>
      </div>
    </div>
    <div class="c-breadcrumbs">
      <ul class="c-breadcrumbs__list c-inner">
        <li><a href="<?php echo home_url(); ?>">TOP</a></li>
        <li>ページが見つかりません</li>
      </ul>
    </div>
    <div class="c-inner">
      <p class="u-txt" style="padding: 2lh 0 3lh;text-align: center;">
        アクセスしようとしたページは、変更または削除されたか、現在利用できない可能性があります。
      </p>
      <div class="c-btnWrap">
        <a href="<?php echo home_url(); ?>" class="c-btn --more"><span>トップページへ戻る</span></a>
      </div>
    </div>
  </section>
</main>
<!-- .l-main -->
<?php get_footer(); ?>
