<div class="job-form-container">
  <?php foreach ($fields as $key => $field): ?>
    <div class="job-form-field">
      <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?>:</label>

      <?php if ($field['type'] === 'select'): ?>
        <select id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>">
          <?php foreach ($field['options'] as $value => $label): ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($meta["_{$key}"][0] ?? '', $value); ?>>
              <?php echo esc_html($label); ?>
            </option>
          <?php endforeach; ?>
        </select>
      <?php else: ?>
        <input type="<?php echo esc_attr($field['type']); ?>"
          id="<?php echo esc_attr($key); ?>"
          name="<?php echo esc_attr($key); ?>"
          value="<?php echo esc_attr($meta["_{$key}"][0] ?? ''); ?>"
          placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>">
      <?php endif; ?>

      <?php if (isset($field['help'])): ?>
        <p class="job-form-help"><?php echo esc_html($field['help']); ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>