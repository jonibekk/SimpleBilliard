"use strict";

$(function () {
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

    // 投稿フォーム submit 時
    $(document).on('submit', '#PostDisplayForm', function (e) {
        return checkUploadFileExpire('PostDisplayForm');
    });

    // リカバリコード再生成
    $(document).on('click', '#RecoveryCodeModal .regenerate-recovery-code', function (e) {
        e.preventDefault();
        var $form = $('#RegenerateRecoveryCodeForm');
        $.ajax({
            url: cake.url.regenerate_recovery_code,
            type: 'POST',
            dataType: 'json',
            processData: false,
            data: $form.serialize()
        })
            .done(function (res) {
                if (res.error) {
                    new Noty({
                        type: 'error',
                        text: '<h4>'+cake.word.error+'</h4>'+res.msg,
                    }).show();
                    return;
                }
                else {
                    var $list_items = $('#RecoveryCodeList').find('li');
                    for (var i = 0; i < 10; i++) {
                        $list_items.eq(i).text(res.codes[i].slice(0, 4) + ' ' + res.codes[i].slice(-4));
                    }
                    new Noty({
                        type: 'success',
                        text: '<h4>'+cake.word.success+'</h4>'+res.msg,
                    }).show();
                }


            })
            .fail(function () {
                new Noty({
                    type: 'error',
                    text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.d,
                }).show();
            });
    });
    $(document).on("click", '#ShowRecoveryCodeButton', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $modal_elm.remove();
        });
        var url = $(this).attr('href');
        $.get(url, function (data) {
            $modal_elm.append(data);
            // ２段階認証設定後、自動で modal を開いた場合は背景クリックで閉じれないようにする
            $modal_elm.modal({
                backdrop: e.isTrigger ? 'static' : true
            });
        }).done(function () {
            $('body').addClass('modal-open');
        });
    });

    //Load term goal
    $('#LoadTermGoal').change(function () {
        var term_id = $(this).val();
        if (term_id == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/term_id:" + term_id;
        }
        location.href = url;
    });
});

/**
 * base64の画像をリサイズ
 */
function resizeImgBase64(imgBase64, width, height, callback) {
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

