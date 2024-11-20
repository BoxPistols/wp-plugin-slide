<?php

/**
 * スライダー設定テンプレート
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

wp_nonce_field('slider_api_settings_nonce', 'slider_api_settings_nonce');

$corporation_id = get_post_meta($post->ID, '_corporation_id', true) ?: '21838';
$company_name = get_post_meta($post->ID, '_company_name', true) ?: '';
$display_count = get_post_meta($post->ID, '_display_count', true) ?: 10;
?>

<div class="api-settings-container">
  <div class="api-settings-form">
    <p>
      <label for="company_name">企業名:</label>
      <input type="text"
        id="company_name"
        name="company_name"
        value="<?php echo esc_attr($company_name); ?>"
        class="widefat">
      <span class="description">表示用の企業名を入力してください</span>
    </p>

    <p>
      <label for="corporation_id">Corporation ID:</label>
      <input type="text"
        id="corporation_id"
        name="corporation_id"
        value="<?php echo esc_attr($corporation_id); ?>"
        class="widefat">
      <span class="description">APIから提供された企業IDを入力してください</span>
    </p>

    <p>
      <label for="display_count">表示件数:</label>
      <input type="number"
        id="display_count"
        name="display_count"
        value="<?php echo esc_attr($display_count); ?>"
        min="1"
        max="50"
        class="small-text">
      <span class="description">一度に表示する求人数を指定してください（最大50件）</span>
    </p>
  </div>

  <div class="api-preview-section">
    <button type="button" class="button button-primary preview-jobs">
      求人情報をプレビュー
    </button>
    <div class="preview-loading" style="display: none;">
      <span class="spinner is-active"></span> 読み込み中...
    </div>
    <div id="jobs-preview" class="jobs-preview-container"></div>
  </div>

  <?php if ($post->post_status !== 'auto-draft'): ?>
    <div class="shortcode-info">
      <p>このスライダーを表示するには、以下のショートコードを使用してください：</p>
      <code>[job_slider id="<?php echo $post->ID; ?>"]</code>
      <button type="button" class="button copy-shortcode" data-shortcode='[job_slider id="<?php echo $post->ID; ?>"]'>
        コピー
      </button>
    </div>
  <?php endif; ?>
</div>