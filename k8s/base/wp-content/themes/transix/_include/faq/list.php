<section class="l-main__section --lightgray">
  <div class="c-inner">

    <h2 id="isp" class="c-headingUnderline u-anchorLink">よくあるご質問一覧</h2>

    <?php
    // 親カテゴリ：ISP事業者の方（business）
    $parent_business = get_term_by( 'slug', 'business', 'faq-category' );
    // 親カテゴリ：個人のお客さま（person）
    $parent_person = get_term_by( 'slug', 'person', 'faq-category' );

    /**
     * ----------------------------
     * ISP事業者の方（business）
     * ----------------------------
     */
    if ( $parent_business && ! is_wp_error( $parent_business ) ) :

      // business 配下の子カテゴリ（サービス全般 / 手続き / …）
      $business_children = get_terms( [
        'taxonomy'   => 'faq-category',
        'hide_empty' => false,
        'parent'     => $parent_business->term_id,
        'orderby'    => 'term_order', // 並び替えプラグインの順
        'order'      => 'ASC',
      ] );

      $cat_index = 0; // 1,2,3,... のカテゴリ番号

      if ( ! empty( $business_children ) && ! is_wp_error( $business_children ) ) :
        foreach ( $business_children as $child_term ) :
          $cat_index++; // 1 からスタート

          // 見出し h3
          ?>
          <h3 id="<?php echo esc_attr( $child_term->slug ); ?>" class="c-headingLine u-anchorLink">
            <em><?php echo esc_html( $cat_index ); ?></em><?php echo esc_html( $child_term->name ); ?>
          </h3>

          <?php
          // 各カテゴリに属する FAQ 取得
          $faq_posts = get_posts( [
            'post_type'      => 'faq',
            'posts_per_page' => -1,
            'tax_query'      => [
              [
                'taxonomy' => 'faq-category',
                'field'    => 'term_id',
                'terms'    => $child_term->term_id,
              ],
            ],
            'orderby' => 'menu_order', // Intuitive Custom Post Order の順
            'order'   => 'ASC',
          ] );

          if ( $faq_posts ) :
            $post_index = 0; // 1-01 の「01」部分

            echo '<ul class="c-faqList">';

            foreach ( $faq_posts as $post ) :
              setup_postdata( $post );
              $post_index++;

              // 1-01 / 1-02 / ... 形式に整形
              $child_num = sprintf( '%02d', $post_index );
              $display_num = $cat_index . '-' . $child_num;
              ?>
              <li class="c-faqList__item">
                <div class="c-faqList__num"><?php echo esc_html( $display_num ); ?></div>
                <a href="<?php the_permalink(); ?>" class="c-faqList__link">
                  <?php the_title(); ?>
                </a>
              </li>
              <?php
            endforeach;

            echo '</ul>';
            wp_reset_postdata();
          else :
            echo '<div class="c-faqList"><p>準備中です</p></div>';
          endif; // $faq_posts

        endforeach;
      endif; // $business_children
    endif; // $parent_business

    /**
     * ----------------------------
     * 個人のお客さま（person）
     * ----------------------------
     */
    if ( $parent_person && ! is_wp_error( $parent_person ) ) :

      // person 配下の子カテゴリ（※1つでも複数でもOK）
      $person_children = get_terms( [
        'taxonomy'   => 'faq-category',
        'hide_empty' => false,
        'parent'     => $parent_person->term_id,
        'orderby'    => 'term_order',
        'order'      => 'ASC',
      ] );

      if ( ! empty( $person_children ) && ! is_wp_error( $person_children ) ) :

        foreach ( $person_children as $child_term ) :

          // 見出し h3（id を「小カテゴリーのスラッグ」にする）
          ?>
          <h3 id="<?php echo esc_attr( $child_term->slug ); ?>" class="c-headingLine u-anchorLink">
            <?php echo esc_html( $child_term->name ); ?>
          </h3>
          <?php

          // 子カテゴリに属する FAQ を取得
          $person_faq = get_posts( [
            'post_type'      => 'faq',
            'posts_per_page' => -1,
            'tax_query'      => [
              [
                'taxonomy'         => 'faq-category',
                'field'            => 'term_id',
                'terms'            => $child_term->term_id,
                'include_children' => false,
              ],
            ],
            'orderby' => 'menu_order',
            'order'   => 'ASC',
          ] );

          if ( $person_faq ) :
            $ind_index = 0;

            echo '<ul class="c-faqList">';

            foreach ( $person_faq as $post ) :
              setup_postdata( $post );
              $ind_index++;

              // 01, 02, ... の2桁番号
              $display_num = sprintf( '%02d', $ind_index );
              ?>
              <li class="c-faqList__item">
                <div class="c-faqList__num"><?php echo esc_html( $display_num ); ?></div>
                <a href="<?php the_permalink(); ?>" class="c-faqList__link">
                  <?php the_title(); ?>
                </a>
              </li>
              <?php
            endforeach;

            echo '</ul>';
            wp_reset_postdata();

          else :
            echo '<div class="c-faqList"><p>準備中です</p></div>';
          endif;

        endforeach;

      endif; // $person_children

    endif; // $parent_person
    ?>

  </div>
</section>
