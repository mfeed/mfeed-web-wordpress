<?php get_header(); ?>
<?php
// -----------------------------
// FAQ用：親カテゴリ名＆番号を計算
// -----------------------------
$faq_parent_name = '';
$faq_number      = '';

$terms = get_the_terms( get_the_ID(), 'faq-category' );

if ( $terms && ! is_wp_error( $terms ) ) {

  // 子ターム優先で1つ選ぶ (親・子両方付いているとき用)
  $current_term = null;
  foreach ( $terms as $t ) {
    if ( $t->parent ) {
      $current_term = $t;
      break;
    }
  }
  if ( ! $current_term ) {
    $current_term = reset( $terms ); // なければ先頭
  }

  // トップレベルの親タームを取得 (business / person など)
  $top_term = $current_term;
  while ( $top_term->parent ) {
    $top_term = get_term( $top_term->parent, 'faq-category' );
  }

  // ページタイトル用の「親カテゴリ名」
  $faq_parent_name = $top_term ? $top_term->name : '';

  // ここから番号ロジック
  $post_id = get_the_ID();

  // 親が business の場合 → 「親カテゴリ番号-当該記事が何番目か」 (例：1-09)
  if ( $top_term && $top_term->slug === 'business' ) {

    // business 配下の子カテゴリ一覧を、管理画面の並び順どおりに取得
    $business_children = get_terms( [
      'taxonomy'   => 'faq-category',
      'hide_empty' => false,
      'parent'     => $top_term->term_id,
      'orderby'    => 'term_order',
      'order'      => 'ASC',
    ] );

    $cat_index = 0;
    $this_cat_index = 0;

    if ( $business_children && ! is_wp_error( $business_children ) ) {
      foreach ( $business_children as $child ) {
        $cat_index++;
        if ( $child->term_id === $current_term->term_id ) {
          $this_cat_index = $cat_index;
          break;
        }
      }
    }

    // 当該カテゴリ内での並び順 (Intuitive Custom Post Order の順)
    $faq_posts = get_posts( [
      'post_type'      => 'faq',
      'posts_per_page' => -1,
      'tax_query'      => [
        [
          'taxonomy' => 'faq-category',
          'field'    => 'term_id',
          'terms'    => $current_term->term_id,
        ],
      ],
      'orderby' => 'menu_order',
      'order'   => 'ASC',
    ] );

    $post_index = 0;

    if ( $faq_posts ) {
      foreach ( $faq_posts as $p ) {
        $post_index++;
        if ( $p->ID === $post_id ) {
          break;
        }
      }
    }

    // 1-09 形式に整形
    if ( $this_cat_index && $post_index ) {
      $faq_number = sprintf( '%d-%02d', $this_cat_index, $post_index );
    }

  // 親が person の場合 → 「当該記事が何番目か」だけ (例：09)
  } elseif ( $top_term && $top_term->slug === 'person' ) {

    // person 配下のターム (current_term) 内での並び順
    $person_posts = get_posts( [
      'post_type'      => 'faq',
      'posts_per_page' => -1,
      'tax_query'      => [
        [
          'taxonomy'         => 'faq-category',
          'field'            => 'term_id',
          'terms'            => $current_term->term_id,
          'include_children' => false,
        ],
      ],
      'orderby' => 'menu_order',
      'order'   => 'ASC',
    ] );

    $post_index = 0;

    if ( $person_posts ) {
      foreach ( $person_posts as $p ) {
        $post_index++;
        if ( $p->ID === $post_id ) {
          break;
        }
      }
    }

    if ( $post_index ) {
      $faq_number = sprintf( '%02d', $post_index ); // 01,02,... 形式
    }
  }
}
?>
<main class="l-main">
  <div class="c-pageTitle">
    <div class="c-pageTitle__inner c-inner">
      <h1 class="c-pageTitle__title">
        <small>IPoE接続サービス transix (VNE) </small>
        <strong>よくあるご質問 (<?php echo esc_html( $faq_parent_name ); ?>) </strong>
      </h1>
    </div>
  </div>
  <?php include('_include/breadcrumbs.php'); ?>
  <section class="l-main__section">
    <div class="c-inner">
      <h2 class="c-headingUnderline --small"><span class="c-faqNumWrap"><span class="c-faqNum"><?php echo esc_html( $faq_number ); ?></span><?php the_title(); ?></span></h2>
      <div class="c-markDown">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php
          // コンテンツが空かどうかをチェック
          $content = get_the_content();
          if ( !empty( $content ) ) :
            the_content();
          else : ?>
            <p class="u-txt">
              このページはただいま準備中です。
            </p>
          <?php endif; ?>
        <?php endwhile; ?>
      <?php endif; ?>
      </div>
    </div>
  </section>
  <?php include('_include/faq/helpful.php'); ?>
  <section class="l-main__section">
    <div class="c-inner">
      <div class="c-btnWrap">
        <a href="<?php echo home_url('faq'); ?>" class="c-btn --more"><span>一覧に戻る</span></a>
      </div>
    </div>
  </section>
  <?php include('_include/cta.php'); ?>
</main>
<!-- .l-main -->
<?php get_footer(); ?>
