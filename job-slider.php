<?php
/*
Plugin Name: Job Slider
Description: Custom job listing slider for WordPress posts
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

class JobSliderPlugin
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // 初期化フック
        add_action('init', array($this, 'register_post_types'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_slider_item', array($this, 'save_slider_item'));
        add_action('save_post_slider_group', array($this, 'save_slider_group'));
        add_shortcode('job_slider', array($this, 'slider_shortcode'));
    }

    // テンプレート読み込みヘルパー
    private function get_template($template_name)
    {
        $template_path = plugin_dir_path(__FILE__) . 'templates/' . $template_name;
        if (!file_exists($template_path)) {
            error_log('Template not found: ' . $template_path);
            return false;
        }
        return $template_path;
    }

    // カスタム投稿タイプの登録
    public function register_post_types()
    {
        // スライダーグループ
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

        // 求人アイテム
        register_post_type('slider_item', array(
            'labels' => array(
                'name' => '求人カード',
                'singular_name' => '求人カード',
                'add_new' => '新規求人',
                'add_new_item' => '新規求人を追加',
                'edit_item' => '求人を編集',
                'view_item' => '求人を表示',
                'search_items' => '求人を検索',
                'not_found' => '求人が見つかりませんでした',
                'not_found_in_trash' => 'ゴミ箱に求人はありません',
            ),
            'public' => true,
            'supports' => array('title', 'editor'),
            'show_in_menu' => 'edit.php?post_type=slider_group',
            'show_in_rest' => true,
        ));
    }

    // 管理画面用スクリプトとスタイル
    public function admin_scripts($hook)
    {
        $screen = get_current_screen();
        if ($screen->post_type === 'slider_item' || $screen->post_type === 'slider_group') {
            wp_enqueue_style('job-slider-admin', plugins_url('assets/css/admin.css', __FILE__));
            wp_enqueue_script('job-slider-admin', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), '1.0', true);
        }
    }

    // フロントエンド用スクリプトとスタイル
    public function frontend_scripts()
    {
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '', true);
        wp_enqueue_style('job-slider', plugins_url('assets/css/job-slider.css', __FILE__));
        // ファイル名を変更
        wp_enqueue_script('job-slider-front', plugins_url('assets/js/front.js', __FILE__), array('swiper-js'), '1.0', true);
    }

    // メタボックスの追加
    public function add_meta_boxes()
    {
        add_meta_box(
            'job_details',
            '求人情報',
            array($this, 'render_job_details'),
            'slider_item',
            'normal',
            'high'
        );

        add_meta_box(
            'slider_settings',
            'スライダー設定',
            array($this, 'render_slider_settings'),
            'slider_group',
            'normal',
            'high'
        );
    }

    // 求人詳細フォームの表示
    public function render_job_details($post)
    {
        wp_nonce_field('job_details_nonce', 'job_details_nonce');

        $fields = array(
            'salary' => array(
                'label' => '給与',
                'type' => 'text',
                'placeholder' => '例: 500-700万円'
            ),
            'location' => array(
                'label' => '勤務地',
                'type' => 'text',
                'placeholder' => '例: 東京都渋谷区'
            ),
            'employment_type' => array(
                'label' => '雇用形態',
                'type' => 'select',
                'options' => array(
                    '正社員' => '正社員',
                    '契約社員' => '契約社員',
                    'フリーランス' => 'フリーランス'
                )
            ),
            'skills' => array(
                'label' => '必要スキル',
                'type' => 'text',
                'placeholder' => '例: PHP, JavaScript, AWS'
            ),
            'experience' => array(
                'label' => '必要経験',
                'type' => 'text',
                'placeholder' => '例: 3年以上'
            ),
            'url' => array(
                'label' => '求人URL',
                'type' => 'url',
                'placeholder' => 'https://...'
            )
        );

        $template = $this->get_template('job-details-form.php');
        if ($template) {
            include $template;
        }
    }

    // スライダー設定フォームの表示
    public function render_slider_settings($post)
    {
        wp_nonce_field('slider_settings_nonce', 'slider_settings_nonce');

        $selected_items = get_post_meta($post->ID, '_slider_items', true) ?: array();
        $items = get_posts(array(
            'post_type' => 'slider_item',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        $template = $this->get_template('slider-settings.php');
        if ($template) {
            include $template;
        }
    }

    // 求人詳細の保存
    public function save_slider_item($post_id)
    {
        if (
            !isset($_POST['job_details_nonce']) ||
            !wp_verify_nonce($_POST['job_details_nonce'], 'job_details_nonce')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array('salary', 'location', 'employment_type', 'skills', 'experience', 'url');

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                if ($field === 'url') {
                    $value = esc_url_raw($value);
                } else {
                    $value = sanitize_text_field($value);
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }

    // スライダー設定の保存
    public function save_slider_group($post_id)
    {
        if (
            !isset($_POST['slider_settings_nonce']) ||
            !wp_verify_nonce($_POST['slider_settings_nonce'], 'slider_settings_nonce')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $slider_items = isset($_POST['slider_items']) ? array_map('intval', $_POST['slider_items']) : array();
        update_post_meta($post_id, '_slider_items', $slider_items);
    }

    // ショートコードの処理
    public function slider_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);

        if (!$atts['id']) {
            return '<p class="error">スライダーIDを指定してください</p>';
        }

        $slider = get_post($atts['id']);
        if (!$slider || $slider->post_type !== 'slider_group') {
            return '<p class="error">指定されたスライダーが見つかりません</p>';
        }

        $selected_items = get_post_meta($slider->ID, '_slider_items', true);
        if (!is_array($selected_items) || empty($selected_items)) {
            return '<p class="error">求人が設定されていません</p>';
        }

        $jobs = get_posts(array(
            'post_type' => 'slider_item',
            'post__in' => $selected_items,
            'orderby' => 'post__in',
            'posts_per_page' => -1
        ));

        ob_start();
        $template = $this->get_template('slider-template.php');
        if ($template) {
            include $template;
        }
        return ob_get_clean();
    }

    // プラグイン有効化時の処理
    public static function activate()
    {
        // パーマリンク構造の更新
        flush_rewrite_rules();
    }

    // プラグイン無効化時の処理
    public static function deactivate()
    {
        // パーマリンク構造の更新
        flush_rewrite_rules();
    }
}

// プラグインの初期化
function init_job_slider()
{
    JobSliderPlugin::get_instance();
}
add_action('plugins_loaded', 'init_job_slider');

// 有効化・無効化フック1
register_activation_hook(__FILE__, array('JobSliderPlugin', 'activate'));
register_deactivation_hook(__FILE__, array('JobSliderPlugin', 'deactivate'));
