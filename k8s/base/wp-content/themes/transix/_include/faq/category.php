<section class="l-main__section">
  <div class="c-inner">
    <h2 class="c-headingUnderline">カテゴリーから探す</h2>

    <?php
    // 親タームを取得 (ISP事業者さまの方 / 個人のお客さま…)
    $faq_parents = get_terms([
      'taxonomy'   => 'faq-category', // タクソノミー名
      'hide_empty' => false,
      'parent'     => 0,
      'orderby'    => 'term_order',
      'order'      => 'ASC',
    ]);

    if ( ! is_wp_error( $faq_parents ) && ! empty( $faq_parents ) ) :
      foreach ( $faq_parents as $parent ) :

        // 親タームのスラッグ (business / personなど)
        $parent_slug = $parent->slug;

        // 親ごとに ul のバリエーションを変えたい場合
        $ul_mod = ( $parent_slug === 'person' ) ? '--free' : '--4column';
        ?>
        <dl class="c-pageLinkCat">
          <dt class="c-pageLinkCat__label --<?php echo esc_attr( $parent_slug ); ?>">
            <?php echo esc_html( $parent->name ); ?>
          </dt>

          <dd class="c-pageLinkCat__body">
            <ul class="c-pageLink <?php echo esc_attr( $ul_mod ); ?>">
              <?php
              // 子ターム (サービス全般 / 手続き …) を取得
              $child_terms = get_terms([
                'taxonomy'   => 'faq-category',
                'hide_empty' => false,
                'parent'     => $parent->term_id,
                'orderby'    => 'term_order',
                'order'      => 'ASC',
              ]);

              if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) :
                foreach ( $child_terms as $child ) : ?>
                  <li class="c-pageLink__item">
                    <a href="#<?php echo esc_attr( $child->slug ); ?>" class="c-pageLink__link">
                      <small><?php echo esc_html( $child->name ); ?></small>
                    </a>
                  </li>
                <?php endforeach;
              endif; ?>
            </ul>
          </dd>
        </dl>
      <?php
      endforeach;
    endif;
    ?>
  </div>
</section>
