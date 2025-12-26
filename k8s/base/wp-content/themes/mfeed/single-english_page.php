  <?php 
  get_header('english');
  if(have_posts()){
    while(have_posts()){
      the_post();
        $parent_id = $post->post_parent; // 親IDを取得
        $parent = get_post( $parent_id ); // 親ページの情報取得
        get_template_part('template/en/child-header');
         get_template_part('template/en/page-content');
      
    }
  }

  get_footer('english');
  ?>
