<?php
/*
Plugin Name: LIQUID TOOLS
Description: Manage custom post types and taxonomies.
Author: LIQUID DESIGN Ltd.
Author URI: https://lqd.jp/wp/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: liquid-tools
Version: 1.0.2
*/
/*  Copyright 2018 LIQUID DESIGN Ltd. (email : info@lqd.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
*/

if (!defined('ABSPATH')) exit;

// translations
load_plugin_textdomain( 'liquid-tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

// カスタム投稿タイプとタクソノミーを設定
function liquid_tools_register_custom_structures() {
    // post_types
    $post_types = get_option('liquid_tools_post_types', []);
    if (is_array($post_types)) {
        foreach ($post_types as $type) {
            if (is_array($type) && !empty($type['slug']) && strlen($type['slug']) > 0 && strlen($type['slug']) <= 20) {
                $menu_position = isset($type['menu_position']) && is_numeric($type['menu_position']) ? intval($type['menu_position']) : 10;
                $menu_icon = isset($type['menu_icon']) ? $type['menu_icon'] : '';
                $supports = isset($type['supports']) && is_array($type['supports']) ? $type['supports'] : ['title', 'editor', 'thumbnail']; // デフォルトのサポート項目に 'thumbnail' を追加
                $capability = isset($type['capability']) ? $type['capability'] : 'edit_posts'; // デフォルトの権限を設定

                // 個別に処理する
                $has_archive = isset($type['has_archive']) ? (bool) $type['has_archive'] : true;
                $hierarchical = isset($type['hierarchical']) ? (bool) $type['hierarchical'] : true;
                $show_in_rest = isset($type['show_in_rest']) ? (bool) $type['show_in_rest'] : true;

                // 選択されたタクソノミー
                $taxonomies = isset($type['taxonomies']) ? $type['taxonomies'] : [];

                $args = array(
                    'labels' => array(
                        'name' => $type['display_name'],
                        'singular_name' => $type['display_name'],
                        /* translators: %s: post types term */
                        'add_new' => sprintf(__('New %s', 'liquid-tools'), $type['display_name']),
                        /* translators: %s: post types term */
                        'add_new_item' => sprintf(__('Add New %s', 'liquid-tools'), $type['display_name']),
                        /* translators: %s: post types term */
                        'new_item' => sprintf(__('Add New %s', 'liquid-tools'), $type['display_name']),
                        /* translators: %s: post types term */
                        'edit_item' => sprintf(__('Edit %s', 'liquid-tools'), $type['display_name']),
                        /* translators: %s: post types term */
                        'search_items' => sprintf(__('Search %s', 'liquid-tools'), $type['display_name']),
                        /* translators: %s: post types term */
                        'view_item' => sprintf(__('View %s', 'liquid-tools'), $type['display_name']),
                    ),
                    'public' => true,
                    'has_archive' => $has_archive,
                    'supports' => array_filter($supports),
                    'hierarchical' => $hierarchical,
                    'menu_position' => $menu_position,
                    'menu_icon' => $menu_icon,
                    'capabilities' => array( // 特定の権限をマッピング
                        'edit_posts' => $capability,
                        'edit_others_posts' => $capability,
                        'delete_posts' => $capability,
                        'delete_others_posts' => $capability,
                        'publish_posts' => $capability,
                        'read_private_posts' => $capability
                    ),
                    'map_meta_cap' => true, // メタ権限を自動的にマッピング
                    'show_in_rest' => $show_in_rest,
                    'taxonomies' => $taxonomies
                );

                // カスタム投稿タイプを登録
                register_post_type($type['slug'], $args);
            }
        }
    }

    // taxonomies
    $taxonomies = get_option('liquid_tools_taxonomies', []);
    if (is_array($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
            if (isset($taxonomy['slug']) && isset($taxonomy['display_name'])) {
                // 使用するカスタム投稿タイプをフォームから取得
                $post_types = isset($taxonomy['post_types']) && is_array($taxonomy['post_types']) ? $taxonomy['post_types'] : ['post']; // デフォルトは 'post'
                
                // タクソノミーをカスタム投稿タイプに関連付ける
                register_taxonomy($taxonomy['slug'], $post_types, array(
                    'hierarchical' => isset($taxonomy['hierarchical']) ? (bool) $taxonomy['hierarchical'] : true,
                    'labels' => array(
                        'name' => $taxonomy['display_name'],
                        'singular_name' => $taxonomy['display_name']
                    ),
                    'show_ui' => true,
                    'query_var' => true,
                    'rewrite' => array('slug' => $taxonomy['slug']),
                    'show_in_rest' => true, // REST API 有効にする
                ));
            }
        }
    }

}

add_action('init', 'liquid_tools_register_custom_structures', 10);

// 管理画面の設定ページを追加
function liquid_tools_add_admin_menu() {
    // メインメニュー
    add_menu_page(
        esc_html__( 'LIQUID TOOLS Settings', 'liquid-tools' ),
        esc_html__( 'LIQUID TOOLS', 'liquid-tools' ),
        'manage_options',
        'liquid-tools',
        'liquid_tools_settings_page',
        'dashicons-admin-tools'
    );

    // サブメニュー: Custom Post Types
    add_submenu_page(
        'liquid-tools',
        esc_html__( 'Custom Post Types', 'liquid-tools' ),
        esc_html__( 'Custom Post Types', 'liquid-tools' ),
        'manage_options',
        'admin.php?page=liquid-tools&view=post_types'
    );

    // サブメニュー: Custom Taxonomies
    add_submenu_page(
        'liquid-tools',
        esc_html__( 'Custom Taxonomies', 'liquid-tools' ),
        esc_html__( 'Custom Taxonomies', 'liquid-tools' ),
        'manage_options',
        'admin.php?page=liquid-tools&view=taxonomies'
    );

    // サブメニュー: Custom Fields
    add_submenu_page(
        'liquid-tools',
        esc_html__( 'Custom Fields', 'liquid-tools' ),
        esc_html__( 'Custom Fields', 'liquid-tools' ),
        'manage_options',
        'admin.php?page=liquid-tools&view=custom_fields'
    );

    // サブメニュー: Others
    add_submenu_page(
        'liquid-tools',
        esc_html__( 'Others', 'liquid-tools' ),
        esc_html__( 'Others', 'liquid-tools' ),
        'manage_options',
        'admin.php?page=liquid-tools&view=others'
    );
}

// 管理画面の設定ページの内容
function liquid_tools_settings_page() {
    // GETパラメータでviewを確認
    $view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'post_types';
    ?>
    <div class="wrap liquid-tools">
        <h1><?php esc_html_e( 'LIQUID TOOLS Settings', 'liquid-tools' ); ?></h1>
        <!-- タブ -->
        <h2 class="nav-tab-wrapper">
            <a href="?page=liquid-tools&view=post_types" class="nav-tab <?php echo ($view === 'post_types') ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Custom Post Types', 'liquid-tools'); ?></a>
            <a href="?page=liquid-tools&view=taxonomies" class="nav-tab <?php echo ($view === 'taxonomies') ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Custom Taxonomies', 'liquid-tools'); ?></a>
            <a href="?page=liquid-tools&view=custom_fields" class="nav-tab <?php echo ($view === 'custom_fields') ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Custom Fields', 'liquid-tools'); ?></a>
            <a href="?page=liquid-tools&view=others" class="nav-tab <?php echo ($view === 'others') ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Others', 'liquid-tools'); ?></a>
        </h2>

        <!-- 設定フォーム -->
        <form method="post" action="options.php">
            <!-- nonce field -->
            <?php wp_nonce_field('liquid_tools_delete_action', 'liquid_tools_nonce'); ?>
            <?php
            if ($view === 'post_types') {
                settings_fields('liquid_tools_post_types_group');
                do_settings_sections('liquid-tools-post-types');
            } elseif ($view === 'taxonomies') {
                settings_fields('liquid_tools_taxonomies_group');
                do_settings_sections('liquid-tools-taxonomies');
            } elseif ($view === 'custom_fields') {
                settings_fields('liquid_tools_custom_fields_group');
                do_settings_sections('liquid-tools-custom_fields');
            } elseif ($view === 'others') {
                settings_fields('liquid_tools_others_group');
                do_settings_sections('liquid-tools-others');
            }
            submit_button();
            ?>
        </form>
        <hr>
        <p><?php esc_html_e('To make the settings permanent, please "Save" Settings > Permalinks.', 'liquid-tools'); ?></p>
        
    </div>
    <?php
}

// プラグインアクションリンク
function liquid_tools_plugin_action_links( $links ) {
	$mylinks = '<a href="'.admin_url( 'admin.php?page=liquid-tools' ).'">'.esc_html__( 'Settings', 'liquid-tools' ).'</a>';
    array_unshift( $links, $mylinks);
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'liquid_tools_plugin_action_links' );

// サニタイズ処理 カスタム投稿タイプ
function liquid_tools_sanitize_post_types($input) {
    $sanitized = [];

    if (is_array($input)) {
        foreach ($input as $index => $item) {
            // 'slug' と 'display_name' が空かどうかチェック
            $slug = sanitize_text_field($item['slug']);
            $display_name = sanitize_text_field($item['display_name']);

            // 必須フィールドが空の場合
            if (empty(trim($slug)) || empty(trim($display_name))) {
                continue; // ループをスキップ
            }

            // サニタイズされたデータを追加
            $sanitized_item = [
                'slug' => sanitize_text_field($item['slug']),
                'display_name' => sanitize_text_field($item['display_name']),
                'menu_position' => intval($item['menu_position']),
                'menu_icon' => sanitize_text_field($item['menu_icon']),
                'capability' => sanitize_text_field($item['capability']),
                'has_archive' => isset($item['has_archive']) ? filter_var($item['has_archive'], FILTER_VALIDATE_BOOLEAN) : false,
                'hierarchical' => isset($item['hierarchical']) ? filter_var($item['hierarchical'], FILTER_VALIDATE_BOOLEAN) : false,
                'show_in_rest' => isset($item['show_in_rest']) ? filter_var($item['show_in_rest'], FILTER_VALIDATE_BOOLEAN) : false,
            ];

            // 'supports' フィールドが存在するかを確認し、サニタイズ処理
            if (isset($item['supports']) && is_array($item['supports'])) {
                $sanitized_item['supports'] = array_map('sanitize_text_field', $item['supports']);
            } else {
                $sanitized_item['supports'] = []; // 'supports' が空の場合
            }

            if (isset($item['taxonomies']) && is_array($item['taxonomies'])) {
                $sanitized_item['taxonomies'] = array_map('sanitize_text_field', $item['taxonomies']);
            } else {
                $sanitized_item['taxonomies'] = [];
            }

            $sanitized[] = $sanitized_item;
        }
    }

    return $sanitized;
}

// サニタイズ処理 カスタムタクソノミー
function liquid_tools_sanitize_taxonomies($input) {
    $sanitized = [];

    if (is_array($input)) {
        foreach ($input as $index => $item) {
            $slug = sanitize_text_field($item['slug']);
            $display_name = sanitize_text_field($item['display_name']);

            // 必須フィールドが空の場合スキップ
            if (empty(trim($slug)) || empty(trim($display_name))) {
                continue; // ループスキップ
            }

            // サニタイズされたデータを追加
            $sanitized_item = [
                'slug' => $slug,
                'display_name' => $display_name,
                'post_types' => isset($item['post_types']) ? array_map('sanitize_text_field', $item['post_types']) : [],
                'hierarchical' => isset($item['hierarchical']) ? filter_var($item['hierarchical'], FILTER_VALIDATE_BOOLEAN) : true,
            ];

            $sanitized[] = $sanitized_item;
        }
    }

    return $sanitized;
}

// サニタイズ処理 カスタムフィールド
function liquid_tools_sanitize_custom_fields($input) {
    $sanitized = [];

    if (isset($input) && is_array($input)) {
        foreach ($input as $field) {
            // 必須フィールドが空の場合スキップ
            if (empty($field['label']) || empty($field['key_name'])) {
                continue; // ループスキップ
            }

            $sanitized[] = [
                'label'         => sanitize_text_field($field['label']),
                'key_name'      => sanitize_text_field($field['key_name']),
                'type'          => sanitize_text_field($field['type']),
                'post_types'    => isset($field['post_types']) ? array_map('sanitize_text_field', $field['post_types']) : [],
                'description'   => sanitize_text_field($field['description']),
                'display_position' => sanitize_text_field($field['display_position']),
                'priority'      => sanitize_text_field($field['priority']),
                'default_value' => sanitize_text_field($field['default_value']),
                'notes'         => sanitize_text_field($field['notes']),
                'background_color' => isset($field['background_color']) ? sanitize_hex_color($field['background_color']) : '',
                'repeatable'    => isset($field['repeatable']) ? intval($field['repeatable']) : 0,
            ];
        }
    }

    return $sanitized;
}

// サニタイズ処理 その他
function liquid_tools_sanitize_others($input) {
    $sanitized = [];

    // 投稿タイプのサニタイズ
    if (isset($input['post_types']) && is_array($input['post_types'])) {
        // 公開されている投稿タイプを取得し、'attachment' を除外
        $valid_post_types = array_diff(get_post_types(['public' => true], 'names'), ['attachment']);
        
        // サニタイズされた投稿タイプのリストを生成
        $sanitized['post_types'] = array_filter(
            $input['post_types'], 
            function($post_type) use ($valid_post_types) {
                // 有効な投稿タイプのみを返す
                return in_array($post_type, $valid_post_types);
            }
        );
    }

    return $sanitized;
}

// 削除処理
function liquid_tools_delete($new_value, $old_value) {

    // カスタム投稿タイプの削除処理
    if (!empty($_POST['delete_post_types']) && check_admin_referer('liquid_tools_delete_action', 'liquid_tools_nonce')) {
        foreach ($_POST['delete_post_types'] as $delete_index) {
            $delete_index = intval($delete_index);
            if (array_key_exists($delete_index, $old_value)) {
                unset($new_value[$delete_index]);  // 削除処理は $old_value ではなく $new_value に対して行う
            }
        }
        $new_value = array_values($new_value);  // 削除後、インデックスをリセット
    }

    // カスタムタクソノミーの削除処理
    if (!empty($_POST['delete_taxonomies']) && check_admin_referer('liquid_tools_delete_action', 'liquid_tools_nonce')) {
        foreach ($_POST['delete_taxonomies'] as $delete_index) {
            $delete_index = intval($delete_index);
            if (array_key_exists($delete_index, $old_value)) {
                unset($new_value[$delete_index]);  // 削除処理は $old_value ではなく $new_value に対して行う
            }
        }
        $new_value = array_values($new_value);  // 削除後、インデックスをリセット
    }

    // カスタムフィールドの削除処理
    if (!empty($_POST['delete_custom_fields']) && check_admin_referer('liquid_tools_delete_action', 'liquid_tools_nonce')) {
        foreach ($_POST['delete_custom_fields'] as $delete_index) {
            $delete_index = intval($delete_index);
            if (array_key_exists($delete_index, $old_value)) {
                unset($new_value[$delete_index]);  // 削除処理は $old_value ではなく $new_value に対して行う
            }
        }
        $new_value = array_values($new_value);  // 削除後、インデックスをリセット
    }

    return $new_value;  // 削除後は $new_value を返す
}

// 設定項目を登録
function liquid_tools_settings_init() {
    // 削除処理用のフック
    add_filter('pre_update_option_liquid_tools_post_types', 'liquid_tools_delete', 10, 2);
    add_filter('pre_update_option_liquid_tools_taxonomies', 'liquid_tools_delete', 10, 2);
    add_filter('pre_update_option_liquid_tools_custom_fields', 'liquid_tools_delete', 10, 2);

    // カスタム投稿タイプの設定
    register_setting('liquid_tools_post_types_group', 'liquid_tools_post_types', 'liquid_tools_sanitize_post_types');
    add_settings_section(
        'liquid_tools_post_types_section',
        esc_html__('Custom Post Types', 'liquid-tools'),
        'liquid_tools_post_types_section_cb',
        'liquid-tools-post-types'
    );

    // カスタムタクソノミーの設定
    register_setting('liquid_tools_taxonomies_group', 'liquid_tools_taxonomies', 'liquid_tools_sanitize_taxonomies');
    add_settings_section(
        'liquid_tools_taxonomies_section',
        esc_html__('Custom Taxonomies', 'liquid-tools'),
        'liquid_tools_taxonomies_section_cb',
        'liquid-tools-taxonomies'
    );

    // カスタムフィールドの設定
    register_setting('liquid_tools_custom_fields_group', 'liquid_tools_custom_fields', 'liquid_tools_sanitize_custom_fields');
    add_settings_section(
        'liquid_tools_custom_fields_section',
        esc_html__('Custom Fields', 'liquid-tools'),
        'liquid_tools_custom_fields_section_cb',
        'liquid-tools-custom_fields'
    );

    // その他の設定
    register_setting('liquid_tools_others_group', 'liquid_tools_others', 'liquid_tools_sanitize_others');
    add_settings_section(
        'liquid_tools_others_section',
        esc_html__('Others', 'liquid-tools'),
        'liquid_tools_others_section_cb',
        'liquid-tools-others'
    );
    
}

// 設定ファイル読み込み
function liquid_tools_post_types_section_cb() {
    include(plugin_dir_path(__FILE__) . 'inc/post_types.php');
}
function liquid_tools_taxonomies_section_cb() {
    include(plugin_dir_path(__FILE__) . 'inc/taxonomies.php');
}
function liquid_tools_custom_fields_section_cb() {
    include(plugin_dir_path(__FILE__) . 'inc/custom_fields.php');
}
function liquid_tools_others_section_cb() {
    include(plugin_dir_path(__FILE__) . 'inc/others.php');
}

add_action('admin_menu', 'liquid_tools_add_admin_menu');
add_action('admin_init', 'liquid_tools_settings_init');

// スクリプトの読み込み
function liquid_tools_enqueue_scripts($hook) {
    $screen = get_current_screen();
    // すべての投稿タイプ（カスタム投稿タイプを含む）に対してメディアライブラリを読み込む
    if (in_array($screen->post_type, get_post_types(['public' => true]))) {
        wp_enqueue_media();
        wp_enqueue_script('liquid-tools-editor', plugin_dir_url(__FILE__) . 'lib/liquid-tools-editor.js', array('jquery'), null, true);
    }
    // 設定画面の場合
    if ('toplevel_page_liquid-tools' == $hook || 'settings_page_liquid-tools' == $hook) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('liquid-tools-script', plugin_dir_url(__FILE__) . 'lib/liquid-tools.js', array('jquery', 'wp-color-picker'), null, true);
        wp_enqueue_style('liquid-tools-style', plugin_dir_url(__FILE__) . 'lib/liquid-tools.css', array(), null);
    }
}
add_action('admin_enqueue_scripts', 'liquid_tools_enqueue_scripts');

