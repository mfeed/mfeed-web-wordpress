<main class="col-md-9 blog-main">
      <article id="post-2025-10-01" class="article article-type-post" itemscope="" itemprop="blogPost">

  
  <div class="position-absolute mt-2">
    <img src="<?php echo get_template_directory_uri();?>/images/rect.svg" class="ml-3">
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
$day_link = get_day_link( get_the_time('Y'), get_the_time('m'), get_the_time('d') );
?>
<a href="<?php echo esc_url( $day_link ); ?>" class="article-date">
  <time datetime="<?php echo get_the_time('c'); ?>">
    <?php echo get_the_time('Y年m月d日'); ?>
  </time>
</a>

</div>

      
      <span class="mr-2">

  

  <i class="fas fa-newspaper"></i>


</span>
      
  <div class="article-category d-inline">
    

    <?php $cat = get_the_category(); echo $cat[0]->name;?>
  </div>


    </div>
  

    <div class="article-entry ml-md-5" itemprop="articleBody">
      
        <?php the_content(); ?>
      
    </div>

    
      

    

    <footer class="article-footer">
      
      

    </footer>
  
    
    
  
</article>



    </main>