<?php
if (!defined('ABSPATH')) exit;

$taxonomies = get_option('liquid_tools_taxonomies', []);

// 保存されているカスタム投稿タイプを取得
$custom_post_types = get_option('liquid_tools_post_types', []);
$available_post_types = [];

// カスタム投稿タイプのスラッグを抽出
if (is_array($custom_post_types)) {
    foreach ($custom_post_types as $post_type) {
        if (isset($post_type['slug'])) {
            $available_post_types[] = $post_type['slug'];  // スラッグを選択肢に追加
        }
    }
}

// デフォルトの 'post' や 'page' も追加
$available_post_types = array_merge($available_post_types, ['post', 'page']);
?>
<!-- HTMLフォーム -->
<div id="taxonomiesContainer">
    <div id="fieldsetsContainer">
        <?php
        if (empty($taxonomies)) {
            // 初期フィールドセットを表示（デフォルト値で）
            echo liquid_generate_fieldset_html(0, [
                'slug' => '',
                'display_name' => '',
                'post_types' => [],
                'hierarchical' => true,
            ], $available_post_types);
        } else {
            foreach ($taxonomies as $index => $taxonomy) {
                echo liquid_generate_fieldset_html($index, $taxonomy, $available_post_types);
            }
        }
        ?>
    </div>

    <?php if (!empty($taxonomies)) { ?>
    <button type="button" id="addFieldSetButton" class="button"><?php esc_html_e('Add Taxonomy', 'liquid-tools'); ?></button>
    <?php } ?>

</div>
<?php
// フィールドセットのHTML生成関数
function liquid_generate_fieldset_html($index, $taxonomy, $available_post_types) {

    // デフォルト値
    $hierarchical = isset($taxonomy['hierarchical']) ? $taxonomy['hierarchical'] : true;
    $post_types = isset($taxonomy['post_types']) ? $taxonomy['post_types'] : [];

    ob_start();
    ?>
    <details id="fieldset-<?php echo esc_attr($index); ?>" open>
        <summary><?php echo esc_attr($index+1); ?></summary>

        <table class="form-table">
        <tr>
            <th><label><?php esc_html_e('Slug', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_taxonomies[<?php echo esc_attr($index); ?>][slug]" value="<?php echo esc_attr($taxonomy['slug'] ?? ''); ?>" class="regular-text" required></td>
        <tr>
        </tr>
            <th><label><?php esc_html_e('Display Name', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_taxonomies[<?php echo esc_attr($index); ?>][display_name]" value="<?php echo esc_attr($taxonomy['display_name'] ?? ''); ?>" class="regular-text" required></td>
        <tr>
        </tr>
            <th><label><?php esc_html_e('Apply to Post Types', 'liquid-tools'); ?> <?php esc_html_e('(Multiple selections allowed)', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_taxonomies[<?php echo esc_attr($index); ?>][post_types][]" multiple>
                <?php
                foreach ($available_post_types as $post_type) {
                    $selected = in_array($post_type, $post_types) ? 'selected' : '';
                    echo '<option value="' . esc_attr($post_type) . '" ' . esc_attr($selected) . '>' . esc_html($post_type) . '</option>';
                }
                ?>
            </select></td>
        </tr>
        </table>

        <!-- 階層 -->
        <label><input type="checkbox" name="liquid_tools_taxonomies[<?php echo esc_attr($index); ?>][hierarchical]" value="true"
        <?php echo $hierarchical ? 'checked' : ''; ?>> <?php esc_html_e('Hierarchical', 'liquid-tools'); ?></label>

        <!-- 削除用のチェックボックス -->
        <label class="delete"><input type="checkbox" name="delete_taxonomies[]" value="<?php echo esc_attr($index); ?>"> <?php esc_html_e('Delete this taxonomies', 'liquid-tools'); ?></label>
    </details>
    <?php
    return ob_get_clean();
}
?>