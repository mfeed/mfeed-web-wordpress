<div class="c-fixedBanner">
  <button class="c-fixedBanner__close" type="button" aria-label="バナーを非表示">
    <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_close.svg" alt="閉じる">
  </button>
  <a href="<?php echo home_url('downloads'); ?>" target="_blank" class="c-fixedBanner__link --gray">
    <picture class="c-fixedBanner__icon">
      <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_dl.svg" alt="download">
    </picture>
    <div class="c-fixedBanner__label">資料ダウンロード</div>
  </a>
  <a href="https://multifeed.atlassian.net/servicedesk/customer/portal/21/group/54/create/211" target="_blank" class="c-fixedBanner__link --white">
    <picture class="c-fixedBanner__icon">
      <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/icon_mail.svg" alt="mail">
    </picture>
    <div class="c-fixedBanner__label">お問い合わせ</div>
  </a>
</div>
<footer class="l-footer">
  <div class="l-footer__main">
    <div class="c-inner">
      <div class="l-footer__left">
        <picture class="u-logo">
          <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/logo_wh.svg" alt="transix" width="219" height="47">
        </picture>
        <small>
          ISP事業者向けインターネット<br class="u-pc">接続 (VNE) サービス
        </small>
        <div class="l-footer__btn u-pc">
          <a href="<?php echo home_url('downloads'); ?>" target="_blank" class="c-btnEnJa --more --white">
            <span class="c-btnEnJa__ja">資料ダウンロード</span>
          </a>
          <a href="https://multifeed.atlassian.net/servicedesk/customer/portal/21/group/54/create/211" target="_blank" class="c-btnEnJa --more --white">
            <span class="c-btnEnJa__ja">お問い合わせ</span>
          </a>
        </div>
      </div>
      <div class="l-footer__right">
        <div class="c-fNav">
          <div class="c-fNav__list">
            <ul class="c-fNavList">
              <li>
                  <div class="c-fNavList__label">transixとは</div>
                  <ul class="c-fNavList__sub">
                    <li><a href="<?php echo home_url('features'); ?>">transixの特長</a></li>
                    <li><a href="<?php echo home_url('option'); ?>">オプションサービス</a></li>
                  </ul>
              </li>
              <li>
                  <div class="c-fNavList__label">接続方式</div>
                  <div>IPv6接続「IPoE」</div>
                  <ul class="c-fNavList__sub">
                    <li><a href="<?php echo home_url('dslite'); ?>">IPv4 over IPv6接続「DS-Lite」</a></li>
                    <li><a href="<?php echo home_url('staticip'); ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a></li>
                  </ul>
              </li>
              <li>
                  <div class="c-fNavList__label">対応機種</div>
                  <ul class="c-fNavList__sub">
                    <li><a href="<?php echo home_url('dslite-models'); ?>">IPv4 over IPv6接続「DS-Lite」</a></li>
                    <li><a href="<?php echo home_url('staticip-models'); ?>">IPv4 over IPv6接続「固定IP (IPIP)」</a></li>
                  </ul>
              </li>
            </ul>
          </div>
        </div>
        <div class="c-fNav">
          <div class="c-fNav__list">
            <ul class="c-fNavList --space">
              <li><a href="<?php echo home_url('cases'); ?>">活用ケース</a></li>
              <li><a href="<?php echo home_url('customers'); ?>">採用ISP</a></li>
              <li><a href="<?php echo home_url('implementation-process'); ?>">導入の流れ</a></li>
              <li><a href="<?php echo home_url('faq'); ?>">よくある質問</a></li>
            </ul>
          </div>
        </div>
        <dl class="c-fNav">
          <dt class="c-fNav__label">コラム</dt>
          <dd class="c-fNav__list">
            <ul class="c-fNavList__sub">
              <li><a href="<?php echo home_url('column/001'); ?>"><small>ISP事業を始めるなら知っておきたいVNEとIPoEの基礎知識</small></a></li>
            </ul>
          </dd>
        </dl>
      </div>
      <div class="l-footer__btn u-sp">
        <a href="<?php echo home_url('downloads'); ?>" target="_blank" class="c-btnEnJa --more --white">
          <span class="c-btnEnJa__ja">資料ダウンロード</span>
        </a>
        <a href="https://multifeed.atlassian.net/servicedesk/customer/portal/21/group/54/create/211" target="_blank" class="c-btnEnJa --more --white">
          <span class="c-btnEnJa__ja">お問い合わせ</span>
        </a>
      </div>
    </div>
  </div>
  <div class="l-footer__sub">
    <div class="c-inner">
      <div class="c-fNavSub">
        <a href="https://www.mfeed.ad.jp/" target="_blank" class="u-logo">
          <img src="<?php echo get_template_directory_uri(); ?>/_assets/img/common/logo02.webp" alt="INTERNET MULTIFEED CO." width="262" height="56">
        </a>
        <ul class="c-fNavSub__list">
          <li><a href="/about/message/" target="_blank">会社案内</a></li>
          <li><a href="/archives/" target="_blank">プレスリリース</a></li>
          <li><a href="https://www.jpnap.net/" target="_blank">JPNAP(インターネットエクスチェンジサービス)</a></li>
          <li><a href="/about/policy/" target="_blank">個人情報保護方針</a></li>
          <li><a href="/about/privacy/" target="_blank">個人情報の取り扱いについて</a></li>
        </ul>
      </div>
      <div class="c-copyright">Copyright© INTERNET MULTIFEED CO. All Rights Reserved.</div>
    </div>
  </div>
</footer>
