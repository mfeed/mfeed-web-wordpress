 <!-- サイドバー：ARCHIVES（年別リスト） -->
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
            global $wpdb;

            // english_press の公開年を重複なしで取得
            $years = $wpdb->get_col("
              SELECT DISTINCT YEAR(post_date)
              FROM {$wpdb->posts}
              WHERE post_type = 'english_press'
                AND post_status = 'publish'
              ORDER BY post_date DESC
            ");

            if ( ! empty( $years ) ) :
              foreach ( $years as $year ) :
                $year = (int) $year;
                // /en/archives/2025/ のようなURLを生成
                $year_link = home_url( '/en/archives/' . $year . '/' );
                ?>
                <a class="border-bottom m-0 d-flex justify-content-between sidebar-cell"
                   href="<?php echo esc_url( $year_link ); ?>">
                  <p class="mb-0 pl-4 text-color">
                    <?php echo esc_html( $year ); ?>
                  </p>
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