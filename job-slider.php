<?php

/**
 * Plugin Name: Job Slider
 * Description: API-driven job listing slider for WordPress posts
 * Version: 1.0
 * Author: Your Name
 * License: GPL v2 or later
 * 
 * @package JobSlider
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * メインプラグインクラス
 * 
 * プラグインの中核となる機能を提供し、各コンポーネントを統括します
 */
class JobSliderPlugin
{
    /** @var JobSliderPlugin|null シングルトンインスタンス */
    private static $instance = null;

    /** @var JobApiClient APIクライアントインスタンス */
    private $api_client;

    /**
     * シングルトンインスタンスを取得
     * 
     * @return JobSliderPlugin
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     * 各種フックの登録を行います
     */
    private function __construct()
    {
        $this->api_client = new JobApiClient();

        // 基本機能の初期化
        add_action('init', array($this, 'register_post_types'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

        // 保存処理
        add_action('save_post_slider_group', array($this, 'save_slider_group'));
        add_action('save_post', array($this, 'save_post_slider_relation'));

        // AJAX処理
        add_action('wp_ajax_preview_jobs', array($this, 'ajax_preview_jobs'));

        // ショートコード
        add_shortcode('job_slider', array($this, 'slider_shortcode'));

        // 自動挿入
        add_filter('the_content', array($this, 'auto_insert_job_slider'));
    }

    /**
     * カスタム投稿タイプを登録
     */
    public function register_post_types()
    {
        register_post_type('slider_group', array(
            'labels' => array(
                'name' => 'スライダー管理',
                'singular_name' => 'スライダー',
                'add_new' => '新規スライダー',
                'add_new_item' => '新規スライダーを追加',
                'edit_item' => 'スライダーを編集',
                'view_item' => 'スライダーを表示',
                'search_items' => 'スライダーを検索',
                'not_found' => 'スライダーが見つかりませんでした',
                'not_found_in_trash' => 'ゴミ箱にスライダーはありません',
            ),
            'public' => true,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-slides',
            'menu_position' => 20,
            'show_in_rest' => true,
        ));
    }

    /**
     * 管理画面用スクリプトとスタイルを読み込み
     * 
     * @param string $hook 現在の管理画面ページ
     */
    public function admin_scripts($hook)
    {
        $screen = get_current_screen();
        if ($screen->post_type === 'slider_group' || $screen->post_type === 'post') {
            wp_enqueue_style('job-slider-admin', plugins_url('assets/css/admin.css', __FILE__));
            wp_enqueue_script('job-slider-admin', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), '1.0', true);
            wp_localize_script('job-slider-admin', 'jobSliderAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('job_slider_nonce')
            ));
        }
    }

