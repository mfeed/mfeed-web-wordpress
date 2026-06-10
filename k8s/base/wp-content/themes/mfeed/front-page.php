<?php get_header(); ?>
<?php

/*
if ( ! function_exists( 'jpnap_log' ) ) {
  function jpnap_log( $msg ) {
    $file = WP_CONTENT_DIR . '/jpnap-api-debug.log';
    $line = '[' . date( 'c' ) . '] ' . $msg . "\n";
    error_log( $line, 3, $file );
  }
}
*/

// 日付フォーマット
if ( ! function_exists( 'mfeed_format_ja_date' ) ) {
  function mfeed_format_ja_date( $iso ) {
    $t = strtotime( $iso );
    if ( ! $t ) {
      return esc_html( $iso );
    }
    return date( 'Y年n月j日', $t );
  }
}

// Use local WP DB: fetch posts by category

/*
// Original: fetch from external JPNAP API
$endpoint = 'http://www-jpnap-net-wordpress-svc/home/api/wp/v2/notice_ja/';

$response = wp_remote_get(
  $endpoint,
  [
    'timeout'     => 10,
    'redirection' => 5,
    'headers'     => [
      'Accept' => 'application/json',
    ],
  ]
);

if ( is_wp_error( $response ) ) {
  jpnap_log( 'jpnap api error (press): ' . $response->get_error_message() );
} else {
  $code = wp_remote_retrieve_response_code( $response );
  $body = wp_remote_retrieve_body( $response );

  jpnap_log( 'jpnap api status: ' . $code );
  jpnap_log( 'jpnap api body len: ' . strlen( $body ) );
  jpnap_log( 'jpnap api body head: ' . substr( $body, 0, 200 ) );

  file_put_contents( WP_CONTENT_DIR . '/jpnap-api-raw.json', $body );

  if ( $body === '' ) {
    jpnap_log( 'jpnap api body is empty, skip json_decode' );
    $data = [];
  } else {
    $data = json_decode( $body, true );
    $err  = json_last_error();
    jpnap_log( 'jpnap api json decode error: ' . json_last_error_msg() );

    if ( ! is_array( $data ) || empty( $data ) || $err !== JSON_ERROR_NONE ) {
      $data = [];
    }
  }

  if ( is_array( $data ) && ! empty( $data ) ) {

    if ( isset( $data['id'] ) ) {
      $data = [ $data ];
    }

    $press_cat_id  = 3; 
    $topics_cat_id = 6; 
    $max_press     = 6; 

    // ==========================
    // ☆ Press（カテゴリID=3）
    // ==========================
    $press_items = [];

    foreach ( $data as $post ) {
      $cid            = isset( $post['category_id'] ) ? (int) $post['category_id'] : 0;
      $cats = isset( $post['categories'] ) && is_array( $post['categories'] )
        ? array_map( 'intval', $post['categories'] )
        : [];

      $is_press =
        ( $cid === $press_cat_id ) ||
        in_array( $press_cat_id, $cats, true );

      if ( ! $is_press ) {
        continue;
      }

      $url = '';
      if ( ! empty( $post['id'] ) ) {
        $url = 'https://www.jpnap.net/news_detail.php?id=' . (int) $post['id'];
      }

      $press_items[] = [
        'date'  => ! empty( $post['date'] ) ? $post['date'] : '',
        'title' => ! empty( $post['title']['rendered'] ) ? wp_strip_all_tags( $post['title']['rendered'] ) : '',
        'url'   => $url,
      ];

      if ( count( $press_items ) >= $max_press ) {
        break;
      }
    }

    if ( empty( $press_items ) ) {
      $cnt = 0;
      foreach ( $data as $post ) {
        $press_items[] = [
          'date'  => ! empty( $post['date'] ) ? $post['date'] : '',
          'title' => ! empty( $post['title']['rendered'] ) ? wp_strip_all_tags( $post['title']['rendered'] ) : '',
          'url'   => ! empty( $post['link'] ) ? $post['link'] : '',
        ];
        $cnt++;
        if ( $cnt >= $max_press ) break;
      }
    }

    $jpnap_press = $press_items[0];

    // ==========================
    // ☆ Topics（カテゴリID=6）
    // ==========================
    $jpnap_topics_items = [];

    foreach ( $data as $post ) {

      $cid            = isset( $post['category_id'] ) ? (int) $post['category_id'] : 0;
      $cats = isset( $post['categories'] ) && is_array( $post['categories'] )
        ? array_map( 'intval', $post['categories'] )
        : [];

      $is_topics =
        ( $cid === $topics_cat_id ) ||
        in_array( $topics_cat_id, $cats, true );

      if ( ! $is_topics ) {
        continue;
      }

      $url = '';
      if ( ! empty( $post['id'] ) ) {
        $url = 'https://www.jpnap.net/news_detail.php?id=' . (int) $post['id'];
      }

      $jpnap_topics_items[] = [
        'date'  => ! empty( $post['date'] ) ? $post['date'] : '',
        'title' => ! empty( $post['title']['rendered'] ) ? wp_strip_all_tags( $post['title']['rendered'] ) : '',
        'url'   => $url,
      ];
    }

    if ( empty( $jpnap_topics_items ) ) {
      foreach ( $data as $post ) {
        $jpnap_topics_items[] = [
          'date'  => ! empty( $post['date'] ) ? $post['date'] : '',
          'title' => ! empty( $post['title']['rendered'] ) ? wp_strip_all_tags( $post['title']['rendered'] ) : '',
          'url'   => ! empty( $post['link'] ) ? $post['link'] : '',
        ];
      }
    }
  }
}
*/

$press_cat_id  = 2;
$max_press     = 6;

$press_items = array();
$press_posts = get_posts( array(
  'post_type'      => 'post',
  'cat'            => $press_cat_id,
  'posts_per_page' => $max_press,
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
) );

