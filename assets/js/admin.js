jQuery(document).ready(function ($) {
  // スライダー項目カードの選択処理
  $('.slider-item-card').on('click', function () {
    $(this).toggleClass('selected');
    const checkbox = $(this).find('input[type="checkbox"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
  });

  // チェックボックスクリック時の伝播を止める
  $('.slider-item-card input[type="checkbox"]').on('click', function (e) {
    e.stopPropagation();
    $(this).closest('.slider-item-card').toggleClass('selected');
  });

  // ショートコードのコピー機能
  $('.copy-shortcode').on('click', function () {
    const shortcode = $(this).data('shortcode');
    navigator.clipboard.writeText(shortcode).then(function () {
      alert('ショートコードをコピーしました');
    });
  });

  // 求人情報フォームのライブプレビュー
  $('.job-form-field input, .job-form-field select, .job-form-field textarea').on('input', function () {
    updatePreview();
  });

  function updatePreview() {
    // プレビューの更新処理
    const title = $('#title').val();
    const salary = $('#salary').val();
    const location = $('#location').val();

    $('.preview-card h3').text(title);
    $('.preview-salary').text(salary);
    $('.preview-location').text(location);
  }
});