    /**
     * フロントエンド用スクリプトとスタイルを読み込み
     */
    public function frontend_scripts()
    {
        if (is_single() || is_page()) {
            wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
            wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '', true);
            wp_enqueue_style('job-slider', plugins_url('assets/css/job-slider.css', __FILE__));
            wp_enqueue_script('job-slider-front', plugins_url('assets/js/front.js', __FILE__), array('swiper-js'), '1.0', true);
        }
    }

    /**
     * メタボックスを追加
     */
    public function add_meta_boxes()
    {
        // スライダーグループ用メタボックス
        add_meta_box(
            'slider_api_settings',
            'API設定',
            array($this, 'render_api_settings'),
            'slider_group',
            'normal',
            'high'
        );

        // 記事用メタボックス
        add_meta_box(
            'company_slider_relation',
            '求人スライダー設定',
            array($this, 'render_company_slider_relation'),
            'post',
            'side'
        );
    }

    /**
     * API設定フォームを表示
     * 
     * @param WP_Post $post 現在の投稿オブジェクト
     */
    public function render_api_settings($post)
    {
        $template = plugin_dir_path(__FILE__) . 'templates/slider-settings.php';
        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * 記事とスライダーの関連付けフォームを表示
     * 
     * @param WP_Post $post 現在の投稿オブジェクト
     */
    public function render_company_slider_relation($post)
    {
        $template = plugin_dir_path(__FILE__) . 'templates/job-details-form.php';
        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * スライダー設定を保存
     * 
     * @param int $post_id 投稿ID
     */
    public function save_slider_group($post_id)
    {
        if (
            !isset($_POST['slider_api_settings_nonce']) ||
            !wp_verify_nonce($_POST['slider_api_settings_nonce'], 'slider_api_settings_nonce')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'corporation_id' => 'text',
            'company_name' => 'text',
            'display_count' => 'int'
        );

        foreach ($fields as $field => $type) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                switch ($type) {
                    case 'int':
                        $value = intval($value);
                        break;
                    case 'text':
                    default:
                        $value = sanitize_text_field($value);
                        break;
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }

    /**
     * 記事とスライダーの関連付けを保存
     * 
     * @param int $post_id 投稿ID
     */
    public function save_post_slider_relation($post_id)
    {
        if (get_post_type($post_id) !== 'post') {
            return;
        }

        if (
            !isset($_POST['company_slider_nonce']) ||
            !wp_verify_nonce($_POST['company_slider_nonce'], 'company_slider_nonce')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['company_slider_id'])) {
            update_post_meta(
                $post_id,
                '_company_slider_id',
                sanitize_text_field($_POST['company_slider_id'])
            );
        }
    }

    /**
     * AJAX求人プレビュー処理
     */
    public function ajax_preview_jobs()
    {
        check_ajax_referer('job_slider_nonce', 'nonce');

        $corporation_id = isset($_POST['corporation_id'])
            ? sanitize_text_field($_POST['corporation_id'])
            : '21838';

        $jobs_data = $this->api_client->fetch_jobs($corporation_id);

        if (!$jobs_data || empty($jobs_data['data'])) {
            wp_send_json_error('求人データを取得できませんでした');
            return;
        }

        ob_start();
        $this->render_jobs_preview($jobs_data['data']);
        $html = ob_get_clean();

        wp_send_json_success($html);
    }

    /**
     * プレビューのレンダリング
     * 
     * @param array $jobs 求人データ配列
     */
    private function render_jobs_preview($jobs)
    {
        include plugin_dir_path(__FILE__) . 'templates/slider-template.php';
    }

    /**
     * ショートコードの処理
     * 
     * @param array $atts ショートコード属性
     * @return string 生成されたHTML
     */
    public function slider_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => null,
            'corporation_id' => null
        ), $atts);

        if (!$atts['id']) {
            return '<p class="error">スライダーIDを指定してください</p>';
        }

        $slider = get_post($atts['id']);
        if (!$slider || $slider->post_type !== 'slider_group') {
            return '<p class="error">スライダーが見つかりません</p>';
        }

        $corporation_id = $atts['corporation_id'] ?: get_post_meta($slider->ID, '_corporation_id', true);
        $jobs_data = $this->api_client->fetch_jobs($corporation_id);

        if (!$jobs_data || empty($jobs_data['data'])) {
            return '<p class="error">求人データを取得できませんでした</p>';
        }

        ob_start();
        $this->render_jobs_preview($jobs_data['data']);
        return ob_get_clean();
    }

    /**
     * 記事への自動挿入
     * 
     * @param string $content 投稿内容
     * @return string 修正された投稿内容
     */
    public function auto_insert_job_slider($content)
    {
        if (is_single() && get_post_type() === 'post') {
            $slider_id = get_post_meta(get_the_ID(), '_company_slider_id', true);
            if ($slider_id) {
                $content .= do_shortcode('[job_slider id="' . $slider_id . '"]');
            }
        }
        return $content;
    }
}

/**
 * APIクライアントクラス
 */
class JobApiClient
{
    /** @var string API基本URL */
    private $api_url = 'https://marinated-api.aimfactory-system.jp/engineer_factory/public/jobs';

    /**
     * 求人情報を取得
     * 
     * @param string $corporation_id 企業ID
     * @param array $filters 追加フィルター
     * @return array|false 求人データまたはfalse
     */
    public function fetch_jobs($corporation_id = '21838', $filters = array())
    {
        $args = array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json'
            )
        );

        $query_params = array(
            'filter[corporation_id]' => $corporation_id,
            'filter[publishing_status_ef]' => 'open',
            'page[size]' => 10,
            'include[]' => ['skills', 'industries', 'project_job_types', 'prefectures', 'project_features']
        );

        $query_params = array_merge($query_params, $filters);
        $url = add_query_arg($query_params, $this->api_url);

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            error_log('Job API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Job API JSON Error: ' . json_last_error_msg());
            return false;
        }

        return $data;
    }
}

// プラグインの初期化
add_action('plugins_loaded', function () {
    JobSliderPlugin::get_instance();
});

// 有効化・無効化フック
register_activation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
