<?php
// FAQ専用クエリパラメータ取得（URLが ?faq_s=サービス のようになる前提）
$keyword = isset( $_GET['faq_s'] ) ? sanitize_text_field( wp_unslash( $_GET['faq_s'] ) ) : '';

// キーワードがあるときだけ検索実行
if ( $keyword !== '' ) :

  $paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;

  $faq_query = new WP_Query( array(
    'post_type'      => 'faq',
    's'              => $keyword,
    'posts_per_page' => 10,
    'paged'          => $paged,
  ) );

  if ( $faq_query->have_posts() ) :

    $total          = (int) $faq_query->found_posts;
    $posts_per_page = (int) $faq_query->get( 'posts_per_page' );
    $start_num      = ( $posts_per_page * ( $paged - 1 ) ) + 1;
    $end_num        = min( $total, $posts_per_page * $paged );
?>
<section class="l-main__section">
  <div class="c-inner">
    <h2 id="faqSearchResult" class="c-headingEnJa u-anchorLink">
      <span class="c-headingEnJa__ja --center">
        <strong>検索結果</strong>
      </span>
    </h2>
    <div class="p-searchResult">
      <div class="p-searchResult__contents">
        <div class="p-searchResult__count">
          <?php echo esc_html( $total ); ?>件中
          <?php echo esc_html( $start_num ); ?>〜<?php echo esc_html( $end_num ); ?>件を表示
        </div>

        <!-- 検索結果リスト -->
        <ul class="c-searchResultList">
          <?php
          $search_keyword = $keyword;

          while ( $faq_query->have_posts() ) :
            $faq_query->the_post();

            // 抜粋生成
            $excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 200, '…' );

            // キーワードをハイライト（タイトル・本文）
            if ( $search_keyword ) {
              $pattern = '/' . preg_quote( $search_keyword, '/' ) . '/iu';
              $replace = '<mark class="c-searchResultList__highlight">$0</mark>';

              $title_highlight   = preg_replace( $pattern, $replace, esc_html( get_the_title() ) );
              $excerpt_highlight = preg_replace( $pattern, $replace, esc_html( $excerpt ) );
            } else {
              $title_highlight   = esc_html( get_the_title() );
              $excerpt_highlight = esc_html( $excerpt );
            }
          ?>
            <li class="c-searchResultList__item">
              <a href="<?php the_permalink(); ?>" class="c-searchResultList__link">
                <?php if ($title_highlight): ?>
                <h2 class="c-searchResultList__title c-headingDot">
                  <span><?php echo wp_kses_post( $title_highlight ); ?></span>
                </h2>
                <?php endif; ?>
                <?php if ($excerpt_highlight): ?>
                <p class="c-searchResultList__excerpt">
                  <?php echo wp_kses_post( $excerpt_highlight ); ?>
                </p>
                <?php endif; ?>
              </a>
            </li>
          <?php
          endwhile; ?>
        </ul>
      </div>

      <div class="p-searchResult__pager">
        <div class="c-pager">
          <?php
          // 固定ページなので paged は GET から取得している前提
          // $paged, $keyword, $faq_query はこのファイルの上の方で定義済みとする

          $base = esc_url_raw(
            add_query_arg(
              'paged',
              '%#%',
              get_permalink( get_queried_object_id() ) // /transix/faq/ のURL
            )
          );

          // ページリンクを配列で取得
          $links = paginate_links( array(
            'base'         => $base,           // /faq/?paged=%#%
            'format'       => '',              // baseに ?paged が入ってるので空でOK
            'current'      => $paged,
            'total'        => $faq_query->max_num_pages,
            'mid_size'     => 2,
            'prev_text'    => '&lt;',
            'next_text'    => '&gt;',
            'type'         => 'array',         // ★これがポイント：配列で受け取る
            'add_args'     => array(
              'faq_s' => $keyword,            // /faq/?faq_s=transix&paged=2
            ),
            'add_fragment' => '#faqSearchResult', // 2ページ目でも見出しまでスクロールさせたい場合
          ) );

          if ( ! empty( $links ) ) :
          ?>
            <nav class="navigation pagination" aria-label="FAQのページ送り">
              <h2 class="screen-reader-text">FAQナビゲーション</h2>
              <div class="nav-links">
                <?php
                // $links の中身は <span class="page-numbers">1</span> などのHTML
                foreach ( $links as $link ) {
                  echo $link;
                }
                ?>
              </div>
            </nav>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php else : ?>
<section class="l-main__section">
  <div class="c-inner">
    <h2 id="faqSearchResult" class="c-headingEnJa u-anchorLink">
      <span class="c-headingEnJa__ja --center">
        <strong>検索結果</strong>
      </span>
    </h2>
    <!-- ヒットなしの場合 -->
    <p>「<?php echo esc_html( $keyword ); ?>」に一致するページは見つかりませんでした。<br>キーワードを変えて再度検索してみてください。</p>
  </div>
</section>
<?php
  endif;
  wp_reset_postdata();
endif;
?>