if ( ! empty( $press_posts ) ) {
  foreach ( $press_posts as $p ) {
    $press_items[] = array(
      'date'  => get_the_date( 'c', $p->ID ),
      'title' => get_the_title( $p->ID ),
      'url'   => get_permalink( $p->ID ),
    );
  }
}

//$jpnap_topics_items = array();
//$topic_posts = get_posts( array(
//  'post_type'      => 'post',
//  'cat'            => $topics_cat_id,
//  'posts_per_page' => 6,
//  'post_status'    => 'publish',
//  'orderby'        => 'date',
//  'order'          => 'DESC',
//) );
//
//if ( ! empty( $topic_posts ) ) {
//  foreach ( $topic_posts as $t ) {
//    $jpnap_topics_items[] = array(
//      'date'  => get_the_date( 'c', $t->ID ),
//      'title' => get_the_title( $t->ID ),
//      'url'   => get_permalink( $t->ID ),
//    );
//  }
//}
?>



    <section class="mfeed-cont-wrapper-introduction d-flex font-plq" data-section-name="introduction">
  <div class="container container-large align-self-center">
    <div class="row">
      <div class="col-12">
        <div class="slider single-item">
          <div class="col-lg-6">
            <h1 class="page-title mb-4">All <span class="mf-red">communication</span><br>flows through here</h1>
            <p class="h5 mb-4 font-weight-normal">「すべてのコミュニケーションはここを通る。」<br>私たちはそんな「場」と「ソリューション」を提供し、<br>革新的な「付加価値」を創造していきます。</p>
            <a class="btn btn-mfeed my-3 d-block d-md-inline-block" href="/about/">
              READ MORE
            </a>
          </div>
          <!--<div class="col-lg-6">
            <h1 class="page-title mb-4">ALL <span class="mf-red">communication</span><br>flows through here</h1>
            <p class="h5 mb-4">「すべてのコミュニケーションはここを通る。」<br>私たちはそんな「場」と「ソリューション」を提供し、革新的な「付加価値」を創造していきます。</p>
            <a class="btn btn-mfeed my-3 d-block d-md-inline-block" href="/about/">
              READ MORE
            </a>
          </div>
          <div class="col-lg-6">
            <h1 class="page-title mb-4">ALL <span class="mf-red">communication</span><br>flows through here</h1>
            <p class="h5 mb-4">「すべてのコミュニケーションはここを通る。」<br>私たちはそんな「場」と「ソリューション」を提供し、革新的な「付加価値」を創造していきます。</p>
            <a class="btn btn-mfeed my-3 d-block d-md-inline-block" href="/about/">
              READ MORE
            </a>
          </div> ※スライド追加する場合はここを使用する -->
        </div>
      </div>
    </div><!-- /.row -->
  </div>
</section><!-- //introduction -->

