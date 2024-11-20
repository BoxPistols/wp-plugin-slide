<div class="slider-settings-container">
  <?php if (empty($items)): ?>
    <p class="no-items">求人カードがまだ作成されていません。先に求人カードを作成してください。</p>
  <?php else: ?>
    <div class="slider-items-grid">
      <?php foreach ($items as $item): ?>
        <div class="slider-item-card <?php echo in_array($item->ID, $selected_items) ? 'selected' : ''; ?>">
          <label>
            <input type="checkbox"
              name="slider_items[]"
              value="<?php echo $item->ID; ?>"
              <?php checked(in_array($item->ID, $selected_items)); ?>>
            <?php echo esc_html($item->post_title); ?>
          </label>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($post->post_status !== 'auto-draft'): ?>
    <div class="shortcode-info">
      <p>このスライダーを表示するには、以下のショートコードを記事や固定ページに貼り付けてください：</p>
      <code>[job_slider id="<?php echo $post->ID; ?>"]</code>
      <button type="button" class="button copy-shortcode" data-shortcode='[job_slider id="<?php echo $post->ID; ?>"]'>
        コピー
      </button>
    </div>
  <?php endif; ?>
</div>