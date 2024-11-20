<?php

/**
 * 求人詳細フォームテンプレート
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

// 保存されているメタデータを取得
$meta = get_post_meta($post->ID);
?>

<div class="job-details-form">
  <?php wp_nonce_field('job_slider_details', 'job_slider_details_nonce'); ?>

  <div class="job-slider-field">
    <label for="corporation_id"><?php _e('企業ID', 'job-slider'); ?></label>
    <input type="text"
      id="corporation_id"
      name="corporation_id"
      value="<?php echo esc_attr($meta['_corporation_id'][0] ?? ''); ?>"
      class="widefat">
    <p class="description">
      <?php _e('求人情報を取得する企業IDを入力してください。', 'job-slider'); ?>
    </p>
  </div>

  <div class="job-slider-field">
    <label for="display_count"><?php _e('表示件数', 'job-slider'); ?></label>
    <input type="number"
      id="display_count"
      name="display_count"
      value="<?php echo esc_attr($meta['_display_count'][0] ?? '10'); ?>"
      min="1"
      max="100"
      class="small-text">
    <p class="description">
      <?php _e('一度に表示する求人の数を設定します。', 'job-slider'); ?>
    </p>
  </div>

  <div class="job-slider-field">
    <label>
      <input type="checkbox"
        name="enable_pagination"
        value="1"
        <?php checked(!empty($meta['_enable_pagination'][0])); ?>>
      <?php _e('ページネーションを有効にする', 'job-slider'); ?>
    </label>
    <p class="description">
      <?php _e('チェックすると「もっと見る」ボタンが表示されます。', 'job-slider'); ?>
    </p>
  </div>

  <div class="job-slider-field">
    <label for="cache_duration"><?php _e('キャッシュ期間', 'job-slider'); ?></label>
    <input type="number"
      id="cache_duration"
      name="cache_duration"
      value="<?php echo esc_attr($meta['_cache_duration'][0] ?? '3600'); ?>"
      class="small-text">
    <p class="description">
      <?php _e('APIレスポンスをキャッシュする期間（秒）を設定します。', 'job-slider'); ?>
    </p>
  </div>
</div>