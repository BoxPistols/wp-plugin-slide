<?php

/**
 * API Client Class
 * 
 * @package JobSlider
 */

if (!defined('ABSPATH')) exit;

class JobSlider_API_Client
{
  private $api_url = 'https://marinated-api.aimfactory-system.jp/engineer_factory/public/jobs';
  private $cache_group = 'job_slider_cache';

  /**
   * 求人情報を取得
   */
  public function fetch_jobs($corporation_id, $page = 1)
  {
    // キャッシュキーの生成
    $cache_key = "jobs_{$corporation_id}_p{$page}";

    // キャッシュがあれば返す
    $cached = wp_cache_get($cache_key, $this->cache_group);
    if ($cached !== false) {
      return $cached;
    }

    // APIパラメータの設定
    $params = apply_filters('job_slider_api_params', [
      'filter[corporation_id]' => $corporation_id,
      'filter[publishing_status_ef]' => 'open',
      'page[size]' => 10,
      'include[]' => [
        'skills',
        'industries',
        'project_job_types',
        'prefectures',
        'project_features'
      ],
      'page[number]' => $page
    ]);

    // APIリクエスト
    $response = wp_remote_get(add_query_arg($params, $this->api_url), [
      'timeout' => 30,
      'headers' => [
        'Accept' => 'application/json'
      ]
    ]);

    // エラーチェック
    if (is_wp_error($response)) {
      error_log('Job Slider API Error: ' . $response->get_error_message());
      return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      error_log('Job Slider JSON Error: ' . json_last_error_msg());
      return false;
    }

    // キャッシュに保存
    wp_cache_set($cache_key, $data, $this->cache_group, 3600);

    return $data;
  }

  /**
   * キャッシュをクリア
   */
  public function clear_cache($corporation_id = null)
  {
    if ($corporation_id) {
      wp_cache_delete("jobs_{$corporation_id}", $this->cache_group);
    } else {
      wp_cache_flush();
    }
  }
}
