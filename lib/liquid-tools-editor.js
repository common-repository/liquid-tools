/*
Author: LIQUID DESIGN Ltd.
Author URI: https://lqd.jp/wp/
*/
// For Editor
jQuery(document).ready(function($) {
    // 画像選択ボタンがクリックされたら
    $(document).on('click', '.select-image-button', function(e) {
        e.preventDefault();
        var button = $(this); // クリックされたボタン
        var imageIdField = button.siblings('.image-id-field'); // 画像IDを保持するフィールド
        var imagePreview = button.siblings('.image-preview').find('img'); // プレビュー用の画像タグ

        var custom_uploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false  // 複数選択を無効にする
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            // hidden inputに画像IDをセット
            imageIdField.val(attachment.id);

            // プレビュー画像を更新して表示
            imagePreview.attr('src', attachment.url).show();
        })
        .open();
    });

    // ページが読み込まれたときに、最初のフィールドの削除ボタンを非表示にする
    $('.liquid-repeatable-container').each(function() {
        $(this).find('.liquid-repeatable-group:first .remove-repeatable').hide(); // 削除ボタンは非表示
    });

    // 繰り返しフィールドの追加ボタンがクリックされたら
    $(document).on('click', '.add-repeatable', function() {
        var container = $(this).prev('.liquid-repeatable-container');
        var firstField = container.children('.liquid-repeatable-group:first').clone(); // 最初のフィールドをクローン
            
        // クローンしたフィールド内の値をクリアする
        firstField.find('input').val(''); // テキストフィールドをクリア
        firstField.find('textarea').val(''); // テキストエリアフィールドをクリア
        firstField.find('.image-preview img').attr('src', '').hide(); // プレビュー画像をクリア
    
        // 削除ボタンはクローンされたフィールドに残す
        firstField.find('.remove-repeatable').show(); // クローンしたフィールドには削除ボタンを表示させる
    
        // 新しいフィールドを追加
        container.append(firstField);
    });

    // 繰り返しフィールドの削除ボタンがクリックされたら
    $(document).on('click', '.remove-repeatable', function() {
        $(this).closest('.liquid-repeatable-group').remove();
    });

    // 背景色
    $('.liquid-field-container').each(function() {
        var backgroundColor = $(this).data('background-color');
        if (backgroundColor) {
            $(this).closest('.postbox').css('background-color', backgroundColor);
        }
    });
});
