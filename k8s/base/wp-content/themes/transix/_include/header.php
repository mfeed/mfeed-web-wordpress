<button class="c-drawerBtn" type="button" aria-label="メニューを開く" aria-haspopup="true"><span class="c-drawerBtn__border"></span></button>
<div class="l-drawer">
  <div class="l-drawer__inner">
    <div class="l-drawer__head">
      <form class="c-searchBox" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
        <div class="c-searchBox__input">
          <input type="search" name="s" id="search">
          <button type="submit" aria-label="検索する">
            <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_search.svg" alt="検索">
          </button>
        </div>
      </form>
      <dl class="c-faqChose">
        <dt class="c-faqChose__label">よくあるご質問</dt>
        <dd class="c-faqChose__item"><a href="<?php echo home_url('faq'); ?>">ISP事業者さま向け</a></dd>
        <dd class="c-faqChose__item"><a href="<?php echo home_url('faq/'); ?>#users">個人のお客さま向け</a></dd>
      </dl>
      <div class="c-ipType">
        <strong><?php echo $label; ?></strong>で接続中
      </div>
    </div>
    <div class="l-drawer__nav">
      <nav class="l-nav">
        <ul class="l-nav__list">
          <li class="l-nav__listItem c-accordion">
            <div class="c-accordion__btn"><span>transixとは</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('features'); ?>" class="c-accordionNav__link<?php if ( is_page('features') ) echo ' is-current'; ?>">transixの​特長</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('option'); ?>" class="c-accordionNav__link<?php if ( is_page('option') ) echo ' is-current'; ?>">オプションサービス</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem c-accordion">
            <div class="c-accordion__btn"><span>接続方式</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  IPv6接続「IPoE」
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('dslite'); ?>" class="c-accordionNav__link<?php if ( is_page('dslite') ) echo ' is-current'; ?>">IPv4 over IPv6接続「DS-Lite」</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('staticip'); ?>" class="c-accordionNav__link<?php if ( is_page('staticip') ) echo ' is-current'; ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem c-accordion">
            <div class="c-accordion__btn"><span>対応機種</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('dslite-models'); ?>" class="c-accordionNav__link<?php if ( is_page('dslite-models') ) echo ' is-current'; ?>">IPv4 over IPv6接続「DS-Lite」</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('staticip-models'); ?>" class="c-accordionNav__link<?php if ( is_page('staticip-models') ) echo ' is-current'; ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('cases'); ?>"<?php if ( is_page('cases') ) echo ' class="is-current"'; ?>>活用ケース</a></li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('customers'); ?><?php if ( is_page('customers') ) echo ' class="is-current"'; ?>">採用ISP</a></li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('implementation-process'); ?><?php if ( is_page('implementation-process') ) echo ' class="is-current"'; ?>">導入の流れ</a></li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('downloads'); ?>" target="_blank">資料ダウンロード</a></li>
          <li class="l-nav__listItem"><a href="https://multifeed.atlassian.net/servicedesk/customer/portal/21/group/54/create/211" target="_blank">お問い合わせ</a></li>
        </ul>
      </nav>
    </div>
  </div>
</div>
<!-- drawer-->
<header class="l-header">
  <div class="l-header__left">
    <a href="<?php echo home_url(); ?>" class="l-header__logo">
      <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/logo.svg" alt="transix" width="160" height="34">
      <p>ISP事業者さま向けインターネット接続 (VNE) サービス</p>
    </a>
  </div>
  <div class="l-header__right">
    <div class="l-header__head">
      <div class="c-accordion">
        <button class="c-accordion__btn" type="button">
          <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_search-wh.svg" alt="検索">
        </button>
        <div class="c-accordion__target">
          <form class="c-searchBox" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
            <div class="c-searchBox__input">
              <input type="search" name="s" id="search">
              <button type="submit" aria-label="検索する">
                <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_search.svg" alt="検索">
              </button>
            </div>
          </form>
        </div>
      </div>
      <dl class="c-faqChose">
        <dt class="c-faqChose__label">よくあるご質問</dt>
        <dd class="c-faqChose__item"><a href="<?php echo home_url('faq'); ?>">ISP事業者さま向け</a></dd>
        <dd class="c-faqChose__item"><a href="<?php echo home_url('faq/'); ?>#users">個人のお客さま向け</a></dd>
      </dl>
      <div class="c-ipType">
        <strong><?php echo $label; ?></strong>で接続中
      </div>
    </div>
    <div class="l-header__nav">
      <nav class="l-nav">
        <ul class="l-nav__list">
          <li class="l-nav__listItem c-accordion --hover">
            <div class="c-accordion__btn"><span>transixとは</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('features'); ?>" class="c-accordionNav__link<?php if ( is_page('features') ) echo ' is-current'; ?>">transixの​特長</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('option'); ?>" class="c-accordionNav__link<?php if ( is_page('option') ) echo ' is-current'; ?>">オプションサービス</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem c-accordion --hover">
            <div class="c-accordion__btn"><span>接続方式</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  IPv6接続「IPoE」
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('dslite'); ?>" class="c-accordionNav__link<?php if ( is_page('dslite') ) echo ' is-current'; ?>">IPv4 over IPv6接続「DS-Lite」</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('staticip'); ?>" class="c-accordionNav__link<?php if ( is_page('staticip') ) echo ' is-current'; ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem c-accordion --hover">
            <div class="c-accordion__btn"><span>対応機種</span></div>
            <div class="c-accordion__target">
              <ul class="c-accordionNav">
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('dslite-models'); ?>" class="c-accordionNav__link<?php if ( is_page('dslite-models') ) echo ' is-current'; ?>">IPv4 over IPv6接続「DS-Lite」</a>
                </li>
                <li class="c-accordionNav__item">
                  <a href="<?php echo home_url('staticip-models'); ?>" class="c-accordionNav__link<?php if ( is_page('staticip-models') ) echo ' is-current'; ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('cases'); ?>"<?php if ( is_page('cases') ) echo ' class="is-current"'; ?>>活用ケース</a></li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('customers'); ?>"<?php if ( is_page('customers') ) echo ' class="is-current"'; ?>>採用ISP</a></li>
          <li class="l-nav__listItem"><a href="<?php echo home_url('implementation-process'); ?>"<?php if ( is_page('implementation-process') ) echo ' class="is-current"'; ?>>導入の流れ</a></li>
          <li class="l-nav__listItem --pc"><a href="<?php echo home_url('downloads'); ?>" target="_blank">資料ダウンロード</a></li>
          <li class="l-nav__listItem"><a href="https://multifeed.atlassian.net/servicedesk/customer/portal/21/group/54/create/211" target="_blank">お問い合わせ</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>
<!-- header -->
