<?php
// サイズ用クラスを作る
$pagetitle_size = get_field('pagetitle_size'); // チェックボックス（配列）
$pt_classes = 'c-pageTitle';

if ( $pagetitle_size && in_array('largeタグ', $pagetitle_size, true) ) {
  // 選択肢に入れている値に合わせてここを書き換え
  $pt_classes .= ' --large';
}
?>
<div class="<?php echo esc_attr( $pt_classes ); ?>">
  <div class="c-pageTitle__inner c-inner">
    <h1 class="c-pageTitle__title">
      <?php if ( get_field('pagetitle_sub') ) : ?>
        <small><?php the_field('pagetitle_sub'); ?></small>
      <?php endif; ?>

      <strong>
        <?php
        if ( get_field('pagetitle_main') ) {
          the_field('pagetitle_main');
        } else {
          the_title(); // 未入力なら通常のタイトルをフォールバックに
        }
        ?>
      </strong>
    </h1>

    <?php if ( get_field('pagetitle_summary') ) : ?>
      <div class="c-pageTitle__summary">
        <p><?php the_field('pagetitle_summary'); ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>
