<?php
  // スラッグが 'faq' の固定ページを取得
  $faq_page = get_page_by_path( 'faq' );
  $faq_url  = $faq_page ? get_permalink( $faq_page->ID ) . '#faqSearchResult' : home_url( '/' );
?>
<form class="c-searchBox" method="get" action="<?php echo esc_url( $faq_url ); ?>">
  <div class="c-searchBox__input--large">
    <input type="search" name="faq_s" id="search_faq" placeholder="調べたいキーワードを入力してください" value="<?php echo isset( $_GET['faq_s'] ) ? esc_attr( wp_unslash( $_GET['faq_s'] ) ) : ''; ?>">
    <button type="submit" class="c-searchBox__submit">
      検索
    </button>
  </div>
</form>
