<?php
/*
Plugin Name: Job Slider
Description: APIベースの求人情報スライダー
Version: 1.0.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

class JobSlider
{
    private static $instance = null;
    private $api_client;
    private $settings;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init_hooks();
        $this->init_components();
    }

    private function init_hooks()
    {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('init', [$this, 'register_post_types']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
        add_shortcode('job_slider', [$this, 'render_slider']);
    }

    private function init_components()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-api-client.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-settings.php';

        $this->api_client = new JobSlider_API_Client();
        $this->settings = new JobSlider_Settings();
    }

    public function load_textdomain()
    {
        load_plugin_textdomain(
            'job-slider',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    public function register_post_types()
    {
        register_post_type('job_slider', [
            'labels' => [
                'name' => __('Job Sliders', 'job-slider'),
                'singular_name' => __('Job Slider', 'job-slider'),
                'add_new' => __('Add New', 'job-slider'),
                'add_new_item' => __('Add New Slider', 'job-slider'),
                'edit_item' => __('Edit Slider', 'job-slider'),
                'new_item' => __('New Slider', 'job-slider'),
                'view_item' => __('View Slider', 'job-slider'),
                'search_items' => __('Search Sliders', 'job-slider'),
                'not_found' => __('No sliders found', 'job-slider'),
                'not_found_in_trash' => __('No sliders found in trash', 'job-slider')
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-slides'
        ]);
    }

    public function admin_scripts($hook)
    {
        $screen = get_current_screen();
        if ($screen->post_type !== 'job_slider') {
            return;
        }

        wp_enqueue_style(
            'job-slider-admin',
            plugins_url('assets/css/admin.css', __FILE__),
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'job-slider-admin',
            plugins_url('assets/js/admin.js', __FILE__),
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('job-slider-admin', 'jobSliderAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('job-slider-admin'),
            'strings' => [
                'error' => __('An error occurred', 'job-slider'),
                'success' => __('Settings saved', 'job-slider')
            ]
        ]);
    }

    public function frontend_scripts()
    {
        wp_enqueue_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css'
        );

        wp_enqueue_style(
            'job-slider',
            plugins_url('assets/css/the-slider.css', __FILE__),
            ['swiper'],
            '1.0.0'
        );

        wp_enqueue_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'job-slider',
            plugins_url('assets/js/front.js', __FILE__),
            ['swiper', 'jquery'],
            '1.0.0',
            true
        );
    }

    public function render_slider($atts)
    {
        $atts = shortcode_atts([
            'id' => null,
            'corporation_id' => null
        ], $atts);

        if (!$atts['corporation_id']) {
            return '<p class="error">' . __('Corporation ID is required', 'job-slider') . '</p>';
        }

        $jobs = $this->api_client->fetch_jobs($atts['corporation_id']);
        if (is_wp_error($jobs)) {
            return '<p class="error">' . $jobs->get_error_message() . '</p>';
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/slider-template.php';
        return ob_get_clean();
    }
}

// Initialize the plugin
function job_slider_init()
{
    JobSlider::get_instance();
}
add_action('plugins_loaded', 'job_slider_init');

// Activation/Deactivation hooks
register_activation_hook(__FILE__, function () {
    // Activation tasks
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    // Cleanup tasks
    flush_rewrite_rules();
});
