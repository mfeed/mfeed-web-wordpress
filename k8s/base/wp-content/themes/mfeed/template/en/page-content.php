<div class="container container-large">
    <div class="row no-gutters">

    <?php 
    $parent = get_post( $post->post_parent );
    $parent_name = $parent->post_name;
    if($parent_name === 'rpki'){
      get_template_part('template/en/sidebar-rpki');
    }else{
      get_template_part('template/en/sidebar');
    } ?>
      <main class="col-md-9 blog-main">
        <article id="page-" class="article article-type-page" itemscope="" itemprop="blogPost">

  
  <div class="position-absolute mt-2">
    <img src="<?php echo get_template_directory_uri();?>/images/rect.svg" class="ml-3">
  </div>
  <header class="article-header mt-5">
    <h2 class="article-title pt-1 mb-4" itemprop="name">
      <?php the_title(); ?>
    </h2>
  </header>
<div class="article-entry ml-md-5" itemprop="articleBody">
<?php the_content(); ?>
</div>
<footer class="article-footer"></footer>
</article>



      </main>
    </div><!-- row -->

  
</div><!-- container -->