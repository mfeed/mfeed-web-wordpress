<sidebar class="col-md-3 blog-sidebar">
      

<div class="mf-menu border border-bottom-0">

<div class="sidebar-module sidebar-module-inset slide-list p-0">
  <div class="sidebar-head red d-flex align-items-end justify-content-between">
    <h5 class="sidebar-headline mx-3 mb-2 text-nowrap font-plq">
      ARCHIVES
    </h5>
    <i class="trapezoid"></i>
  </div>

  <ul class="sidebar-module-list pl-0">
    <?php
      // 年別アーカイブを取得
      $years = wp_get_archives([
        'type'            => 'yearly',
        'format'          => 'custom',
        'before'          => '',
        'after'           => '',
        'echo'            => 0,
        'show_post_count' => false
      ]);

      // aタグ単位で分割して1行ずつ整形し直す
      preg_match_all('/<a .*?<\/a>/', $years, $matches);

     if (!empty($matches[0])) :
    foreach ($matches[0] as $link) :

      // 年を抽出
      preg_match('/>([0-9]{4})</', $link, $year_match);
      $year = $year_match[1];
         
    ?>
    <a class="border-bottom m-0 d-flex justify-content-between sidebar-cell"
   href="<?php echo esc_url( home_url( '/archives/' . $year . '/' ) ); ?>">
  <p class="mb-0 pl-4 text-color"><?php echo esc_html($year); ?></p>
  <i class="fas fa-chevron-right mr-3 text-color"></i>
</a>
    <?php
        endforeach;
      endif;
    ?>
  </ul>
</div>


</div>

    </sidebar>