<?php
if (!defined('ABSPATH')) exit;

$post_types = get_option('liquid_tools_post_types', []);

// 保存されている投稿タイプがなければデフォルト値で表示
?>
<!-- HTMLフォーム -->
<div id="postTypesContainer">
    <div id="fieldsetsContainer">
        <?php
        if (empty($post_types)) {
            // 初期フィールドセットを表示（デフォルト値で）
            echo liquid_generate_fieldset_html(0, [
                'slug' => '',
                'display_name' => '',
                'menu_position' => '10',
                'menu_icon' => 'dashicons-admin-page',
                'capability' => 'edit_posts'
            ]);
        } else {
            foreach ($post_types as $index => $type) {
                echo liquid_generate_fieldset_html($index, $type);
            }
        }
        ?>
    </div>
    <?php if (!empty($post_types)) { ?>
    <button type="button" id="addFieldSetButton" class="button"><?php esc_html_e('Add Post Type', 'liquid-tools'); ?></button>
    <?php } ?>
</div>

<?php
// フィールドセットのHTML生成関数
function liquid_generate_fieldset_html($index, $type) {
    ob_start();
    // デフォルト値
    $has_archive = isset($type['has_archive']) ? $type['has_archive'] : true;
    $hierarchical = isset($type['hierarchical']) ? $type['hierarchical'] : true;
    $show_in_rest = isset($type['show_in_rest']) ? $type['show_in_rest'] : true;
    $menu_position = isset($type['menu_position']) ? intval($type['menu_position']) : 10;
    $menu_icon = isset($type['menu_icon']) && !empty($type['menu_icon']) ? $type['menu_icon'] : 'dashicons-admin-page';
    $supports = isset($type['supports']) ? $type['supports'] : ['title', 'editor', 'thumbnail', 'revisions'];
    $available_taxonomies = ['', 'category', 'post_tag'];
    $selected_taxonomies = isset($type['taxonomies']) ? $type['taxonomies'] : [];
    ?>
    <details id="fieldset-<?php echo esc_attr($index); ?>" open>
        <summary><?php echo esc_attr($index+1); ?></summary>

        <table class="form-table">
        <tr>
            <th><label><?php esc_html_e('Slug', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][slug]" value="<?php echo esc_attr($type['slug'] ?? ''); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Display Name', 'liquid-tools'); ?></label></th>
            <td><input type="text" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][display_name]" value="<?php echo esc_attr($type['display_name'] ?? ''); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Menu Position', 'liquid-tools'); ?></label></th>
            <td><input type="number" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][menu_position]" value="<?php echo esc_attr($menu_position); ?>" placeholder="<?php esc_attr_e('Post:5 Page:20', 'liquid-tools'); ?>"></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Menu Icon', 'liquid-tools'); ?> <a href="https://developer.wordpress.org/resource/dashicons/#arrow-up-alt" target="_blank"><?php esc_html_e('(List)', 'liquid-tools'); ?></a></label></th>
            <td><input type="text" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][menu_icon]" value="<?php echo esc_attr($menu_icon); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Capability', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][capability]">
                <option value="edit_posts" <?php echo (isset($type['capability']) && $type['capability'] === 'edit_posts') ? 'selected' : ''; ?>>Edit Posts</option>
                <option value="publish_posts" <?php echo (isset($type['capability']) && $type['capability'] === 'publish_posts') ? 'selected' : ''; ?>>Publish Posts</option>
                <option value="manage_categories" <?php echo (isset($type['capability']) && $type['capability'] === 'manage_categories') ? 'selected' : ''; ?>>Manage Categories</option>
                <option value="manage_options" <?php echo (isset($type['capability']) && $type['capability'] === 'manage_options') ? 'selected' : ''; ?>>Manage Options</option>
            </select></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Supports', 'liquid-tools'); ?></label></th>
            <td><div class="labels">
                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][has_archive]" value="true"
                <?php echo $has_archive ? 'checked' : ''; ?>> <?php esc_html_e('Archive', 'liquid-tools'); ?></label>

                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][hierarchical]" value="true"
                <?php echo $hierarchical ? 'checked' : ''; ?>> <?php esc_html_e('Hierarchical', 'liquid-tools'); ?></label>

                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="title" 
                <?php echo in_array('title', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Title', 'liquid-tools'); ?></label>
            
                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="editor" 
                <?php echo in_array('editor', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Editor', 'liquid-tools'); ?></label>
            
                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="thumbnail" 
                <?php echo in_array('thumbnail', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Thumbnail', 'liquid-tools'); ?></label>

                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="revisions" 
                <?php echo in_array('revisions', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Revisions', 'liquid-tools'); ?></label>
            
                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="excerpt" 
                <?php echo in_array('excerpt', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Excerpt', 'liquid-tools'); ?></label>
            
                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][supports][]" value="comments" 
                <?php echo in_array('comments', $supports) ? 'checked' : ''; ?>> <?php esc_html_e('Comments', 'liquid-tools'); ?></label>

                <label><input type="checkbox" name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][show_in_rest]" value="true"
                <?php echo $show_in_rest ? 'checked' : ''; ?>> <?php esc_html_e('REST API', 'liquid-tools'); ?></label>
            </div></td>
        </tr>
        <tr>
            <th><label><?php esc_html_e('Taxonomies', 'liquid-tools'); ?> <?php esc_html_e('(Multiple selections allowed)', 'liquid-tools'); ?></label></th>
            <td><select name="liquid_tools_post_types[<?php echo esc_attr($index); ?>][taxonomies][]" multiple>
                <?php
                foreach ($available_taxonomies as $taxonomy) {
                    $selected = in_array($taxonomy, $selected_taxonomies) ? 'selected' : '';
                    echo '<option value="' . esc_attr($taxonomy) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy) . '</option>';
                }
                ?>
            </select></td>
        </tr>

        </table>

        <!-- 削除用のチェックボックス -->
        <label class="delete"><input type="checkbox" name="delete_post_types[]" value="<?php echo esc_attr($index); ?>"> <?php esc_html_e('Delete this post type', 'liquid-tools'); ?></label>

    </details>
    <?php
    return ob_get_clean();
}
?>