// 通知
function liquid_tools_admin_notices() {
    // 現在のページが 'liquid-tools' のときのみメッセージを表示
    $current_screen = get_current_screen();
    if ($current_screen->id === 'toplevel_page_liquid-tools' && isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        add_settings_error(
            'liquid_tools_messages',
            'liquid_tools_message',
            esc_html__('Settings saved successfully.', 'liquid-tools'), // メッセージ内容
            'updated' 
        );
    }
    // 通知メッセージを表示
    settings_errors('liquid_tools_messages');
}
add_action('admin_notices', 'liquid_tools_admin_notices');

// サムネイルカラムを追加
function liquid_tools_add_thumbnail_column( $columns ) {
    $columns['thumbnail'] = esc_html__('Thumbnail', 'liquid-tools');
    return $columns;
}

// サムネイルカラムの表示処理
function liquid_tools_display_thumbnail_column($column_name, $post_id) {
    if ($column_name === 'thumbnail') {
        // まずカラムを空にする
        $output = ''; 
        // サムネイルがある場合のみ表示
        if (has_post_thumbnail($post_id)) {
            $output = get_the_post_thumbnail($post_id, [70, 70]);
        }else{
            $output = '—';
        }
        echo wp_kses_post($output);
    }
}

// 投稿タイプにカラムを追加する
function liquid_tools_add_custom_columns_to_post_types() {
    $others = get_option('liquid_tools_others', []);
    if (!empty($others['post_types'])) {
        foreach ($others['post_types'] as $post_type) {
            add_filter("manage_{$post_type}_posts_columns", 'liquid_tools_add_thumbnail_column');
            add_action("manage_{$post_type}_posts_custom_column", 'liquid_tools_display_thumbnail_column', 10, 2);
        }
    }
}
add_action('admin_head', 'liquid_tools_add_custom_columns_to_post_types');

