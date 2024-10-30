<?php
if (!defined('ABSPATH')) exit;

$others = get_option('liquid_tools_others', []);
// 公開されている投稿タイプを取得し、'attachment' を除外
$post_types = array_diff(get_post_types(['public' => true], 'names'), ['attachment']);

?>
<!-- HTMLフォーム -->
<div id="othersContainer">
    <div id="fieldsetsContainer">
        <details open>
            <summary><?php esc_html_e('Thumbnail images in the admin list', 'liquid-tools'); ?></summary>

            <table class="form-table">
            <tr>
                <th><label for="post_types"><?php esc_html_e('Select the post type that displays thumbnail images in the admin list', 'liquid-tools'); ?> <?php esc_html_e('(Multiple selections allowed)', 'liquid-tools'); ?></label></th>
                <td><select name="liquid_tools_others[post_types][]" multiple>
                    <?php
                    foreach ($post_types as $post_type) {
                        $selected = in_array($post_type, $others['post_types'] ?? []) ? 'selected' : '';
                        echo '<option value="' . esc_attr($post_type) . '" ' . esc_attr($selected) . '>' . esc_html($post_type) . '</option>';
                    }
                    ?>
                </select></td>
            </tr>
            </table>

        </details>
    </div>
</div>