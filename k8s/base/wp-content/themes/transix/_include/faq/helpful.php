<section class="l-main__section --lightgray"
  data-faq-post-id="<?php echo esc_attr(get_the_ID()); ?>"
  data-faq-url="<?php echo esc_url( get_permalink() ); ?>">
  <div class="c-inner">
    <dl class="c-helpful">
      <dt class="c-helpful__label">この内容は役に立ちましたか？</dt>
      <dd class="c-helpful__contents">
        <button class="c-helpfulLink --01" data-choice="solved"><span>解決した</span></button>
        <button class="c-helpfulLink --02" data-choice="solved_but_unclear"><span>解決したが<br>分かりにくかった</span></button>
        <button class="c-helpfulLink --03" data-choice="not_solved"><span>解決しなかった</span></button>
        <button class="c-helpfulLink --04" data-choice="not_found"><span>探した内容では<br>なかった</span></button>
      </dd>
    </dl>
  </div>
</section>