// カスタムフィールドを表示する関数を作成
function liquid_tools_display_custom_field($field, $post, $index) {
    // nonce field
    wp_nonce_field('liquid_tools_save_custom_fields_action', 'liquid_tools_nonce');

    // 説明がある場合は表示
    if (!empty($field['description'])) {
        echo '<p>' . esc_html($field['description']) . '</p>';
    }

    $default_value = $field['default_value'] ?? ''; // デフォルト値が設定されていればそれを使用
    if (!empty($default_value)) {
        // 選択肢を ',' 区切りで分解
        $options = explode(',', $default_value);
    }

    $value = get_post_meta($post->ID, $field['key_name'], true) ?: $default_value;

    $input_type = $field['type'];
    $input_name = 'custom_fields[' . esc_attr($field['key_name']) . ']';

    $backgroundColor = !empty($field['background_color']) ? esc_attr($field['background_color']) : '';

    // フィールドの種類によって表示を変更
    switch ($input_type) {
        case 'checkbox':
        case 'radio':
        case 'select':
            if (isset($field['repeatable']) && $field['repeatable']) {
                // 繰り返しフィールド（チェックボックス、ラジオボタン、セレクトボックスの場合）
                $values = is_array($value) ? $value : [$value];
                echo '<div class="liquid-field-container liquid-repeatable-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                foreach ($values as $val) {
                    echo '<div class="liquid-repeatable-group">';
                    if ($input_type === 'checkbox') {
                        foreach ($options as $option) {
                            echo '<label><input type="checkbox" name="' . esc_attr($input_name) . '[]" value="' . esc_attr(trim($option)) . '" ' . (in_array(trim($option), $values) ? 'checked' : '') . '> ' . esc_html(trim($option)) . '</label>&emsp;';
                        }
                    } elseif ($input_type === 'radio') {
                        foreach ($options as $option) {
                            echo '<label><input type="radio" name="' . esc_attr($input_name) . '[' . esc_attr($index) . ']" value="' . esc_attr(trim($option)) . '" ' . (trim($val) === trim($option) ? 'checked' : '') . '> ' . esc_html(trim($option)) . '</label>&emsp;';
                        }
                    } elseif ($input_type === 'select') {
                        echo '<select name="' . esc_attr($input_name) . '[]">';
                        foreach ($options as $option) {
                            echo '<option value="' . esc_attr(trim($option)) . '" ' . (trim($val) === trim($option) ? 'selected' : '') . '>' . esc_html(trim($option)) . '</option>';
                        }
                        echo '</select>';
                    }
                    echo '<button type="button" class="button remove-repeatable">' . esc_html__('Remove', 'liquid-tools') . '</button>';
                    echo '</div>'; // .liquid-repeatable-group
                }
                echo '</div>';
                echo '<button type="button" class="button add-repeatable" style="margin-top:10px;">' . esc_html__('Add Field', 'liquid-tools') . '</button>';
            } else {
                // 単一フィールド（チェックボックス、ラジオボタン、セレクトボックスの場合）
                echo '<div class="liquid-field-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                if ($input_type === 'checkbox') {
                    foreach ($options as $option) {
                        echo '<label><input type="checkbox" name="' . esc_attr($input_name) . '[]" value="' . esc_attr(trim($option)) . '" ' . (in_array(trim($option), (array) $value) ? 'checked' : '') . '> ' . esc_html(trim($option)) . '</label>&emsp;';
                    }
                } elseif ($input_type === 'radio') {
                    foreach ($options as $option) {
                        echo '<label><input type="radio" name="' . esc_attr($input_name) . '" value="' . esc_attr(trim($option)) . '" ' . (trim($value) === trim($option) ? 'checked' : '') . '> ' . esc_html(trim($option)) . '</label>&emsp;';
                    }
                } elseif ($input_type === 'select') {
                    echo '<select name="' . esc_attr($input_name) . '">';
                    foreach ($options as $option) {
                        echo '<option value="' . esc_attr(trim($option)) . '" ' . (trim($value) === trim($option) ? 'selected' : '') . '>' . esc_html(trim($option)) . '</option>';
                    }
                    echo '</select>';
                }
                echo '</div>';
            }
            break;
        case 'textarea':
        case 'text':
            if (isset($field['repeatable']) && $field['repeatable']) {
                // 繰り返しフィールド（テキストまたはテキストエリア）
                $values = is_array($value) ? $value : [$value];
                echo '<div class="liquid-field-container liquid-repeatable-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                foreach ($values as $val) {
                    echo '<div class="liquid-repeatable-group">';
                    if ($input_type === 'text') {
                        echo '<input type="text" name="' . esc_attr($input_name) . '[]" value="' . esc_attr($val) . '" style="width:80%;">';
                    } elseif ($input_type === 'textarea') {
                        echo '<textarea name="' . esc_attr($input_name) . '[]" style="width:80%;">' . esc_textarea($val) . '</textarea>';
                    }
                    echo '<button type="button" class="button remove-repeatable">' . esc_html__('Remove', 'liquid-tools') . '</button>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<button type="button" class="button add-repeatable" style="margin-top:10px;">' . esc_html__('Add Field', 'liquid-tools') . '</button>';
            } else {
                // 単一テキストまたはテキストエリアフィールド
                echo '<div class="liquid-field-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                if ($input_type === 'text') {
                    echo '<input type="text" name="' . esc_attr($input_name) . '" value="' . esc_attr($value) . '" style="width:80%;">';
                } elseif ($input_type === 'textarea') {
                    echo '<textarea name="' . esc_attr($input_name) . '" style="width:80%;">' . esc_textarea($value) . '</textarea>';
                }
                echo '</div>';
            }
            break;
        case 'url':
        case 'email':
        case 'number':
            if (isset($field['repeatable']) && $field['repeatable']) {
                // 繰り返しフィールド
                $values = is_array($value) ? $value : [$value];
                echo '<div class="liquid-field-container liquid-repeatable-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                foreach ($values as $val) {
                    echo '<div class="liquid-repeatable-group">';
                    echo '<input type="' . esc_attr($input_type) . '" name="' . esc_attr($input_name) . '[]" value="' . esc_attr($val) . '" style="width:80%;">';
                    echo '<button type="button" class="button remove-repeatable">' . esc_html__('Remove', 'liquid-tools') . '</button>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<button type="button" class="button add-repeatable" style="margin-top:10px;">' . esc_html__('Add Field', 'liquid-tools') . '</button>';
            } else {
                // 単一フィールド
                echo '<div class="liquid-field-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                echo '<input type="' . esc_attr($input_type) . '" name="' . esc_attr($input_name) . '" value="' . esc_attr($value) . '" style="width:80%;">';
                echo '</div>';
            }
            break;
        case 'image':
            if (isset($field['repeatable']) && $field['repeatable']) {
                // 繰り返し画像フィールド
                $values = is_array($value) ? $value : [$value];
                echo '<div class="liquid-field-container liquid-repeatable-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                foreach ($values as $val) {
                    echo '<div class="liquid-repeatable-group">';
                    echo '<input type="hidden" name="' . esc_attr($input_name) . '[]" value="' . esc_attr($val) . '" class="image-id-field">';
                    echo '<button type="button" class="button select-image-button">' . esc_html__('Select Image', 'liquid-tools') . '</button>';
                    if ($val) {
                        $image_url = wp_get_attachment_url($val);
                        echo '<div class="image-preview"><img src="' . esc_url($image_url) . '" style="max-width:300px;"></div>';
                    } else {
                        echo '<div class="image-preview"><img src="" style="max-width:300px; display:none;"></div>';
                    }
                    echo '<button type="button" class="button remove-repeatable">' . esc_html__('Remove', 'liquid-tools') . '</button>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<button type="button" class="button add-repeatable" style="margin-top:10px;">' . esc_html__('Add Image Field', 'liquid-tools') . '</button>';
            } else {
                // 単一画像フィールド
                echo '<div class="liquid-field-container" data-background-color="' . esc_attr($backgroundColor) . '">';
                echo '<input type="hidden" name="' . esc_attr($input_name) . '" value="' . esc_attr($value) . '" class="image-id-field">';
                echo '<button type="button" class="button select-image-button">' . esc_html__('Select Image', 'liquid-tools') . '</button>';
                if ($value) {
                    $image_url = wp_get_attachment_url($value);
                    echo '<div class="image-preview"><img src="' . esc_url($image_url) . '" style="max-width:300px;"></div>';
                } else {
                    echo '<div class="image-preview"><img src="" style="max-width:300px; display:none;"></div>';
                }
                echo '</div>';
            }
            break;
    }
}
add_action('add_meta_boxes', function() {
    $custom_fields = get_option('liquid_tools_custom_fields', []);

    // カスタムフィールドが設定されている場合
    if (is_array($custom_fields)) {
        // 各カスタムフィールドを確認
        foreach ($custom_fields as $index => $field) {
            if (isset($field['post_types']) && is_array($field['post_types'])) {
                // 表示位置と表示順を取得
                $display_position = $field['display_position'] ?? 'normal';
                $priority = $field['priority'] ?? 'default';

                // フィールドごとの投稿タイプに対してメタボックスを追加
                foreach ($field['post_types'] as $post_type) {
                    add_meta_box(
                        "liquid_custom_field_{$index}", // メタボックスID（フィールドごとに一意）
                        esc_html($field['label']), // タイトル
                        function($post) use ($field, $index) {
                            liquid_tools_display_custom_field($field, $post, $index); // フィールドごとの表示処理
                        },
                        $post_type, // 投稿タイプ
                        $display_position, // 表示位置
                        $priority // 表示順
                    );
                }
            }
        }
    }
});

// カスタムフィールドの保存処理
function liquid_tools_save_custom_fields($post_id) {
    // nonce check
    if (!isset($_POST['liquid_tools_nonce']) || !wp_verify_nonce($_POST['liquid_tools_nonce'], 'liquid_tools_save_custom_fields_action')) {
        return;
    }
    if (isset($_POST['custom_fields'])) {
        $custom_fields = get_option('liquid_tools_custom_fields', []);
        foreach ($custom_fields as $field) {
            if (isset($_POST['custom_fields'][$field['key_name']])) {
                // $sanitized_value に変換
                $value = $_POST['custom_fields'][$field['key_name']];
                if (is_array($value)) {
                    // 空の要素を削除し、配列として保存
                    $sanitized_value = array_filter(array_map('sanitize_text_field', $value), function($val) {
                        return !empty($val);
                    });
                    update_post_meta($post_id, $field['key_name'], $sanitized_value);
                } else {
                    // 単一値のサニタイズと保存
                    $sanitized_value = sanitize_text_field($value);
                    update_post_meta($post_id, $field['key_name'], $sanitized_value);
                }
            }
        }
    }
}
add_action('save_post', 'liquid_tools_save_custom_fields');