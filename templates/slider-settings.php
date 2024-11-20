<?php

/**
 * スライダー設定画面テンプレート
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

// 現在の設定を取得
$options = get_option('job_slider_settings', [
  'cards_per_page' => 10,
  'enable_pagination' => true,
  'cache_duration' => 3600,
  'default_corporation_id' => ''
]);
?>

<div class="job-slider-admin">
  <!-- メッセージ表示エリア -->
  <div id="message-container"></div>

  <!-- 設定フォーム -->
  <form id="job-slider-settings" method="post" action="options.php">
    <?php settings_fields('job_slider_options'); ?>

    <!-- タブナビゲーション -->
    <div class="job-slider-tabs">
      <a href="#" class="job-slider-tab active" data-target="#general-settings">
        <?php _e('一般設定', 'job-slider'); ?>
      </a>
      <a href="#" class="job-slider-tab" data-target="#display-settings">
        <?php _e('表示設定', 'job-slider'); ?>
      </a>
      <a href="#" class="job-slider-tab" data-target="#advanced-settings">
        <?php _e('高度な設定', 'job-slider'); ?>
      </a>
    </div>

    <!-- 一般設定 -->
    <div id="general-settings" class="tab-content">
      <div class="job-slider-settings">
        <div class="job-slider-field">
          <label for="default_corporation_id">
            <?php _e('デフォルト企業ID', 'job-slider'); ?>
          </label>
          <input type="text"
            id="default_corporation_id"
            name="job_slider_settings[default_corporation_id]"
            value="<?php echo esc_attr($options['default_corporation_id']); ?>">
          <p class="description">
            <?php _e('デフォルトで表示する企業IDを設定します。', 'job-slider'); ?>
          </p>
        </div>
      </div>
    </div>

    <!-- 表示設定 -->
    <div id="display-settings" class="tab-content" style="display: none;">
      <div class="job-slider-settings">
        <div class="job-slider-field">
          <label for="cards_per_page">
            <?php _e('表示件数', 'job-slider'); ?>
          </label>
          <input type="number"
            id="cards_per_page"
            name="job_slider_settings[cards_per_page]"
            value="<?php echo esc_attr($options['cards_per_page']); ?>"
            min="1"
            max="100">
          <p class="description">
            <?php _e('1ページあたりの表示件数を設定します（1-100）。', 'job-slider'); ?>
          </p>
        </div>

        <div class="job-slider-field">
          <label>
            <input type="checkbox"
              name="job_slider_settings[enable_pagination]"
              <?php checked($options['enable_pagination']); ?>>
            <?php _e('ページネーションを有効にする', 'job-slider'); ?>
          </label>
          <p class="description">
            <?php _e('「もっと見る」ボタンを表示して追加読み込みを可能にします。', 'job-slider'); ?>
          </p>
        </div>
      </div>
    </div>

    <!-- 高度な設定 -->
    <div id="advanced-settings" class="tab-content" style="display: none;">
      <div class="job-slider-settings">
        <div class="job-slider-field">
          <label for="cache_duration">
            <?php _e('キャッシュ期間', 'job-slider'); ?>
          </label>
          <input type="number"
            id="cache_duration"
            name="job_slider_settings[cache_duration]"
            value="<?php echo esc_attr($options['cache_duration']); ?>"
            min="0">
          <p class="description">
            <?php _e('APIレスポンスのキャッシュ期間を秒単位で設定します。0で無効化。', 'job-slider'); ?>
          </p>
        </div>
      </div>
    </div>

    <!-- 保存ボタン -->
    <p class="submit">
      <?php submit_button(__('設定を保存', 'job-slider')); ?>
    </p>
  </form>

  <!-- プレビュー -->
  <div class="job-slider-preview">
    <div class="preview-header">
      <h3><?php _e('プレビュー', 'job-slider'); ?></h3>
      <button id="preview-jobs" class="preview-jobs">
        <?php _e('プレビューを更新', 'job-slider'); ?>
      </button>
    </div>
    <div class="preview-loading" style="display: none;">
      <span class="spinner is-active"></span>
      <?php _e('読み込み中...', 'job-slider'); ?>
    </div>
    <div id="preview-container"></div>
  </div>

  <!-- ショートコード -->
  <div class="shortcode-info">
    <h3><?php _e('ショートコード', 'job-slider'); ?></h3>
    <p><?php _e('以下のショートコードを記事や固定ページに貼り付けてください：', 'job-slider'); ?></p>
    <div class="shortcode-display">
      <code>[job_slider id="<?php echo get_the_ID(); ?>"]</code>
      <button class="copy-shortcode" data-shortcode='[job_slider id="<?php echo get_the_ID(); ?>"]'>
        <?php _e('コピー', 'job-slider'); ?>
      </button>
    </div>
  </div>
</div>