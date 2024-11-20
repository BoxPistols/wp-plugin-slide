/**
 * Job Slider 管理画面 JavaScript
 * v1.0.0
 */

(function ($) {
  'use strict';

  // 管理画面の初期化
  function initAdmin() {
    const $form = $('#job-slider-settings');
    const $preview = $('#preview-container');
    const $loading = $('.preview-loading');

    // プレビューボタンのイベント
    $('#preview-jobs').on('click', function (e) {
      e.preventDefault();
      loadPreview();
    });

    // 設定の保存
    $form.on('submit', function (e) {
      e.preventDefault();
      saveSettings();
    });

    // ショートコードのコピー
    $('.copy-shortcode').on('click', function () {
      copyShortcode($(this));
    });

    // タブの切り替え
    $('.job-slider-tab').on('click', function (e) {
      e.preventDefault();
      switchTab($(this));
    });
  }

  // プレビューの読み込み
  function loadPreview() {
    const $button = $('#preview-jobs');
    const $preview = $('#preview-container');
    const $loading = $('.preview-loading');

    $button.prop('disabled', true);
    $loading.show();
    $preview.html('');

    $.ajax({
      url: jobSliderAdmin.ajaxUrl,
      method: 'POST',
      data: {
        action: 'job_slider_preview',
        settings: $('#job-slider-settings').serialize(),
        nonce: jobSliderAdmin.nonce
      },
      success: function (response) {
        if (response.success) {
          $preview.html(response.data);
          initializePreviewSlider();
        } else {
          showError(response.data.message || jobSliderAdmin.strings.error);
        }
      },
      error: function () {
        showError(jobSliderAdmin.strings.error);
      },
      complete: function () {
        $button.prop('disabled', false);
        $loading.hide();
      }
    });
  }

  // 設定の保存
  function saveSettings() {
    const $form = $('#job-slider-settings');
    const $submit = $form.find('input[type="submit"]');

    $submit.prop('disabled', true);

    $.ajax({
      url: jobSliderAdmin.ajaxUrl,
      method: 'POST',
      data: {
        action: 'job_slider_save_settings',
        settings: $form.serialize(),
        nonce: jobSliderAdmin.nonce
      },
      success: function (response) {
        if (response.success) {
          showSuccess(jobSliderAdmin.strings.success);
        } else {
          showError(response.data.message || jobSliderAdmin.strings.error);
        }
      },
      error: function () {
        showError(jobSliderAdmin.strings.error);
      },
      complete: function () {
        $submit.prop('disabled', false);
      }
    });
  }

  // ショートコードのコピー
  function copyShortcode($button) {
    const shortcode = $button.data('shortcode');
    const $temp = $('<input>');

    $('body').append($temp);
    $temp.val(shortcode).select();

    try {
      document.execCommand('copy');
      $button.text(jobSliderAdmin.strings.copied);
      setTimeout(() => {
        $button.text(jobSliderAdmin.strings.copy);
      }, 2000);
    } catch (err) {
      console.error('Copy failed:', err);
    }

    $temp.remove();
  }

  // タブの切り替え
  function switchTab($tab) {
    const target = $tab.data('target');

    $('.job-slider-tab').removeClass('active');
    $('.tab-content').hide();

    $tab.addClass('active');
    $(target).show();
  }

  // エラーメッセージの表示
  function showError(message) {
    const $error = $('<div>')
      .addClass('job-slider-error')
      .text(message);

    $('#message-container').html($error);

    setTimeout(() => {
      $error.fadeOut(300, function () {
        $(this).remove();
      });
    }, 3000);
  }

  // 成功メッセージの表示
  function showSuccess(message) {
    const $success = $('<div>')
      .addClass('job-slider-success')
      .text(message);

    $('#message-container').html($success);

    setTimeout(() => {
      $success.fadeOut(300, function () {
        $(this).remove();
      });
    }, 3000);
  }

  // DOM準備完了時の処理
  $(document).ready(function () {
    initAdmin();
  });

})(jQuery);