<?php

/**
 * Settings Class
 * 
 * @package JobSlider
 */

if (!defined('ABSPATH')) exit;

class JobSlider_Settings
{
  private $option_name = 'job_slider_settings';
  private $page_title = 'Job Slider Settings';
  private $menu_title = 'Job Slider';
  private $capability = 'manage_options';
  private $menu_slug = 'job-slider-settings';

  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_settings_page']);
    add_action('admin_init', [$this, 'register_settings']);
  }

  /**
   * 設定ページを追加
   */
  public function add_settings_page()
  {
    add_options_page(
      $this->page_title,
      $this->menu_title,
      $this->capability,
      $this->menu_slug,
      [$this, 'render_settings_page']
    );
  }

  /**
   * 設定を登録
   */
  public function register_settings()
  {
    register_setting($this->option_name, $this->option_name, [
      'type' => 'array',
      'sanitize_callback' => [$this, 'sanitize_settings']
    ]);

    add_settings_section(
      'general',
      __('General Settings', 'job-slider'),
      null,
      $this->menu_slug
    );

    add_settings_field(
      'cards_per_page',
      __('Cards per page', 'job-slider'),
      [$this, 'render_number_field'],
      $this->menu_slug,
      'general',
      ['field' => 'cards_per_page', 'default' => 10]
    );

    add_settings_field(
      'cache_duration',
      __('Cache Duration (seconds)', 'job-slider'),
      [$this, 'render_number_field'],
      $this->menu_slug,
      'general',
      ['field' => 'cache_duration', 'default' => 3600]
    );
  }

  /**
   * 設定ページを表示
   */
  public function render_settings_page()
  {
    if (!current_user_can($this->capability)) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include plugin_dir_path(dirname(__FILE__)) . 'templates/slider-settings.php';
  }

  /**
   * 数値フィールドを表示
   */
  public function render_number_field($args)
  {
    $options = get_option($this->option_name);
    $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
?>
    <input type="number"
      name="<?php echo esc_attr("{$this->option_name}[{$args['field']}]"); ?>"
      value="<?php echo esc_attr($value); ?>"
      min="1">
<?php
  }

  /**
   * 設定値をサニタイズ
   */
  public function sanitize_settings($input)
  {
    $sanitized = [];

    if (isset($input['cards_per_page'])) {
      $sanitized['cards_per_page'] = absint($input['cards_per_page']);
    }

    if (isset($input['cache_duration'])) {
      $sanitized['cache_duration'] = absint($input['cache_duration']);
    }

    return $sanitized;
  }

  /**
   * 設定値を取得
   */
  public function get_setting($key, $default = null)
  {
    $options = get_option($this->option_name);
    return isset($options[$key]) ? $options[$key] : $default;
  }
}
