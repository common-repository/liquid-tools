/*
Author: LIQUID DESIGN Ltd.
Author URI: https://lqd.jp/wp/
*/
// For Admin
jQuery(document).ready(function($) {
    // フィールドセットを追加
    function addFieldSet(containerId) {
        var $container = $('#' + containerId);
        var $template = $container.children('details').last(); // 最後の details 要素をクローン元として選択

        if ($template.length === 0 || $container.length === 0) {
            return;
        }

        // クローンを作成
        var $clone = $template.clone();
        var fieldsetCount = $container.children('details').length;

        // 名前のインデックスを現在のフィールドセット数に置き換える
        $clone.find('input, select').each(function() {
            var nameAttr = $(this).attr('name');
            if (nameAttr) {
                // [0][slug] のような部分の 0 を現在の fieldsetCount に置き換える
                var updatedName = nameAttr.replace(/\[\d+\]/, '[' + fieldsetCount + ']');
                $(this).attr('name', updatedName);

                // input text フィールドの値をクリア
                if ($(this).is('input[type="text"]')) {
                    $(this).val('');  // テキストフィールドの値をクリア
                }
            }
        });

        // 削除ボタンの value 属性を更新
        $clone.find('.delete input[type="checkbox"]').val(fieldsetCount);

        // カラーピッカーの初期化済み要素を削除し、元の input 要素を追加
        $clone.find('.wp-picker-container').each(function() {
            // カラーピッカーの初期化前の input 要素を作成
            var originalInputHtml = '<input type="text" name="liquid_tools_custom_fields[' + fieldsetCount + '][background_color]" value="" class="color-picker">';
            
            // wp-picker-container の直前に input 要素を挿入
            $(this).before(originalInputHtml);
            
            // 初期化済みのカラーピッカー要素を削除
            $(this).remove();
        });

        // フィールドセットの番号を更新
        $clone.find('summary').text(fieldsetCount + 1);

        // コンテナにクローンを追加
        $container.append($clone);

        // クローンした要素内のカラーピッカーを初期化
        $clone.find('.color-picker').wpColorPicker();

    }

    // カラーピッカーの初期化
    $('.color-picker').wpColorPicker();

    // ボタンクリックでフィールドセットを追加
    $('#addFieldSetButton').on('click', function() {
        addFieldSet('fieldsetsContainer');
        // ボタンを1回しか押せないようにする（無効化）
        $(this).prop('disabled', true);
        // カラーピッカーの再初期化
        $('.color-picker').wpColorPicker();
    });
});