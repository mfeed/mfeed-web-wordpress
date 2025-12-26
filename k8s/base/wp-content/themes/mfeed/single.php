<?php get_header(); ?>
     <div class="container container-large">

    <div class="row no-gutters">
      <?php get_sidebar('archive');?>
  <?php 
  if(have_posts()){
    while(have_posts()){
      the_post();
      if(is_single())
      {
        get_template_part('template/post-content');
      }else{
      the_content();
      }
    
    }
  }
  ?>
      </div><!-- row -->

  </div><!-- container -->

<?php get_footer(); ?>