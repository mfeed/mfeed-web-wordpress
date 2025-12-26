<?php get_header('english'); ?>

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

   <?php get_template_part('template/en/sidebar-en')?>

    <!-- メイン：english_press アーカイブ -->
    <main class="col-md-9 blog-main">

      <section class="archives-wrap font-plq">
        <div class="archives ml-md-5">

          <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>

              <article <?php post_class( 'archive-article archive-type-post' ); ?>>
                <header class="archive-article-header">
                  <div class="d-flex justify-content-between align-items-end">

                    <div class="category">
                      <span class="mr-2">
                        <i class="fas fa-newspaper"></i>
                      </span>
                      <div class="article-category d-inline">
                        <?php
                        // カテゴリがあれば先頭カテゴリ、なければ PRESS 固定
                        $cats = get_the_category();
                        if ( ! empty( $cats ) ) {
                          echo esc_html( $cats[0]->name );
                        } else {
                          echo 'PRESS';
                        }
                        ?>
                      </div>
                    </div>

                    <div class="article-datetime text-right">
                      <?php $timestamp = get_post_time('U', true); ?>
                      <a href="<?php the_permalink(); ?>" class="archive-article-date">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished" class="align-bottom">
                          <?php  echo date('F j, Y', $timestamp); // October 1, 2025 形式 ?>
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

            <?php the_posts_pagination(); ?>

          <?php else : ?>
            <p>No press releases found.</p>
          <?php endif; ?>

        </div><!-- /.archives -->

      </section>

    </main>
  </div><!-- row -->
</div><!-- container -->

<?php get_footer('english'); ?>
