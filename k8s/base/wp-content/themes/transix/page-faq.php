<?php
/*
Template Name: FAQページ用
Template Post Type: page
*/
; ?>
<?php get_header(); ?>
<main class="l-main">
  <?php include('_include/pageTitle.php'); ?>
  <?php include('_include/breadcrumbs.php'); ?>
  <?php if ( get_field('faq_pickup') ) : ?>
  <section class="l-main__section">
    <div class="c-inner">
      <div class="c-pickUp">
        <h2 class="c-pickUp__title">Pick Up</h2>
        <div class="c-pickUp__contents">
          <?php the_field('faq_pickup'); ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <section class="l-main__section --gray">
    <div class="c-inner">
      <div class="p-keyword">
        <h2 class="c-headingUnderline">キーワードから探す</h2>
        <?php include('_include/faq/searchBox.php'); ?>
      </div>
    </div>
  </section>
  <?php include('_include/faq/searchResult.php'); ?>
  <?php include('_include/faq/category.php'); ?>
  <?php include('_include/faq/list.php'); ?>
  <?php include('_include/cta.php'); ?>
</main>
<!-- .l-main -->
<?php get_footer(); ?>
