console.log('[faq-feedback] loaded');


document.addEventListener('click', async (e) => {
     console.log('[faq-feedback] click', e.target);
  const btn = e.target.closest('.c-helpfulLink');
  console.log('[faq-feedback] btn', btn);
  if (!btn) return;

  const section = btn.closest('section[data-faq-post-id]');
  console.log('[faq-feedback] section', section);
  if (!section) return;

  const postId = section.getAttribute('data-faq-post-id');
  const pageUrl = section.getAttribute('data-faq-url') || location.href;
  const choice = btn.getAttribute('data-choice');

  // 連打防止
  if (section.dataset.sending === '1') return;
  section.dataset.sending = '1';

  try {
    const res = await fetch(FaqFeedback.endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': FaqFeedback.nonce,
      },
      body: JSON.stringify({
        post_id: Number(postId),
        choice,
        page_url: pageUrl,
      }),
    });

    const json = await res.json();
    if (json.ok) {
      section.querySelector('.c-helpful__contents').innerHTML =
        '<p class="c-helpful__thanks">送信ありがとうございました。</p>';
    } else {
      alert('送信に失敗しました。');
      section.dataset.sending = '0';
    }
  } catch (err) {
    alert('通信エラーが発生しました。');
    section.dataset.sending = '0';
  }
});
