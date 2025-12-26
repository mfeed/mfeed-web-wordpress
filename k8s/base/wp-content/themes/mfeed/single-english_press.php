<?php get_header('english'); ?>
<div class="container container-large">
  <div class="row no-gutters">
    <?php get_template_part('template/en/sidebar-en'); ?>
    <main class="col-md-9 blog-main">
      <article id="post-2025-10-01" class="article article-type-post" itemscope="" itemprop="blogPost">
  <div class="position-absolute mt-2">
    <img src="<?php echo get_template_directory_uri(); ?>/images/rect.svg" class="ml-3">
  </div>
  <header class="article-header mt-5">
    <h2 class="article-title pt-1 mb-4" itemprop="name">
      <?php the_title(); ?>
    </h2>
  </header>
    <div class="article-meta">
      <i class="mr-2 fas fa-calendar"></i>
      <div class="article-datetime text-right">
<?php
$timestamp = get_post_time('U', true);
$day_link  = home_url( '/en/' . get_the_time('Y/m/d/') ); // /en/2025/01/01/
?>
<a href="<?php echo esc_url( $day_link ); ?>" class="article-date">
  <time datetime="<?php echo esc_attr( get_the_time('c') ); ?>">
    <?php echo date('F j, Y', $timestamp); ?>
  </time>
</a>
</div>
    <span class="mr-2">
  <i class="fas fa-newspaper"></i>
</span>
  <div class="article-category d-inline">
 <?php $cat = get_the_terms( $post->ID, 'category' ); echo $cat[0]->name;?>
  </div>
    </div>
    <div class="article-entry ml-md-5" itemprop="articleBody">
        <?php 
        if(have_posts()):while(have_posts()):the_post();
        the_content(); 
        endwhile;endif;
        ?>
    </div>
    <footer class="article-footer">
    </footer>
</article>
    </main>
  </div><!-- row -->
</div><!-- container -->
<?php get_footer('english'); ?>