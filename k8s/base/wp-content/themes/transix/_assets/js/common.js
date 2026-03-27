/*================================
  JS Information
----------------------------------
  File name: common.js
  Summary:   全ページ共通JSファイル
  Author:    coder-ao
  last-update 2025/04/18
================================*/

// const
const body = document.body;
const header = document.querySelector("header");
const footer = document.querySelector("footer");
const mainContents = document.querySelector(".l-main__contents");
const pageCode = body.className;

// 要素の幅・高さ取得
function setVars() {
  const windowWidth = window.innerWidth;
  const scrollbarWidth = window.innerWidth - document.body.clientWidth;
  const viewHeight = window.innerHeight;

  const header = document.querySelector(".l-header");
  const footer = document.querySelector("footer");

  const headerHeight = header ? header.offsetHeight : 0;
  const footerHeight = footer ? footer.offsetHeight : 0;

  document.documentElement.style.setProperty("--js-scrollbarWidth", `${scrollbarWidth}px`);
  document.documentElement.style.setProperty("--js-viewHeight", `${viewHeight}px`);
  document.documentElement.style.setProperty("--js-headerHeight", `${headerHeight}px`);
  document.documentElement.style.setProperty("--js-footerHeight", `${footerHeight}px`);
}

document.addEventListener("DOMContentLoaded", setVars);
window.addEventListener("load", setVars);
window.addEventListener("resize", setVars);


window.addEventListener("scroll", function () {
  var scroll = window.scrollY;
  if (scroll > 0) {
    header.classList.add("is-fixed");
  } else {
    header.classList.remove("is-fixed");
  }
});

// drawer
function drawer() {
  var drawerLinks = document.querySelectorAll(".l-drawer a");
  var drawerParts = document.querySelectorAll(".l-drawer, .c-drawerBtn");
  var drawerBtn = document.querySelector(".c-drawerBtn");
  // var drawerBtnTxt = document.querySelector(".c-drawerBtn__txt");

  for (let i = 0; i < drawerParts.length; i++) {
    // ハンバーガーメニューボタンクリック時の挙動
    drawerBtn.addEventListener("click", function () {
      const part = drawerParts[i];

      if (part.classList.contains("is-open")) {
        // 今開いてる → 閉じる処理
        part.classList.remove("is-open");

        // トランジション終了後に is-closed を付ける（CSSアニメ終了待ち）
        part.addEventListener("transitionend", function handleTransition() {
          part.classList.add("is-closed");
          part.removeEventListener("transitionend", handleTransition);
        });

        body.classList.remove("is-scrollNone");
        header.classList.remove("is-drawerOpen");
      } else {
        // 今閉じてる → 開く処理
        part.classList.remove("is-closed");
        part.classList.add("is-open");

        body.classList.add("is-scrollNone");
        header.classList.add("is-drawerOpen");
      }
    });

    // ドロワーメニュー内のリンクをクリック時にドロワーを閉じる
    for (let d = 0; d < drawerLinks.length; d++) {
      drawerLinks[d].addEventListener("click", function () {
        body.classList.remove("is-scrollNone");
        drawerParts[i].classList.remove("is-open");
        drawerParts[i].classList.add("is-closed");
      });
    }
  }
}

// pageTopのスクロール先
function scrollToHead() {
  body.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}

// アンカーリンククリックでscroll-margin-topが効くように補正スクロール
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', (e) => {
    const id = a.getAttribute('href');
    const target = document.querySelector(id);
    if (!target) return;

    e.preventDefault();

    target.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    });

    history.pushState(null, '', id);
  });
});

// 子要素や孫要素の高さを揃える
function setEqualHeightByDataGroup() {
  const elements = document.querySelectorAll('.js-heightEven');
  const groups = {};

  // グループ分け
  elements.forEach(el => {
    const group = el.dataset.group;
    if (!group) return;
    if (!groups[group]) groups[group] = [];
    groups[group].push(el);
  });

  // グループごとに高さを揃える
  Object.values(groups).forEach(groupEls => {
    let maxHeight = 0;

    // リセット
    groupEls.forEach(el => el.style.height = 'auto');

    // 最大高さ取得
    groupEls.forEach(el => {
      if (el.offsetHeight > maxHeight) {
        maxHeight = el.offsetHeight;
      }
    });

    // 最大高さを適用
    groupEls.forEach(el => el.style.height = maxHeight + 'px');
  });
}
window.addEventListener('load', setEqualHeightByDataGroup);
window.addEventListener('resize', setEqualHeightByDataGroup);

// アコーディオン
function accordion() {
  document.querySelectorAll(".c-accordion").forEach((wrap) => {
    const target = wrap.querySelector(".c-accordion__target");
    const btn = wrap.querySelector(".c-accordion__btn");

    target.addEventListener("transitionend", (e) => {
      if (e.propertyName !== "height") return;
      if (wrap.classList.contains("is-open")) {
        target.style.height = "auto";
      }
    });

    btn.addEventListener("click", () => {
      const isOpen = wrap.classList.contains("is-open");

      if (isOpen) {
        // 現在の高さをpxに固定→0へ
        target.style.height = target.getBoundingClientRect().height + "px";
        requestAnimationFrame(() => (target.style.height = "0px"));
        wrap.classList.remove("is-open");
        target.classList.remove("is-open");
        btn.classList.remove("is-open");
      } else {
        // いったん0にしてからscrollHeightへ
        target.style.height = "0px";
        wrap.classList.add("is-open");
        target.classList.add("is-open");
        btn.classList.add("is-open");
        requestAnimationFrame(() => {
          target.style.height = target.scrollHeight + "px";
        });
      }
    });
  });
}

