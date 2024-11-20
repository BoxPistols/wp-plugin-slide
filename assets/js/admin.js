/**
 * 管理画面用JavaScriptファイル
 * プレビューやコピー機能の制御を行います
 * 
 * @package JobSlider
 * @version 1.0.0
 */

jQuery(document).ready(function ($) {
  // プレビュー機能
  $('.preview-jobs').on('click', function () {
    const $button = $(this);
    const $loading = $('.preview-loading');
    const $preview = $('#jobs-preview');
    const corporationId = $('input[name="corporation_id"]').val();

    if (!corporationId) {
      alert('Corporation IDを入力してください');
      return;
    }

    $button.prop('disabled', true);
    $loading.show();
    $preview.html('');

    $.ajax({
      url: jobSliderAdmin.ajaxUrl,
      method: 'POST',
      data: {
        action: 'preview_jobs',
        nonce: jobSliderAdmin.nonce,
        corporation_id: corporationId
      },
      success: function (response) {
        if (response.success) {
          $preview.html(response.data);
        } else {
          $preview.html('<p class="error">エラー: ' + (response.data || '求人データを取得できませんでした') + '</p>');
        }
      },
      error: function (xhr, status, error) {
        $preview.html('<p class="error">APIリクエストに失敗しました: ' + error + '</p>');
      },
      complete: function () {
        $button.prop('disabled', false);
        $loading.hide();
      }
    });
  });

  // ショートコードのコピー機能
  $('.copy-shortcode').on('click', function () {
    const shortcode = $(this).data('shortcode');
    navigator.clipboard.writeText(shortcode).then(function () {
      const $button = $('.copy-shortcode');
      $button.text('コピーしました！');
      setTimeout(function () {
        $button.text('コピー');
      }, 2000);
    }).catch(function (err) {
      alert('コピーに失敗しました: ' + err);
    });
  });

  // フォームバリデーション
  $('form#post').on('submit', function (e) {
    const corporationId = $('input[name="corporation_id"]').val();
    const companyName = $('input[name="company_name"]').val();

    if (!corporationId || !companyName) {
      e.preventDefault();
      alert('企業名とCorporation IDは必須項目です');
      return false;
    }
  });
});