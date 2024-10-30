<?php
if (!defined('ABSPATH')) exit;

// カスタムフィールドと選択された投稿タイプを取得
$custom_fields = get_option('liquid_tools_custom_fields', []);
// 投稿タイプの一覧
$post_types = array_diff(get_post_types(['public' => true], 'names'), ['attachment']);
// 表示位置の選択肢を定義
$display_positions = [
    'advanced' => esc_attr__('Advanced (Below Content)', 'liquid-tools'),
    'normal' => esc_attr__('Normal (Main Content)', 'liquid-tools'),
    'side' => esc_attr__('Side (Sidebar)', 'liquid-tools')
];
// 表示順（優先度）の選択肢を定義
$priority_options = [
    'default' => esc_attr__('Default', 'liquid-tools'),
    'high' => esc_attr__('High', 'liquid-tools'),
    'core' => esc_attr__('Core', 'liquid-tools'),
    'low' => esc_attr__('Low', 'liquid-tools')
];
?>
<div id="customfieldsContainer">
    <div id="fieldsetsContainer">
        <?php
        // フィールドが存在しない場合だけ、初期フィールドセットを表示
        if (empty($custom_fields)) {
            echo liquid_generate_fieldset_html(0, [
                'label' => '',
                'key_name' => '',
                'type' => 'text', // デフォルトはテキストフィールド
                'description' => '',
                'display_position' => 'advanced',
                'priority' => 'default',
                'default_value' => '',
                'notes' => '',
                'repeatable' => '0',
                'post_types' => []
            ], $post_types, $display_positions, $priority_options);
        } else {
            // 保存されたカスタムフィールドを表示
            foreach ($custom_fields as $index => $type) {
                echo liquid_generate_fieldset_html($index, $type, $post_types, $display_positions, $priority_options);
            }
        }
        ?>
    </div>
    <button type="button" id="addFieldSetButton" class="button"><?php esc_html_e('Add custom field', 'liquid-tools'); ?></button>
</div>

<?php
// フィールドセットのHTML生成関数
function liquid_generate_fieldset_html($index, $type, $post_types, $display_positions, $priority_options) {
    ob_start();
    // デフォルト値
    $field_type = isset($type['type']) ? $type['type'] : 'text';
    ?>
    <details id="fieldset-<?php echo esc_attr($index); ?>" open>
        <summary><?php echo esc_attr($index+1); ?></summary>

        <table class="form-table">
        <tr>
            <th><label><?php esc_html_e('Label', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($type['label']) ?? ''; ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Key', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][key_name]" value="<?php echo esc_attr($type['key_name']) ?? ''; ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Content Type', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][type]" class="field-type">
                <option value="text" <?php selected(esc_attr($field_type), 'text'); ?>><?php esc_html_e('Text', 'liquid-tools'); ?></option>
                <option value="textarea" <?php selected(esc_attr($field_type), 'textarea'); ?>><?php esc_html_e('Textarea', 'liquid-tools'); ?></option>
                <option value="url" <?php selected(esc_attr($field_type), 'url'); ?>><?php esc_html_e('URL', 'liquid-tools'); ?></option>
                <option value="email" <?php selected(esc_attr($field_type), 'email'); ?>><?php esc_html_e('Email', 'liquid-tools'); ?></option>
                <option value="number" <?php selected(esc_attr($field_type), 'number'); ?>><?php esc_html_e('Number', 'liquid-tools'); ?></option>
                <option value="image" <?php selected(esc_attr($field_type), 'image'); ?>><?php esc_html_e('Image', 'liquid-tools'); ?></option>
                <option value="checkbox" <?php selected(esc_attr($field_type), 'checkbox'); ?>><?php esc_html_e('Checkbox', 'liquid-tools'); ?></option>
                <option value="radio" <?php selected(esc_attr($field_type), 'radio'); ?>><?php esc_html_e('Radio', 'liquid-tools'); ?></option>
                <option value="select" <?php selected(esc_attr($field_type), 'select'); ?>><?php esc_html_e('Select', 'liquid-tools'); ?></option>
            </select></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Default Value', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][default_value]" value="<?php echo esc_attr($type['default_value'] ?? ''); ?>" class="regular-text">
            <p class="description"><?php esc_html_e('If the type is checkbox, radio, or select, you can define choices separated by “,”', 'liquid-tools'); ?></p></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Description', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][description]" value="<?php echo esc_attr($type['description'] ?? ''); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Note (For admin screen)', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][notes]" value="<?php echo esc_attr($type['notes'] ?? ''); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Display Position', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][display_position]">
                <?php foreach ($display_positions as $position => $label): ?>
                    <option value="<?php echo esc_attr($position); ?>" <?php selected($type['display_position'] ?? 'advanced', $position); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Priority', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][priority]">
                <?php foreach ($priority_options as $priority_value => $priority_label): ?>
                    <option value="<?php echo esc_attr($priority_value); ?>" <?php selected($type['priority'] ?? 'default', $priority_value); ?>>
                        <?php echo esc_html($priority_label); ?>
                    </option>
                <?php endforeach; ?>
            </select></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Apply to Post Types', 'liquid-tools'); ?> <?php esc_html_e('(Multiple selections allowed)', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][post_types][]" multiple>
                <?php foreach ($post_types as $post_type): ?>
                    <option value="<?php echo esc_attr($post_type); ?>" <?php echo in_array($post_type, $type['post_types'] ?? []) ? 'selected' : ''; ?>>
                        <?php echo esc_html($post_type); ?>
                    </option>
                <?php endforeach; ?>
            </select></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Field Background Color', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][background_color]" value="<?php echo esc_attr($type['background_color'] ?? ''); ?>" class="color-picker"></td>
        </tr>
        </table>

        <!-- 繰り返しオプション -->
        <label><input type="checkbox" name="liquid_tools_custom_fields[<?php echo esc_attr($index); ?>][repeatable]" value="1" <?php checked($type['repeatable'] ?? 0, 1); ?>> <?php esc_html_e('Enable Repeatable Field', 'liquid-tools'); ?></label>

        <!-- 削除用のチェックボックス -->
        <label class="delete"><input type="checkbox" name="delete_custom_fields[]" value="<?php echo esc_attr($index); ?>"> <?php esc_html_e('Delete this custom field', 'liquid-tools'); ?></label>
    </details>

    <?php
    return ob_get_clean();
}
?>