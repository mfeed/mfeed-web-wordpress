// home.js

// --------------------------------------
// 設定：カテゴリIDによる振り分けルール
// --------------------------------------

// ★ここをWordPress側のカテゴリIDに合わせて修正してください
const PRESS_CATEGORY_IDS  = [6];        // プレスリリース用カテゴリID
const TOPICS_CATEGORY_IDS = [7, 8];     // トピックス用カテゴリID（なければ空配列でもOK）

function isPressPost(wpPost) {
  if (!Array.isArray(wpPost.categories)) return false;
  return wpPost.categories.some(id => PRESS_CATEGORY_IDS.includes(id));
}

function isTopicsPost(wpPost) {
  if (!Array.isArray(wpPost.categories)) return false;
  // Topics用カテゴリIDを使う場合
  if (TOPICS_CATEGORY_IDS.length > 0) {
    return wpPost.categories.some(id => TOPICS_CATEGORY_IDS.includes(id));
  }
  // もしくは「Press以外全部Topics」にしたい場合はこうでもOK
  return !isPressPost(wpPost);
}

// --------------------------------------
// 日付を「2025年10月1日」形式に整形するヘルパ
// --------------------------------------
function formatJaDate(isoString) {
  const d = new Date(isoString);
  if (Number.isNaN(d.getTime())) return isoString; // 万一壊れてたらそのまま
  const year = d.getFullYear();
  const month = d.getMonth() + 1;
  const day = d.getDate();
  return `${year}年${month}月${day}日`;
}

// --------------------------------------
// WordPressの投稿オブジェクト → UI用オブジェクトに変換
// --------------------------------------
function convertWpPostToItem(wpPost, categoryLabel) {
  return {
    date: wpPost.date,
    title: wpPost.title?.rendered || '',
    url: wpPost.link || null,
    category: categoryLabel || '',
    // 必要なら本文の抜粋なども使える
    // html: wpPost.excerpt?.rendered || ''
  };
}

// --------------------------------------
// Press Releases 用の li を生成
// --------------------------------------
function createPressReleaseItem(item) {
  const li = document.createElement('li');
  li.className = 'list-group-item';

  const dateText = formatJaDate(item.date);
  const datetimeAttr = item.date; // ISO文字列をそのままtimeのdatetimeに

  li.innerHTML = `
    <article class="archive-article archive-article-for-top archive-type-post">
      <header class="archive-article-header">
        <div class="d-flex align-items-end">
          <div class="article-datetime text-right">
            <a href="${item.url || '#'}" class="archive-article-date">
              <time datetime="${datetimeAttr}" itemprop="datePublished" class="align-bottom">
                ${dateText}
              </time>
            </a>
          </div>

          <div class="category ml-2">
            <i class="fas fa-newspaper"></i>
            <div class="article-category d-inline">
              ${item.category || 'PRESS'}
            </div>
          </div>
        </div>

        <h2 itemprop="name">
          <a class="archive-article-title font-weight-normal" href="${item.url || '#'}">
            ${item.title}
          </a>
        </h2>
      </header>
    </article>
  `;

  return li;
}

// --------------------------------------
// Topics 用の li を生成
// --------------------------------------
function createTopicItem(item) {
  const li = document.createElement('li');
  li.className = 'list-group-item';

  const dateText = formatJaDate(item.date);
  const titleContent = item.html || item.title;
  const titleHrefAttr = item.url ? `href="${item.url}"` : '';

  li.innerHTML = `
    <article class="archive-article archive-article-for-top archive-type-post">
      <header class="archive-article-header">
        <div class="d-flex align-items-end">
          <div class="article-datetime text-right">
            <a class="archive-article-date">
              <time class="align-bottom">
                ${dateText}
              </time>
            </a>
          </div>
        </div>
        <h2 itemprop="name">
          <a class="archive-article-title font-weight-normal" ${titleHrefAttr}>
            ${titleContent}
          </a>
        </h2>
      </header>
    </article>
  `;

  return li;
}

// --------------------------------------
// API を叩いて描画するメイン処理（全件取得＋振り分け）
// --------------------------------------
async function loadHomeContents() {
  const pressListElem  = document.getElementById('latestnews-list');
  const topicsListElem = document.getElementById('topics-list');

  // 対象のULが一つも無ければ何もしない
  if (!pressListElem && !topicsListElem) return;

  try {
    // ★ここ重要：ブラウザから見て到達できるURLにしてください
    //   - テスト環境なら相対パスにするのがおすすめ：
    //     const apiUrl = '/home/api/wp/v2/notice_ja?per_page=20';
    //   - いまのような Docker 内ホスト名（http://www-jpnap-...）は、
    //     ブラウザからは名前解決できないのでNGです。
    const apiUrl = '/home/api/wp/v2/notice_ja?per_page=20&orderby=date&order=desc';

    const res = await fetch(apiUrl, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    });

    if (!res.ok) {
      console.error('API error:', res.status, res.statusText);
      return;
    }

    const data = await res.json();
    console.log('WP raw data:', data);

    // data が配列で返ってくる想定（一覧API）
    const posts = Array.isArray(data) ? data : [data];

    const pressItems = [];
    const topicItems = [];

    posts.forEach((post) => {
      if (isPressPost(post)) {
        pressItems.push(
          convertWpPostToItem(post, 'PRESS')
        );
      } else if (isTopicsPost(post)) {
        topicItems.push(
          convertWpPostToItem(post, 'TOPICS')
        );
      }
    });

    // --- Press Releases 描画 ---
    if (pressListElem) {
      pressListElem.innerHTML = '';
      pressItems.forEach(item => {
        const li = createPressReleaseItem(item);
        pressListElem.appendChild(li);
      });
    }

    // --- Topics 描画 ---
    if (topicsListElem) {
      topicsListElem.innerHTML = '';
      topicItems.forEach(item => {
        const li = createTopicItem(item);
        topicsListElem.appendChild(li);
      });
    }

  } catch (e) {
    console.error('fetch error:', e);
  }
}

// DOM 準備完了後に実行
document.addEventListener('DOMContentLoaded', loadHomeContents);
