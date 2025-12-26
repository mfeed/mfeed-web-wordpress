<sidebar class="col-md-3 blog-sidebar">
  <div class="mf-menu border border-bottom-0">
    <div class="sidebar-module sidebar-module-inset slide-list p-0">

      <?php
      global $post;

      $heading_title   = 'English';
      $items           = array(); // サイドバーに表示するページたち
      $current_id      = $post ? $post->ID : 0;

      if ( $post && get_post_type( $post ) === 'english_page' ) {

        // まず親ページを取得
        $parent_id = wp_get_post_parent_id( $current_id );

        if ( $parent_id ) {
          // ① 子ページの場合：親タイトル＋兄弟一覧
          $heading_title = get_the_title( $parent_id );

          $items = get_pages( array(
            'post_type'   => 'english_page',
            'parent'      => $parent_id,
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
          ) );

        } else {
          // 親がない（トップレベル）
          // ② 子ページがあれば：自分タイトル＋子一覧
          $children = get_pages( array(
            'post_type'   => 'english_page',
            'parent'      => $current_id,
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
          ) );

          if ( ! empty( $children ) ) {
            $heading_title = get_the_title( $current_id );
            $items         = $children;

          } else {
            // ③ 親も子もない単独ページ：自分だけ表示
            $heading_title = get_the_title( $current_id );
            $items         = array( $post );
          }
        }
      }
      ?>

      <div class="sidebar-head red d-flex align-items-end justify-content-between">
        <h5 class="sidebar-headline mx-3 mb-2 text-nowrap font-plq">
          <?php echo esc_html( $heading_title ); ?>
        </h5>
        <i class="trapezoid"></i>
      </div>

      <ul class="sidebar-module-list pl-0">
        <?php
        if ( ! empty( $items ) ) :
          foreach ( $items as $item ) :
            $item_id      = $item->ID;
            $active_class = ( $item_id === $current_id ) ? ' active' : '';
            ?>
            <a class="border-bottom m-0 d-flex justify-content-between sidebar-cell<?php echo esc_attr( $active_class ); ?>"
               href="<?php echo esc_url( get_permalink( $item_id ) ); ?>">
              <p class="mb-0 pl-4 text-color">
                <?php echo esc_html( get_the_title( $item_id ) ); ?>
              </p>
              <i class="fas fa-chevron-right mr-3 text-color"></i>
            </a>
          <?php
          endforeach;
        endif;
        ?>
      </ul>

    </div>
  </div>
</sidebar>
