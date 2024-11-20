<?php

/**
 * 記事用スライダー選択フォームテンプレート
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

wp_nonce_field('company_slider_nonce', 'company_slider_nonce');
$selected_slider = get_post_meta($post->ID, '_company_slider_id', true);

// スライダーグループの一覧を取得
$sliders = get_posts([
  'post_type' => 'slider_group',
  'posts_per_page' => -1,
  'orderby' => 'title',
  'order' => 'ASC'
]);
?>

<div class="slider-relation-form">
  <p>
    <label for="company_slider_id">企業の求人スライダーを選択:</label>
    <select name="company_slider_id" id="company_slider_id" class="widefat">
      <option value="">選択してください</option>
      <?php foreach ($sliders as $slider):
        $company_name = get_post_meta($slider->ID, '_company_name', true);
        $display_name = $company_name ? $company_name . ' - ' . $slider->post_title : $slider->post_title;
      ?>
        <option value="<?php echo $slider->ID; ?>" <?php selected($selected_slider, $slider->ID); ?>>
          <?php echo esc_html($display_name); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>

  <?php if (empty($sliders)): ?>
    <p class="description">
      <a href="<?php echo admin_url('post-new.php?post_type=slider_group'); ?>">
        先に求人スライダーを作成してください
      </a>
    </p>
  <?php endif; ?>

  <?php if ($selected_slider): ?>
    <p class="description">
      <a href="<?php echo get_edit_post_link($selected_slider); ?>" target="_blank">
        選択中のスライダーを編集
      </a>
    </p>
  <?php endif; ?>
</div>