// アコーディオン（hover）
function accordionHover() {
  var accordions = document.querySelectorAll(".c-accordion.--hover");
  var windowWidth = window.innerWidth; // 現在のウィンドウ幅を取得

  // Hoverで動作するアコーディオン（PC向け）
  if (windowWidth >= 768) {
    accordions.forEach(function (accordion) {
      var accordionTarget = accordion.querySelector(".c-accordion__target");
      var accordionBtn = accordion.querySelector(".c-accordion__btn");

      // マウスが要素上に入ったとき
      accordion.addEventListener("mouseenter", function () {
        accordion.classList.add("is-open");
        accordionTarget.classList.add("is-open");
        accordionBtn.classList.add("is-open");
      });

      // マウスが要素から離れたとき
      accordion.addEventListener("mouseleave", function () {
        accordion.classList.remove("is-open");
        accordionTarget.classList.remove("is-open");
        accordionBtn.classList.remove("is-open");
      });
    });
  }
}

// スライダー
document.addEventListener("DOMContentLoaded", function () {
  const swiper = new Swiper(".c-slider", {
    loop: true,
    slidesPerView: 1,
    spaceBetween: 0,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    // pagination: {
    //   el: ".swiper-pagination",
    //   clickable: true,
    // },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
});


// スクロールヒント
let scrollHintInstance = null;

function enableScrollHint() {
  // 既に初期化済みなら何もしない
  if (scrollHintInstance) return;

  // 対象要素がない場合も何もしない
  if (!document.querySelector(".c-tableScroll, .wp-block-table")) return;

  scrollHintInstance = new ScrollHint(".c-tableScroll, .wp-block-table", {
    i18n: {
      scrollable: "横スクロール",
    },
  });
}

function disableScrollHint() {
  if (!scrollHintInstance) return;

  // ScrollHint が付けたクラスや要素をざっくり掃除（簡易版）
  document.querySelectorAll(".c-tableScroll").forEach((el) => {
    el.classList.remove("scroll-hint");
    el.classList.remove("is-scrollable");
  });
  document
    .querySelectorAll(".scroll-hint-icon, .scroll-hint-text")
    .forEach((el) => el.remove());

  scrollHintInstance = null;
}

function toggleScrollHint() {
  if (window.innerWidth <= 940) {
    enableScrollHint();
  } else {
    disableScrollHint();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  toggleScrollHint(); // 初回チェック

  window.addEventListener("resize", () => {
    toggleScrollHint();
  });
});


// 追従バナー
document.addEventListener('DOMContentLoaded', function () {
  const banners = document.querySelectorAll('.js-scrollFixed');
  if (!banners.length) return;

  const THRESHOLD = 20; // px

  function onScroll() {
    banners.forEach(banner => {
      if (window.scrollY >= THRESHOLD) {
        banner.classList.add('is-fixed');
      } else {
        banner.classList.remove('is-fixed');
      }
    });
  }

  // 初期状態
  onScroll();

  // スクロール時
  window.addEventListener('scroll', onScroll);
});

document.addEventListener("DOMContentLoaded", function () {
  const banner = document.querySelector(".c-fixedBanner");
  const closeBtn = document.querySelector(".c-fixedBanner__close");

  if (!banner || !closeBtn) return;

  const STORAGE_KEY = "fixedBannerClosedAt";
  const HIDE_DURATION = 24 * 60 * 60 * 1000; // 24時間（ミリ秒）

  // 以前閉じた時間を取得
  const closedAt = localStorage.getItem(STORAGE_KEY);

  // 24時間以内なら非表示にする
  if (closedAt) {
    const elapsed = Date.now() - Number(closedAt);
    if (elapsed < HIDE_DURATION) {
      banner.style.display = "none";
    }
  }

  // ×ボタンで閉じる
  closeBtn.addEventListener("click", () => {
    banner.style.display = "none";

    // 現在時刻を保存して「24時間非表示」に設定
    localStorage.setItem(STORAGE_KEY, Date.now());
  });
});


/*==アニメーション==================*/

// ロードしたらアニメーション
var loadAnimeTargets = document.querySelectorAll(".js-loadAnime");
// var loading = document.querySelector('.p-loading');
window.addEventListener("load", function () {
  for (let target of loadAnimeTargets) {
    setTimeout(() => {
      target.classList.add("is-animated");
    }, 800);
  }
  setTimeout(() => {
    // loading
    body.classList.add("is-loaded");
    // loading.classList.add('is-loaded');
  }, 0);
});

// 画面に表示されたらアニメーション
var scrollAnimeTargets = document.querySelectorAll(
  ".js-scrollAnime-fadeIn, .js-scrollAnime-fadeInUp, .js-scrollAnime-fadeInLeftRight, .js-scrollAnime-fadeInRightLeft, .js-scrollAnime-band, .js-scrollAnime-line"
);
window.addEventListener("scroll", function () {
  var scroll = window.scrollY;
  var viewHeight = window.innerHeight; 
  for (let target of scrollAnimeTargets) {
    var targetPos = target.getBoundingClientRect().top + scroll + 100;
    if (scroll > targetPos - viewHeight) {
      target.classList.add("is-animated");
    }
  }
});


/*==ON/OFF========================*/

drawer();
accordion();
accordionHover();

/*================================*/
