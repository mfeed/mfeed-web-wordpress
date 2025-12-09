<?php get_header(); ?>

  <?php 
  if(have_posts()){
    while(have_posts()){
      the_post();
        $parent_id = $post->post_parent; // 親IDを取得
        $parent = get_post( $parent_id ); // 親ページの情報取得
      if ( $parent && $parent->post_name === 'rpki' ){
        get_template_part('template/rpki-header');
      }elseif($parent && $parent->post_name === 'ntp'){
        get_template_part('template/ntp-header');
      }elseif($parent && $parent->post_name === 'about'){
        get_template_part('template/about-header');
      }elseif($parent && $parent->post_name === 'recruitment'){
        get_template_part('template/recruit-header');
      }
        ?>
        


    <?php
      the_content();
    }
  }
  ?>
<?php get_footer(); ?>