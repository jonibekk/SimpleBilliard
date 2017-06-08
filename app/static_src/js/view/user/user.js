"use strict";

$(function () {
    // TODO: Remove console log
    console.log("LOADING: user.js");

    // アップロードしたカバー画像選択時にリサイズして表示
    $('.fileinput_cover').fileinput().on('change.bs.fileinput', function () {
        var $input = $(this).find('input[type=file]');
        if (!$input.prop('files') || $input.prop('files').length == 0) {
            return;
        }
        var file = $input.prop('files')[0];
        var $preview = $(this).find('.fileinput-preview');
        resizeImgBase64(file.result, 672, 378,
            function (img_b64) {
                $preview.removeClass('mod-no-image');
                $preview.css('line-height', '');
                $preview.html('<img class="profile-setting-cover-image" src="' + img_b64 + '">')
            }
        );
    });
});

/**
 * base64の画像をリサイズ
 */
function resizeImgBase64(imgBase64, width, height, callback) {
    // TODO: Remove console log
    console.log("user.js: resizeImgBase64");
    // Image Type
    var img_type = imgBase64.substring(5, imgBase64.indexOf(";"));
    // Source Image
    var img = new Image();
    img.onload = function () {
        // New Canvas
        var canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        // Draw (Resize)
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);
        // Destination Image
        var imgB64_dst = canvas.toDataURL(img_type);
        callback(imgB64_dst);
    };
    img.src = imgBase64;
}

