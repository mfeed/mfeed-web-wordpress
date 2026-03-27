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
              <?php echo esc_html( $total ); ?>件中
              <?php echo esc_html( $start_num ); ?>〜<?php echo esc_html( $end_num ); ?>件を表示
            </div>

            <ul class="c-searchResultList">
              <?php while ( have_posts() ) : the_post(); ?>
                <li class="c-searchResultList__item">
                <a href="<?php echo esc_url( add_query_arg( 'highlight', get_search_query(), get_permalink() ) ); ?>" class="c-searchResultList__link">
                  <h2 class="c-searchResultList__title c-headingDot">
                    <span>
                      <?php
                        $title = get_the_title();
                        echo mfeed_mark_highlight_html( esc_html($title), get_search_query() );
                      ?>
                    </span>
                  </h2>

                  <div class="c-searchResultList__excerpt">
                    <?php
                      $q = get_search_query();


                      $excerpt = mfeed_context_excerpt_from_content( get_the_ID(), $q, 140, 320 );

                      echo mfeed_mark_highlight_html( $excerpt, $q );
                    ?>
                  </div>

                  </a>
                </li>
              <?php endwhile; ?>
            </ul>

          </div>

          <div class="p-searchResult__pager">
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
    <section class="l-main__section">
      <div class="c-inner">
        <p>
          「<?php echo esc_html( get_search_query() ); ?>」に一致するページは見つかりませんでした。<br>
          キーワードを変えて再度検索してみてください。
        </p>
      </div>
    </section>
  <?php endif; ?>

  <?php include('_include/cta.php'); ?>
</main>
<?php get_footer(); ?>