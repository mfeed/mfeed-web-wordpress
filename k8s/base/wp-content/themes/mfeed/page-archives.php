<?php get_header(); ?>

<header class="article-header-news py-4 d-flex">
  <div class="container align-self-center">
    <div class="row">
      <div class="col-md-5 align-self-center text-center text-md-left pr-md-5">
        <p class="service-logo-wrapper display-4 font-weight-normal">Press Releases</p>
      </div>
    </div><!-- row -->
  </div>
</header>










<div class="container container-large">

  <div class="row no-gutters">
    <?php get_sidebar('archive');?>
    <main class="col-md-9 blog-main">
    <section class="archives-wrap font-plq">
      <div class="archive-year-wrap mb-5">
        <a href="/archives/2025" class="archive-year"></a>
      </div>
      <div class="archives ml-md-5">
<?php
// URL から archive_year を取得（/archives/2024/ → 2024）
$year = get_query_var('archive_year');

// 何も指定がなければ今年
if (empty($year)) {
  $year = (int) current_time('Y');
}

$args = array(
  'post_type'      => 'post',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'date_query'     => array(
    array(
      'year' => (int) $year,
    ),
  ),
);

$year_query = new WP_Query($args);
?>


<?php if ($year_query->have_posts()) : ?>
  <?php while ($year_query->have_posts()) : $year_query->the_post(); ?>

    <article class="archive-article archive-type-post">
      <header class="archive-article-header">
        <div class="d-flex justify-content-between align-items-end">

          <div class="category">
            <span class="mr-2">
              <i class="fas fa-newspaper"></i>
            </span>
            <div class="article-category d-inline">
              <?php
                $cats = get_the_category();
                if (!empty($cats)) {
                  echo esc_html($cats[0]->name);
                } else {
                  echo 'PRESS'; // 固定にしたいならここで
                }
              ?>
            </div>
          </div>

          <div class="article-datetime text-right">
            <a href="<?php the_permalink(); ?>" class="archive-article-date">
              <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" itemprop="datePublished" class="align-bottom">
                <?php echo esc_html(get_the_date('Y年n月j日')); ?>
              </time>
            </a>
          </div>

        </div>

        <h2 itemprop="name">
          <a class="archive-article-title font-weight-normal" href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
          </a>
        </h2>
      </header>
    </article>

  <?php endwhile; ?>
  <?php wp_reset_postdata(); ?>

<?php else : ?>
  <p>この年の記事はありません。</p>
<?php endif; ?>

        
      </div>
    </section>


    </main>
  </div><!-- row -->

</div><!-- container -->


<?php get_footer(); ?>