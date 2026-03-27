<?php get_header(); ?>
<main class="l-main">
  <div class="c-pageTitle">
    <div class="c-pageTitle__inner c-inner">
      <h1 class="c-pageTitle__title">
        <strong>検索結果</strong>
      </h1>
    </div>
  </div>
  <div class="c-breadcrumbs">
    <ul class="c-breadcrumbs__list c-inner">
      <li><a href="<?php echo home_url(); ?>">TOP</a></li>
      <li>「<?php echo esc_html( get_search_query() ); ?>」の検索結果</li>
    </ul>
  </div>
  <?php if ( have_posts() ) : ?>
    <?php
      global $wp_query;

      // ページ番号・表示件数・開始/終了番号を計算
      $paged          = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
      $posts_per_page = (int) get_query_var( 'posts_per_page', get_option( 'posts_per_page' ) );
      $total          = (int) $wp_query->found_posts;
      $start_num      = ( $posts_per_page * ( $paged - 1 ) ) + 1;
      $end_num        = min( $total, $posts_per_page * $paged );
    ?>
    <section class="l-main__section">
      <div class="c-inner">
        <div class="p-searchResult">
          <div class="p-searchResult__contents">
            <div class="p-searchResult__count">
              <?php echo esc_html( $total ); ?>件中 <?php echo esc_html( $start_num ); ?>〜<?php echo esc_html( $end_num ); ?>件を表示
            </div>
            <!-- 検索結果リスト -->
            <ul class="c-searchResultList">
              <?php
              $search_keyword = get_search_query();
              while ( have_posts() ) :
                the_post();

                // 抜粋生成
                $excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 200, '…' );

                // キーワードをハイライト (タイトル・本文)
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
            <!-- ページネーション -->
            <div class="c-pager">
              <?php
              the_posts_pagination(
                array(
                  'mid_size'           => 2,
                  'prev_text'          => '&lt;',
                  'next_text'          => '&gt;',
                  'screen_reader_text' => '',
                )
              );
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php else : ?>
    <!-- ヒットなしの場合 -->
    <section class="l-main__section">
      <div class="c-inner">
        <p>「<?php echo esc_html( get_search_query() ); ?>」に一致するページは見つかりませんでした。<br>キーワードを変えて再度検索してみてください。</p>
      </div>
    </section>
  <?php endif; ?>
  <?php include('_include/cta.php'); ?>
</main>
<?php get_footer(); ?>