<section class="mfeed-cont-wrapper-service font-plq" data-section-name="service">
  <div class="service-crimson">
    <div class="container container-large py-5">
      <div class="row no-gutters">
        <div class="col-md-6 pr-md-5 pb-5 pb-md-0">
          <div class="inner-hook d-flex flex-column justify-content-between text-center">
            <svg class="service-logo-jpnap" viewbox="0 0 94.4 53.5" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <style>
      .service-logo-jpnap > .color1{ fill:#e50000; }
      .service-logo-jpnap > .color2{ fill:white; }
    </style>
  </defs>
  <g class="color1">
    <path d="M31.2,3.8c.4-2-1.5-3.4-2.9-3.7H26.6A26.6,26.6,0,0,0,9.1,46.6a1.7,1.7,0,0,1-.1-.7,2.9,2.9,0,0,1,3.2-3.2,2.7,2.7,0,0,1,2.9,2.4h0c.1,1.5.3,2.6,1.1,2.5,2.6,0,3-4.5,5.6-13.1S31.2,3.9,31.2,3.8Z"/>
  </g>
  <g class="color2">
    <path d="M44.7,42.5a22.2,22.2,0,0,1-5.1.6.7.7,0,0,1-.1-1h0a8.4,8.4,0,0,0,7.4-8.5c0-3.2-1.8-4.8-4.8-4.8-1.1,0-1.2.2-1.6,1.4L34.7,48.7a14.5,14.5,0,0,1-.6,1.9c0,.3-.1.6-.1.9s0,.7.6.5h.1A26.7,26.7,0,0,0,49.9,39.4,10.3,10.3,0,0,1,44.7,42.5Z"/>
    <path d="M27.8,53c.9-.4,1.3-1.3,2.2-4.3l5.2-16.3c.8-2.6.7-3.2-.7-3.4l-1.8-.3c-.3-.2-.1-.9.3-1a60,60,0,0,1,7.4-.5c3.8,0,6.6.6,8.5,1.9a6.4,6.4,0,0,1,2.8,5.6v.9a26.5,26.5,0,0,0-10.2-31,3.1,3.1,0,0,0-3.9,1.6c0,.2-5.8,18.1-5.8,18.1C29.4,32,26,49.9,14.5,50.3a27,27,0,0,0,12.1,2.9A2.5,2.5,0,0,0,27.8,53Z"/>
  </g>
  <g class="color2">
    <path d="M52.3,40.8c.5-1.5.7-2.3.5-2.7s-.4-.5-.7-.5l-.9-.2c-.2-.1-.1-.5.2-.6h4.7a14.5,14.5,0,0,0,1.6,4.7l1.8,4.4,1.4,3.2a5.6,5.6,0,0,0,.4-1.2l1.6-5c.5-1.7.9-3.5,1-4s-.1-1.2-1.1-1.3h-.7a.5.5,0,0,1,.1-.7H68a.9.9,0,0,1,0,.7h-.7a1.8,1.8,0,0,0-1.7,1.1,35.2,35.2,0,0,0-1.5,4.2l-1.8,5.9c-.7,2.2-1,3.4-1.4,4.5a2.2,2.2,0,0,1-1.2.2,37.2,37.2,0,0,0-1.8-4.6l-2.3-5.5c-.6-1.3-1.3-3.1-1.6-3.9a17.2,17.2,0,0,0-.7,2.1l-1.7,5.6c-.5,1.7-1,3.5-1.1,4s.1,1.2,1.2,1.3h.6a.4.4,0,0,1-.1.7H46.5a.5.5,0,0,1,0-.7h.6a2,2,0,0,0,1.8-1.1c.5-1.3,1-2.7,1.4-4.1Z"/>
    <path d="M70.3,46c-.7,0-.7-.1-.5-.4l2.7-4.1a12.7,12.7,0,0,0,1.2-1.8h.1c-.1.6,0,1-.1,1.5l-.2,4.2c0,.5-.1.6-.6.6Zm2.5,1.1c.6,0,.7.1.7.6V51c-.1,1.2-.2,1.3-1.2,1.5h-.7a.5.5,0,0,0-.1.6h6.5c.2-.1.2-.5.1-.7h-.6c-1.3-.1-1.4-.4-1.3-2.6s.1-6.4.2-9.6.1-3.2.1-3.6-.2-.3-.4-.3a6.2,6.2,0,0,1-2.2,1A10.1,10.1,0,0,1,72.2,40l-4,5.8c-.9,1.3-3.2,4.6-3.6,5s-1.3,1.6-2.3,1.7h-.6a.5.5,0,0,0,0,.7h5.5c.2-.1.2-.4.1-.7h-.8c-.5-.1-.7-.2-.7-.4a7.1,7.1,0,0,1,.8-1.5l1.7-2.8c.5-.7.5-.7,1.2-.7Z"/>
    <path d="M85.6,52.6c.1.3.1.6-.1.7H78.6a.4.4,0,0,1-.1-.6.1.1,0,0,1,.1-.1h.7c1.2-.1,1.4-.4,2.1-2.8l3.1-9.8c.5-1.6.5-1.9-.4-2.1h-1a.5.5,0,0,1,.1-.7l4.4-.2a8.2,8.2,0,0,1,5.1,1.1,3.5,3.5,0,0,1,1.7,3.3,4.9,4.9,0,0,1-4.2,4.7,10.3,10.3,0,0,1-3,.4.4.4,0,0,1-.1-.6h0a5.1,5.1,0,0,0,4.5-5.1,2.7,2.7,0,0,0-2.4-2.9h-.5c-.7,0-.7.2-1,.9L84.2,49.7c-.6,2.1-.7,2.7.6,2.8Z"/>
  </g>
</svg>

            <h3 class="service-subtitle text-white my-4">インターネットエクスチェンジサービス</h3>
            <p class="service-descripiton text-white my-4">大容量トラフィックの安定した交換を可能にするレイヤ2のインターネット相互接続(IX: Internet eXchange)サービス</p>
            <a href="https://www.jpnap.net" class="btn btn-secondary mx-auto">More Info...</a>
          </div>
        </div>
        <div class="col-md-6 pl-md-5 pt-5 pt-md-0 sector-style">
          <div class="inner-hook d-flex flex-column justify-content-between text-center">
            <svg class="service-logo-transix" viewbox="0 0 437.9 92.9" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <style>
      .service-logo-transix > .color1{fill:#231815;}
      .service-logo-transix > .color2{fill:#fff;}
      .service-logo-transix > .color3{fill:#e60019;}
    </style>
  </defs>
  <g class="color1">
    <path d="M49.1,31.6v9H27.3V57.8a5.5,5.5,0,0,0,2.5,4.9,10,10,0,0,0,6.7,1.8,34,34,0,0,0,12.6-2.4v9.5a56.9,56.9,0,0,1-17.4,2.6c-7.3,0-13-1.3-17.1-4A12.9,12.9,0,0,1,8.5,58.6v-18H0V38L25,18.5h2.3V31.6Z"/>
    <path d="M74.7,31.6v12h.2C80.4,35,86.7,30.7,94,30.7c3.4,0,7.6,1.3,12.5,3.8l-5.2,11.7c-4.7-2.1-8.4-3.1-11.2-3.1a16.3,16.3,0,0,0-11,5.6c-3,3.7-4.4,6-4.4,6.6V73.4H55.9V31.6Z"/>
    <path d="M169.6,63.1v7.3A42.9,42.9,0,0,1,152,74.2c-5.9,0-9.4-1.5-10.5-4.7a43.6,43.6,0,0,1-19.7,4.7A20.3,20.3,0,0,1,110,71.1c-3.2-2-4.9-4.4-4.9-7.1a11.3,11.3,0,0,1,5.1-8.8c3.4-2.2,13.5-5.1,30.4-8.7.5-4.6-3-7-10.3-7a38.9,38.9,0,0,0-22.1,7V35.8a67.6,67.6,0,0,1,25.9-5.1c16.9,0,25.3,4.3,25.3,12.8v19c0,2,1.1,3,3.1,3S166.5,64.7,169.6,63.1Zm-29,.2V53.1q-9.4,2.4-12.9,4.2c-2.3,1.2-3.5,2.8-3.5,4.7a3.5,3.5,0,0,0,1.8,3.1,6.1,6.1,0,0,0,4.3,1.3A18.1,18.1,0,0,0,140.6,63.3Z"/>
    <path d="M191.6,31.6v6A29.7,29.7,0,0,1,211,30.7c6.2,0,11.4,1.4,15.5,4.2a13.4,13.4,0,0,1,6.3,11.9V73.4H214V47.9q0-8.4-9.3-8.4c-4.5,0-8.8,2.4-13.1,7V73.4H172.8V31.6Z"/>
    <path d="M343.1,13.5c0-2,1-3.7,3-5.2a13.7,13.7,0,0,1,14.6,0c2,1.5,3,3.2,3,5.2a6,6,0,0,1-3,5.1,13.7,13.7,0,0,1-14.6,0A6,6,0,0,1,343.1,13.5Zm19.8,18.1V73.4H344.1V31.6Z"/>
    <path d="M436.1,31.6,412.5,52.1l25.4,21.3H416L402,60.5,388.4,73.4H367.2l24.4-21.3L367.2,31.6h22L402,43.7l12.2-12.1Z"/>
</g>
  <g class="color2">
  <circle cx="285.8" cy="46.4" r="46.4"/>
  </g>
  <g class="color3">
    <path d="M295.5,58c-2.1-1-8.8-2.7-20.4-5.2s-19.4-5.3-23.6-8.3-6.4-6.3-6.4-10c0-5.5,4.1-9.9,12.2-13.3s18.9-5.1,32.3-5.1,24.3,1.1,34.6,4a46.5,46.5,0,0,0-83,39.1c14.3,6.6,31.6,6.5,40.9,6.5,10.9,0,16.4-1.5,16.4-4.4Q298.5,59.5,295.5,58Z"/>
		<path d="M331,35.5c-14.1-7.2-31.5-8.2-42.5-8.2-8.2,0-12.2,1.5-12.2,4.6,0,.8.8,1.6,2.6,2.4s8.2,2.4,19.6,4.9,19.4,5.4,24.1,8.7,7.1,6.9,7.1,10.9c0,6.2-3.8,11-11.4,14.3s-18.4,5.1-32.5,5.1-25.2-1-37-3.9a46.4,46.4,0,0,0,83.5-28A44.3,44.3,0,0,0,331,35.5Z"/>
    </g>
</svg>

            <h3 class="service-subtitle text-white my-4">IPv6インターネット接続サービス</h3>
            <p class="service-descripiton text-white my-4">NTT東日本・西日本が提供するフレッツ光のインターネット(IPv6 IPoE)接続機能を活用した、事業者向けIPv6インターネット接続サービス</p>
            <a href="/transix/" class="btn btn-secondary mx-auto">More Info...</a>
          </div>
        </div>
      </div>
    </div><!-- /.row -->
  </div>
  <div class="service-white">
    <div class="container container-large py-5">
      <div class="row no-gutters">
        <div class="col-md-6 pr-md-5 pb-5 pb-md-0">
          <div class="inner-hook d-flex flex-column justify-content-between text-center">
            <svg class="service-logo-rpki" viewbox="0 0 74.912 22.844" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <style>
      .service-logo-rpki > .color1{ fill:url(#linear-gradient); }
    </style>
    <lineargradient id="linear-gradient" x1="0.5" x2="0.5" y2="1" gradientunits="objectBoundingBox">
      <stop offset="0" stop-color="#e60013"/>
      <stop offset="1" stop-color="#670009"/>
    </lineargradient>
  </defs>
  <g class="color1">
    <path d="M-20.94-3.906a8.3,8.3,0,0,0,1.578,1.922,2.692,2.692,0,0,0,1.781.641h.094V0h-.453A22.528,22.528,0,0,1-20.4-.109a5.3,5.3,0,0,1-1.633-.422,3.325,3.325,0,0,1-1.148-.875,11.465,11.465,0,0,1-1.023-1.469l-4.328-7.2H-31.19v6.594a2.586,2.586,0,0,0,.211,1.133,1.516,1.516,0,0,0,.57.648,2.21,2.21,0,0,0,.836.289,6.906,6.906,0,0,0,1.008.07h.422V0h-9.312V-1.344h.406a6.906,6.906,0,0,0,1.008-.07,2.21,2.21,0,0,0,.836-.289,1.516,1.516,0,0,0,.57-.648,2.586,2.586,0,0,0,.211-1.133V-19.328a2.586,2.586,0,0,0-.211-1.133,1.626,1.626,0,0,0-.57-.664,2.06,2.06,0,0,0-.836-.3,6.906,6.906,0,0,0-1.008-.07h-.406v-1.344h8.609q4.156,0,6.188,1.539a5.444,5.444,0,0,1,2.031,4.633,5.629,5.629,0,0,1-.414,2.242,5.576,5.576,0,0,1-1.086,1.664,6.353,6.353,0,0,1-1.516,1.172,9.969,9.969,0,0,1-1.687.75Zm-10.25-7.672h2.25a7.507,7.507,0,0,0,2.344-.312,3.425,3.425,0,0,0,1.5-.937,3.658,3.658,0,0,0,.8-1.562,8.8,8.8,0,0,0,.242-2.187,7.768,7.768,0,0,0-.266-2.18,3.278,3.278,0,0,0-.859-1.469,3.505,3.505,0,0,0-1.539-.828,8.874,8.874,0,0,0-2.3-.258H-31.19ZM-15.511,0V-1.344h.406a6.332,6.332,0,0,0,1.023-.078,2.076,2.076,0,0,0,.836-.32,1.6,1.6,0,0,0,.563-.7,2.979,2.979,0,0,0,.2-1.2V-19.328a2.586,2.586,0,0,0-.211-1.133,1.626,1.626,0,0,0-.57-.664,2.06,2.06,0,0,0-.836-.3A6.906,6.906,0,0,0-15.1-21.5h-.406v-1.344h8.984a11.853,11.853,0,0,1,3.5.461A6.517,6.517,0,0,1-.574-21.055a5.378,5.378,0,0,1,1.445,2.1,7.719,7.719,0,0,1,.477,2.8A8.452,8.452,0,0,1,.9-13.422,5.909,5.909,0,0,1-.558-11.1,7.249,7.249,0,0,1-3.214-9.492a11.731,11.731,0,0,1-4.016.6H-9.246v5.406a2.586,2.586,0,0,0,.211,1.133,1.516,1.516,0,0,0,.57.648,2.21,2.21,0,0,0,.836.289,6.906,6.906,0,0,0,1.008.07h1.063V0Zm6.266-10.406h1.7a8.848,8.848,0,0,0,2.484-.3A3.781,3.781,0,0,0-3.371-11.7a4.071,4.071,0,0,0,.961-1.75,9.544,9.544,0,0,0,.3-2.586,9.1,9.1,0,0,0-.266-2.336,3.922,3.922,0,0,0-.859-1.648,3.575,3.575,0,0,0-1.539-.977,7.236,7.236,0,0,0-2.3-.32H-9.246Zm26.913-7.359q.516-.594.859-1.055a6.3,6.3,0,0,0,.539-.836,3.983,3.983,0,0,0,.281-.656,1.777,1.777,0,0,0,.086-.516.55.55,0,0,0-.391-.555,3.555,3.555,0,0,0-1.187-.148v-1.312h7.078v1.313a2.723,2.723,0,0,0-1.055.211,4.676,4.676,0,0,0-1.016.594,9.2,9.2,0,0,0-1.047.938q-.539.555-1.164,1.258l-4.641,5.219,6.75,9.406a9.215,9.215,0,0,0,1.773,1.914,2.852,2.852,0,0,0,1.742.648h.063V0H26a22.71,22.71,0,0,1-2.641-.125,5.753,5.753,0,0,1-1.734-.453,3.989,3.989,0,0,1-1.227-.883,12.086,12.086,0,0,1-1.117-1.414l-5.516-7.906-2.719,2.2v4.938a2.881,2.881,0,0,0,.211,1.2,1.68,1.68,0,0,0,.563.7,2.019,2.019,0,0,0,.836.32,6.374,6.374,0,0,0,1.016.078h.422V0H4.777V-1.344h.406a6.906,6.906,0,0,0,1.008-.07A2.21,2.21,0,0,0,7.027-1.7a1.516,1.516,0,0,0,.57-.648,2.586,2.586,0,0,0,.211-1.133V-19.328A2.586,2.586,0,0,0,7.6-20.461a1.626,1.626,0,0,0-.57-.664,2.06,2.06,0,0,0-.836-.3,6.906,6.906,0,0,0-1.008-.07H4.777v-1.344h9.313V-21.5h-.422a6.374,6.374,0,0,0-1.016.078,2.019,2.019,0,0,0-.836.32,1.68,1.68,0,0,0-.562.7,2.881,2.881,0,0,0-.211,1.2v9.031ZM28.143,0V-1.344h.406a6.332,6.332,0,0,0,1.023-.078,2.076,2.076,0,0,0,.836-.32,1.6,1.6,0,0,0,.563-.7,2.979,2.979,0,0,0,.2-1.2V-19.2a2.979,2.979,0,0,0-.2-1.2,1.6,1.6,0,0,0-.562-.7,2.076,2.076,0,0,0-.836-.32,6.332,6.332,0,0,0-1.023-.078h-.406v-1.344h9.312V-21.5h-.422a6.374,6.374,0,0,0-1.016.078,2.019,2.019,0,0,0-.836.32,1.68,1.68,0,0,0-.562.7,2.881,2.881,0,0,0-.211,1.2V-3.641a2.881,2.881,0,0,0,.211,1.2,1.68,1.68,0,0,0,.563.7,2.019,2.019,0,0,0,.836.32,6.374,6.374,0,0,0,1.016.078h.422V0Z" transform="translate(37.456 22.844)"/>
  </g>
</svg>

            <h3 class="service-subtitle my-4">Resource Public Key Infrastructure</h3>
            <a href="/rpki/" class="btn btn-secondary mx-auto py-1">More Info...</a>
          </div>
        </div>
        <div class="col-md-6 pl-md-5 pt-5 pt-md-0 sector-style">
          <div class="inner-hook d-flex flex-column justify-content-between text-center">
            <svg class="service-logo-ntp" viewbox="0 0 140.811 18" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <style>
      .service-logo-ntp > .color1{ fill:#e60013; }
      .service-logo-ntp > .color2{ fill:#363636; }
    </style>
  </defs>
  <g class="color1" transform="translate(-1129.44 -107.319)">
    <path d="M8.4-16.3a9.064,9.064,0,0,1,3.884.774,5.824,5.824,0,0,1,2.539,2.2,6.192,6.192,0,0,1,.889,3.351A6.393,6.393,0,0,1,14.781-6.5a6.03,6.03,0,0,1-2.666,2.272,9.67,9.67,0,0,1-4.075.8H5.731V.963H1.44V-16.3Zm-.1,9.114a3.279,3.279,0,0,0,2.272-.762,2.592,2.592,0,0,0,.85-2.031,2.255,2.255,0,0,0-.85-1.853,3.515,3.515,0,0,0-2.272-.685H5.731v5.331Zm26.293.1a9.839,9.839,0,0,1-.977,4.544A6.69,6.69,0,0,1,30.818.354a8.8,8.8,0,0,1-4.278.99,8.727,8.727,0,0,1-4.265-.99A6.709,6.709,0,0,1,19.5-2.54a9.839,9.839,0,0,1-.977-4.544V-16.3h4.316v9.292a4.72,4.72,0,0,0,1,3.161,3.365,3.365,0,0,0,2.7,1.181,3.4,3.4,0,0,0,2.717-1.181,4.681,4.681,0,0,0,1.016-3.161V-16.3h4.316ZM50.5-7.923A3.757,3.757,0,0,1,52.846-6.45a4.352,4.352,0,0,1,.825,2.615A4.215,4.215,0,0,1,52.008-.344,7.145,7.145,0,0,1,47.477.963H38.439V-16.3h8.53a6.924,6.924,0,0,1,4.367,1.231,4.04,4.04,0,0,1,1.574,3.364,4.437,4.437,0,0,1-.647,2.323A3.235,3.235,0,0,1,50.5-7.923ZM46.309-9.268a2.978,2.978,0,0,0,1.841-.5,1.616,1.616,0,0,0,.647-1.358,1.421,1.421,0,0,0-.622-1.244,3.286,3.286,0,0,0-1.866-.432h-3.58v3.529Zm.609,6.677a3.065,3.065,0,0,0,1.9-.5,1.577,1.577,0,0,0,.635-1.307,1.328,1.328,0,0,0-.635-1.193,3.4,3.4,0,0,0-1.828-.406H42.729v3.4Zm9.14.207c0-1.719,0-13.916,0-13.916h4.291s-.013,12.537,0,12.859a.65.65,0,0,0,.557.622H63.59V.963H59.373A3.442,3.442,0,0,1,56.058-2.384ZM66.678.963V-16.3h4.291V.963ZM82.723-2.693a4.266,4.266,0,0,0,2.818-.927A4.437,4.437,0,0,0,87.014-6.2h4.392a8.592,8.592,0,0,1-1.422,3.922A7.921,7.921,0,0,1,86.951.379a9.214,9.214,0,0,1-4.2.939A9.484,9.484,0,0,1,78.09.163,8.446,8.446,0,0,1,74.8-3.048a9.037,9.037,0,0,1-1.193-4.621A9.037,9.037,0,0,1,74.8-12.289,8.446,8.446,0,0,1,78.09-15.5a9.484,9.484,0,0,1,4.659-1.155,9.213,9.213,0,0,1,4.2.939,7.921,7.921,0,0,1,3.034,2.653,8.592,8.592,0,0,1,1.422,3.922H87.014a4.437,4.437,0,0,0-1.472-2.577,4.266,4.266,0,0,0-2.818-.927A4.717,4.717,0,0,0,80.248-12,4.55,4.55,0,0,0,78.56-10.22a5.288,5.288,0,0,0-.609,2.551,5.288,5.288,0,0,0,.609,2.551A4.55,4.55,0,0,0,80.248-3.34,4.717,4.717,0,0,0,82.723-2.693Z" transform="translate(1128 123.976)"/>
  </g>
  <g class="color2" transform="translate(-1129.44 -107.319)">
    <path d="M110.134-.32,99.687-13.308V-.32H98.016v-16H99.4L109.9-3.191V-16.32h1.671v16Zm11.765,0h-1.671V-14.791h-5.953V-16.32h13.576v1.529H121.9Zm14.235-16a7.382,7.382,0,0,1,3.2.659,5.051,5.051,0,0,1,2.153,1.859,5.054,5.054,0,0,1,.765,2.776,5.2,5.2,0,0,1-.788,2.859,5.15,5.15,0,0,1-2.224,1.906,7.758,7.758,0,0,1-3.318.671h-3.694V-.32h-1.671v-16Zm-.071,9.2A4.847,4.847,0,0,0,139.357-8.2a3.561,3.561,0,0,0,1.247-2.824,3.373,3.373,0,0,0-1.247-2.729,4.99,4.99,0,0,0-3.294-1.035h-3.835V-7.12Z" transform="translate(1128 125.64)"/>
  </g>
</svg>
            <h3 class="service-subtitle my-4">時刻情報提供サービス for Public</h3>
            <a href="/ntp/" class="btn btn-secondary mx-auto py-1">More Info...</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</section><!-- //service -->
<section class="mfeed-cont-wrapper-latestnews py-5 font-plq" data-section-name="latestnews">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="topic-title text-center">Press <span class="mf-red">Releases</span></h2>

        <div class="row py-3">
          <div class="col">
<ul class="list-group list-group-flush" id="latestnews-list">
<?php foreach ( $press_items as $post ) : ?>
  <li class="list-group-item">
    <article class="archive-article archive-article-for-top archive-type-post">
      <header class="archive-article-header">
        <div class="d-flex align-items-end">
          <div class="article-datetime text-right">
            <a href="<?php echo esc_url( $post['url'] ); ?>" class="archive-article-date">
              <time datetime="<?php echo esc_attr( $post['date'] ); ?>" itemprop="datePublished" class="align-bottom">
                <?php echo esc_html( mfeed_format_ja_date( $post['date'] ) ); ?>
              </time>
            </a>
          </div>

          <div class="category ml-2">
            <i class="fas fa-newspaper"></i>
            <div class="article-category d-inline">
              PRESS
            </div>
          </div>
        </div>

        <h2 itemprop="name">
          <a class="archive-article-title font-weight-normal" href="<?php echo esc_url( $post['url'] ); ?>">
            <?php echo esc_html( $post['title'] ); ?>
          </a>
        </h2>
      </header>
    </article>
  </li>
<?php endforeach; ?>
</ul>

          </div>
        </div>

      </div>
    </div>
    <div class="row mt-3">
      <div class="col-12 text-center">
        <a class="btn btn-mfeed my-3" href="/archives/">
          READ MORE
        </a>
      </div>
    </div>
  </div>
</section><!-- //latestnews -->


<section class="mfeed-cont-wrapper-corporatestyle font-plq" data-section-name="corporatestyle">
  <div class="container">
    <div class="row">
      <div class="col-12 my-5">
        <div class="inner-hook">
          <h2 class="text-center topic-title mb-5">Topics</h2>

          <!-- トピックスの DOM 構造は、Press Releases で利用している _partial/latest-news に寄せる -->
          <div class="row py-3">
            <div class="col">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2026年6月10日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          Interop Tokyo 2026へのコントリビューションとして、開催期間中にShowNetとJPNAPを400Gで接続します。<br>
                          <object>Interop Tokyo 2026 ShowNetについては<a class="archive-article-title font-weight-normal" href="https://www.interop.jp/2026/shownet/" target="_blank">主催者の解説ページ</a>をご覧ください。</object>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2026年3月31日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          transix公式Webサイトをリニューアルしました。サービスの特長をより分かりやすくご紹介しているほか、資料ダウンロードページ、コラムなどのコンテンツを追加しています。ぜひご覧ください。<br>
                          transix公式Webサイトは<a class="archive-article-title font-weight-normal" href="https://www.mfeed.ad.jp/transix/" target="_blank">こちら</a><br>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2025年11月20日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          CEDEC+KYUSHU 2025の開催期間中に運用される会場Wi-FiネットワークとJPNAPを10Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2025年7月28日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JANOG56のネットワークサポーターとして、開催期間中に運用される会場ネットワークとJPNAPを10Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2025年6月11日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          Interop Tokyo 2025へのコントリビューションとして、開催期間中にShowNetとJPNAPを100Gで接続します。<br>
                          <object>Interop Tokyo 2025 ShowNetについては<a class="archive-article-title font-weight-normal" href="https://www.interop.jp/2025/shownet/" target="_blank">主催者の解説ページ</a>をご覧ください。</object>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2025年1月20日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JANOG55のネットワークサポーターとして、開催期間中に運用される会場ネットワークとJPNAPを10Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2024年11月20日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          CEDEC+KYUSHU 2024の開催期間中に運用される会場Wi-FiネットワークとJPNAPを10Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2024年7月3日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JANOG54のネットワークサポーターとして、開催期間中に運用される会場ネットワークとJPNAPを10Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2024年6月12日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          Interop Tokyo 2024へのコントリビューションとして、開催期間中にShowNetとJPNAPを100Gで接続します。<br>
                          <object>Interop Tokyo 2024 ShowNetについては<a class="archive-article-title font-weight-normal" href="https://www.interop.jp/2024/shownet/" target="_blank">主催者の解説ページ</a>をご覧ください。</object>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
               <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2024年3月29日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          西日本エリアで運用中の「JPNAP RPKI パブリックROAキャッシュサーバ」について、新たに東日本エリアでの運用を開始しました。<br>
                          詳細については<a class="archive-article-title font-weight-normal" href="https://www.mfeed.ad.jp/rpki/tech.html" target="_blank">こちら</a><br>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2024年2月1日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAPではこのたび、2014年より運用しているRPKI パブリックROAキャッシュサーバについて、RPKIを取り巻く昨今の状況の変化を取り入れつつ、<br>
                          より使いやすいサービスを目指しリニューアルを実施しました。詳細については<a class="archive-article-title font-weight-normal" href="https://www.mfeed.ad.jp/rpki/tech.html" target="_blank">こちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2022年7月13日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          ■ 書籍「ピアリング戦記 日本のインターネットを繋ぐ技術者たち」出版への協賛について<br>
                          当社は、2022年7月13日に発売される書籍「ピアリング戦記 日本のインターネットを繋ぐ技術者たち」に協賛します。<br>
                          業界の有志が発起人となり、ピアリングを切り口として日本のインターネットがどの様に発展し、実社会に変化をもたらしてきたかを、実ビジネス目線で綴った書籍となっております。<br>
                          当社は、本書籍への協賛を通じて、ピアリングコミュニティとインターネット環境のさらなる普及と発展に貢献します。<br>
                          <br>
                          <書籍概要><br>
                          書名：ピアリング戦記 日本のインターネットを繋ぐ技術者たち<br>
                          著者：小川晃通<br>
                          発売日: 2022年7月13日<br>
                          出版社: ラムダノート株式会社<br>
                          定価: 2,200円（税込）<br>
                          ISBN: 978-4-908686-14-6
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2022年7月12日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JANOG50 Meeting の ホットトピックとして、IX相互接続実証実験でのアジア初400G導入について掲載されました。<br>
                          <a class="archive-article-title font-weight-normal" href="https://www.janog.gr.jp/meeting/janog50/ix%E7%9B%B8%E4%BA%92%E6%8E%A5%E7%B6%9A%E5%AE%9F%E8%A8%BC%E5%AE%9F%E9%A8%93%E3%82%92%E9%80%9A%E3%81%98%E3%81%A6%E8%A6%8B%E3%81%88%E3%81%A6%E3%81%8D%E3%81%9F400g%E5%B0%8E%E5%85%A5%E3%81%A7%E3%80%8C/">JANOG50 Meeting でのアジア初 400G 実証実験に関する掲載記事はこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2021年10月29日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAPはPCCW Global Ltd.と協業を開始しました。PCCW Global Ltd.が提供するConsole Connect経由で、
                          JPNAP東京へのリモートピアリングが可能になります。<br>
                        <a class="archive-article-title font-weight-normal" href="https://www.consoleconnect.com/2021/07/pccw-global-launches-on-demand-access-to-leading-ix-platforms-through-console-connect/">PCCW Global Ltd.のニュースリリースはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2021年10月1日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          ■当社の業務体制について<br>
                          新型コロナウィルス感染症拡大防止に向けて、弊社では全社的にリモートワークによる業務体制とし、7月12日よりお問い合わせはメールでのご連絡をお願いさせて頂いておりましたが、緊急事態宣言の解除に伴い、当社へのお問合せについて、電話を含め従来通りのお問合せ窓口とさせて頂きます。ご不便をおかけし申し訳ございませんでした。<br>
                          尚、今後の感染拡大の動向によっては、再度、お問合せ窓口等を変更させていただく場合がございます。その際は、改めてお知らせします。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2021年9月1日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          ■JANOG50をホスト会社としてサポートします<br>
                          当社は、2022年7月13日〜15日に北海道函館市で開催予定のJANOG50ミーティングをホストとしてサポートさせて頂きます。<br>
                          JANOG50は、JANOGミーティング開始以来25周年となるミーティングです。<br>
                          社員一同、JANOGミーティングのサポートを通じ、インターネットの課題解決・更なる発展 及び JANOGコミュニティの発展に貢献していきたいと思っております。<br>
                          多くのインターネット関係者にJANOGミーティングにご参加いただけることを楽しみにしております。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2021年3月24日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAPはNTT Ltd.と協業を開始しました。NTT Ltd.のバンコク2データセンターを介したBangkok Neutral Internet eXchange (BKNIX) とJPNAP東京との相互接続により、
                          バンコク2データセンターのみならずBKNIXの各接続拠点からJPNAP東京へのリモートピアリングの提供が可能となりました。<br>
                          <a class="archive-article-title font-weight-normal" href="https://hello.global.ntt/en-us/newsroom/ntt-establishes-its-bangkok-2-data-center-as-international-network-exchange-hub">NTT Ltd.のニュースリリースはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2020年12月24日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAP RouteFEEDサービスにおいて、全てのルートサーバーでRPKI (Resource Public Key Infrastructure) による経路の広報元検証 (ROV: Route Origin Validation) を導入しました。
                          これによりルートサーバーを介したお客様間の経路交換がこれまで以上にセキュアになります。JPNAPは引き続きインターネットルーティングセキュリティの向上に努めてまいります。<br>
                          <!--- <a> のネストができずに「ご覧ください」が <h2> で出力されてしまうため <object> で回避している。参考: https://blog.n-t.jp/tech/html-nested-anchor/ -->
                          <object>RPKIについては <a class="archive-article-title font-weight-normal" href="https://www.nic.ad.jp/ja/rpki/">JPNIC様の解説ページ</a> をご覧ください。</object>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2020年6月9日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          NGN IPoE協議会が発展的に解散し、一般社団法人 IPoE協議会として新たに活動を開始しました。
                          当社は継続して新法人に参画いたします。<br>
                          <a class="archive-article-title font-weight-normal" href="https://ipoe-c.jp/__assets__/20200609_pressrelease.pdf">一般社団法人IPoE協議会のニュースリリースはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2020年6月9日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAP東京RouteFEEDサービスにおいて、RPKI対応ルートサーバートライアルを実施します。RPKIを実装したルートサーバーを試験的に運用し、
                          ご協力いただけるお客様に対してRPKI ROVをご提供いたします。ご接続いただけるお客様はJPNAPカスタマサポートまでお問い合わせください。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2020年4月10日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          インターネットトラヒック流通効率化検討協議会に参加いたします。<br>
                          <a class="archive-article-title font-weight-normal" href="https://www.soumu.go.jp/menu_news/s-news/01kiban04_02000165.html">協議会についてはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2020年3月26日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          Megaport MarketplaceでのJPNAP東京サービスの申込受付を開始いたしました。MegaportのSDNサービス経由で、JPNAP東京でのリモートピアリングが可能になります。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2019年6月12日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          Interop Tokyo 2019へのコントリビューションとして、開催期間中にShowNetとJPNAPを100Gで接続します。
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2019年4月24日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          NGN IPoE協議会がIPv6地理情報共有ワーキンググループを発足しました。当社もそのIPv6の普及促進という設立趣意に賛同し、ワーキンググループに参画します。<br>
                          <a class="archive-article-title font-weight-normal" href="https://ipoe-c.jp/__assets__/20190424.pdf">NGN IPoE協議会のニュースリリースはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
                <li class="list-group-item">
                  <article class="archive-article archive-article-for-top archive-type-post">
                    <header class="archive-article-header">
                      <div class="d-flex align-items-end">
                        <div class="article-datetime text-right">
                          <a class="archive-article-date"><time class="align-bottom">
                            2017年11月20日
                          </time></a>
                        </div>
                      </div>
                      <h2 itemprop="name">
                        <a class="archive-article-title font-weight-normal">
                          JPNAPはジャパンケーブルキャスト株式会社と協業し、ケーブルテレビ事業者向け多チャンネル映像配信サービスであるJC-HITSをJPNAP東京経由で受信できるオプションサービスを開始しました。JPNAP東京をご利用のケーブルテレビ事業者は、接続に利用している専用線の余剰帯域にJC-HITS信号を重畳して受信することで、コストダウンを図ることが可能です。<br>
                          <a class="archive-article-title font-weight-normal" href="http://www.cablecast.co.jp/press/pdf/0254_20171120.pdf">ジャパンケーブルキャスト株式会社のニュースリリースはこちら</a>
                        </a>
                      </h2>
                    </header>
                  </article>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section><!-- //corporatestyle -->

<?php /*
// Original: fetch from external JPNAP API
          <div class="row py-3">
            <div class="col">
              <ul class="list-group list-group-flush" id="topics-list">
  <?php if ( ! empty( $jpnap_topics_items ) ) : ?>
    <?php foreach ( $jpnap_topics_items as $item ) : ?>
      <li class="list-group-item">
        <article class="archive-article archive-article-for-top archive-type-post">
          <header class="archive-article-header">
            <div class="d-flex align-items-end">
              <div class="article-datetime text-right">
                <a class="archive-article-date" href="<?php echo esc_url( $item['url'] ); ?>">
                  <time class="align-bottom" datetime="<?php echo esc_attr( $item['date'] ); ?>" itemprop="datePublished">
                    <?php echo esc_html( mfeed_format_ja_date( $item['date'] ) ); ?>
                  </time>
                </a>
              </div>
            </div>
            <h2 itemprop="name">
              <a class="archive-article-title font-weight-normal" href="<?php echo esc_url( $item['url'] ); ?>">
                <?php echo esc_html( $item['title'] ); ?>
              </a>
            </h2>
          </header>
        </article>
      </li>
    <?php endforeach; ?>
  <?php else : ?>
    <li class="list-group-item">
      現在、トピックスはありません。
    </li>
  <?php endif; ?>
</ul>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section><!-- //corporatestyle -->
*/ ?>

<?php get_footer(); ?>