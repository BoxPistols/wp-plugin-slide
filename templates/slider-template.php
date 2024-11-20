<?php

/**
 * Job Slider テンプレート
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;
?>

<div class="job-slider-container" id="job-slider-<?php echo esc_attr($atts['id']); ?>">
  <?php if (empty($jobs['data'])): ?>
    <div class="job-slider-error">
      <?php _e('求人情報が見つかりませんでした。', 'job-slider'); ?>
    </div>
  <?php else: ?>
    <div class="swiper job-slider">
      <div class="swiper-wrapper">
        <?php foreach ($jobs['data'] as $job):
          $attributes = $job['attributes'];
          $relationships = $job['relationships'];
        ?>
          <div class="swiper-slide">
            <div class="job-card">
              <!-- カードヘッダー -->
              <div class="job-card-header">
                <h3 class="job-title">
                  <?php echo esc_html($attributes['name']); ?>
                </h3>

                <!-- 特徴タグ -->
                <?php if (!empty($relationships['project_features']['data'])): ?>
                  <div class="job-tags">
                    <?php foreach ($relationships['project_features']['data'] as $feature):
                      $feature_label = $this->get_tag_label($feature['id'], $jobs['included']);
                    ?>
                      <span class="job-tag">
                        <?php echo esc_html($feature_label); ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- メタ情報 -->
              <div class="job-meta">
                <?php if (!empty($attributes['wage_max'])): ?>
                  <div class="job-meta-item">
                    <i class="dashicons dashicons-money-alt"></i>
                    <span class="job-salary">
                      月額 ~<?php echo esc_html($attributes['wage_max']); ?>万円
                    </span>
                  </div>
                <?php endif; ?>

                <?php if (!empty($attributes['nearest_stations'])): ?>
                  <div class="job-meta-item">
                    <i class="dashicons dashicons-location"></i>
                    <span class="job-location">
                      <?php echo esc_html($attributes['nearest_stations']); ?>
                    </span>
                  </div>
                <?php endif; ?>

                <?php if (!empty($relationships['project_job_types']['data'])):
                  $job_type = $this->get_tag_label($relationships['project_job_types']['data'][0]['id'], $jobs['included']);
                ?>
                  <div class="job-meta-item">
                    <i class="dashicons dashicons-businessman"></i>
                    <span class="job-type">
                      <?php echo esc_html($job_type); ?>
                    </span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- 職務内容 -->
              <?php if (!empty($attributes['duties'])): ?>
                <div class="job-description">
                  <?php echo wp_kses_post(nl2br($attributes['duties'])); ?>
                </div>
              <?php endif; ?>

              <!-- スキル要件 -->
              <?php if (!empty($relationships['skills']['data'])): ?>
                <div class="job-skills">
                  <?php foreach ($relationships['skills']['data'] as $skill):
                    $skill_label = $this->get_tag_label($skill['id'], $jobs['included']);
                  ?>
                    <span class="job-skill" data-tooltip="<?php echo esc_attr($skill_label); ?>">
                      <?php echo esc_html($skill_label); ?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- 詳細リンク -->
              <a href="<?php echo esc_url('https://www.engineer-factory.com/freelance/jobs/' . $job['id']); ?>"
                class="job-link"
                target="_blank"
                rel="noopener noreferrer">
                <?php _e('詳細を見る', 'job-slider'); ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- ナビゲーションボタン -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>

    <!-- ページネーション -->
    <?php if (!empty($jobs['meta']) && $jobs['meta']['total_pages'] > 1): ?>
      <div class="job-slider-pagination">
        <button class="load-more"
          data-page="2"
          data-corporation="<?php echo esc_attr($atts['corporation_id']); ?>">
          <?php _e('もっと見る', 'job-slider'); ?>
        </button>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>