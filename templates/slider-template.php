<div class="swiper job-slider" id="slider-<?php echo esc_attr($atts['id']); ?>">
  <div class="swiper-wrapper">
    <?php
    // 重複を防ぐために一意の投稿のみを取得
    $unique_jobs = array_unique($jobs, SORT_REGULAR);

    foreach ($unique_jobs as $job):
      $meta = array(
        'salary' => get_post_meta($job->ID, '_salary', true),
        'location' => get_post_meta($job->ID, '_location', true),
        'employment_type' => get_post_meta($job->ID, '_employment_type', true),
        'skills' => get_post_meta($job->ID, '_skills', true),
        'url' => get_post_meta($job->ID, '_url', true)
      );
    ?>
      <div class="swiper-slide">
        <div class="job-card">
          <h3><?php echo esc_html($job->post_title); ?></h3>

          <div class="job-meta">
            <?php if ($meta['salary']): ?>
              <div class="job-meta-item">
                <i class="dashicons dashicons-money-alt"></i>
                <?php echo esc_html($meta['salary']); ?>
              </div>
            <?php endif; ?>

            <?php if ($meta['location']): ?>
              <div class="job-meta-item">
                <i class="dashicons dashicons-location"></i>
                <?php echo esc_html($meta['location']); ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="job-content">
            <?php echo wp_kses_post($job->post_content); ?>
          </div>

          <?php if ($meta['url']): ?>
            <a href="<?php echo esc_url($meta['url']); ?>" class="job-link" target="_blank">
              詳細を見る
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($unique_jobs) > 3): // スライド可能な場合のみナビゲーションを表示 
  ?>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  <?php endif; ?>
</div>