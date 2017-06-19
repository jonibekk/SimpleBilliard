/**
 * This file contains script related to comments on posts
 */
"use strict";

$(function () {
    require.config({
        baseUrl: '/js/modules/'
    });

    $(document).on("click", ".click-get-ajax-form-replace", getAjaxFormReplaceElm);
    $(document).on("click", ".click-comment-new", evCommentLatestView);
    $(document).on("click", ".js-click-comment-delete", evCommentDelete);
    $(document).on("click", ".js-click-comment-confirm-delete", evCommentDeleteConfirm);
    $(document).on("click", '[id*="CommentEditSubmit_"]', evCommendEditSubmit);

    $(document).on("click", ".notify-click-target", evNotifyPost);
    $(document).on("click", ".click-comment-all", evCommentOldView);
    $(document).on("click", ".target-toggle-click", evTargetToggleClick);
    bindCommentBalancedGallery($('.comment_gallery'));

    // コメント
    bindCtrlEnterAction('.comment-form', function (e) {
        $(this).find('.comment-submit-button').trigger('click');
    });
});

/**
 * Show form and submit button when user click to add new comment
 */
function getAjaxFormReplaceElm() {
    attrUndefinedCheck(this, 'replace-elm-parent-id');
    attrUndefinedCheck(this, 'click-target-id');
    attrUndefinedCheck(this, 'tmp-target-height');
    attrUndefinedCheck(this, 'ajax-url');
    var $obj = $(this);
    // 非表示状態の時は何もしない
    if (!$obj.is(':visible')) {
        return;
    }
    var replace_elm_parent_id = sanitize($obj.attr("replace-elm-parent-id"));
    var replace_elm = $('#' + replace_elm_parent_id);
    var click_target_id = sanitize($obj.attr("click-target-id"));
    var ajax_url = $obj.attr("ajax-url");
    var tmp_target_height = sanitize($obj.attr("tmp-target-height"));
    var post_id = sanitize($obj.attr("post-id"));
    replace_elm.children().toggle();
    replace_elm.height(tmp_target_height + "px");
    //noinspection JSJQueryEfficiency
    $.ajax({
        url: ajax_url,
        async: false,
        success: function (data) {
            //noinspection JSUnresolvedVariable
            if (data.error) {
                //noinspection JSUnresolvedVariable
                alert(data.msg);
            }
            else {
                replace_elm.css("height", "");
                replace_elm.append(data.html);
                replace_elm.children("form").bootstrapValidator().on('success.form.bv', function (e) {
                    // アップロードファイルの有効期限が切れていなければコメント投稿
                    var res = checkUploadFileExpire($(this).attr('id'));
                    if (res) {
                        validatorCallback(e)
                    }
                    return res;
                });
                $('#' + click_target_id).trigger('click').focus();

                var $uploadFileForm = $(document).data('uploadFileForm');

                // コメントフォームをドラッグ＆ドロップ対象エリアにする
                var commentParams = {
                    formID: function () {
                        return $(this).attr('data-form-id');
                    },
                    previewContainerID: function () {
                        return $(this).attr('data-preview-container-id');
                    },
                    beforeSending: function () {
                        if ($uploadFileForm._sending) {
                            return;
                        }
                        $uploadFileForm._sending = true;
                        // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
                        $('#CommentSubmit_' + post_id).on('click', $uploadFileForm._forbitSubmit);
                    },
                    afterQueueComplete: function () {
                        $uploadFileForm._sending = false;
                        // フォームをsubmit可能にする
                        $('#CommentSubmit_' + post_id).off('click', $uploadFileForm._forbitSubmit);
                    },
                    afterError: function (file) {
                        var $preview = $(file.previewTemplate);
                        // エラーと確認出来るように失敗したファイルの名前を強調して少しの間表示しておく
                        $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.error + ')');
                        setTimeout(function () {
                            $preview.remove();
                        }, 4000);
                    }
                };
                $uploadFileForm.registerDragDropArea('#CommentBlock_' + post_id, commentParams);
                $uploadFileForm.registerAttachFileButton('#CommentUploadFileButton_' + post_id, commentParams);

                // OGP 情報を取得してプレビューする処理
                require(['ogp'], function (ogp) {
                    var onKeyUp = function () {
                        ogp.getOGPSiteInfo({
                            // URL が含まれるテキスト
                            text: $('#CommentFormBody_' + post_id).val(),

                            // ogp 情報を取得する必要があるかチェック
                            readyLoading: function () {
                                // 既に OGP 情報を取得している場合は終了
                                if ($('#CommentSiteInfoUrl_' + post_id).val()) {
                                    return false;
                                }
                                return true;
                            },

                            // ogp 情報取得成功時
                            success: function (data) {
                                var $siteInfoUrl = $('#CommentSiteInfoUrl_' + post_id);
                                var $siteInfo = $('#CommentOgpSiteInfo_' + post_id);
                                $siteInfo
                                    // プレビュー用 HTML
                                    .html(data.html)
                                    // プレビュー削除ボタンを重ねて表示
                                    .prepend($('<a>').attr('href', '#')
                                        .addClass('font_lightgray comment-ogp-close')
                                        .append('<i class="fa fa-times"></i>')
                                        .on('click', function (e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            $siteInfoUrl.val('');
                                            $siteInfo.empty();
                                        }))
                                    // プレビュー削除ボタンの表示スペースを作る
                                    .find('.site-info').css({
                                        "padding-right": "30px"
                                    });

                                // hidden に URL 追加
                                $siteInfoUrl.val(data.url);
                            },

                            // ogp 情報 取得失敗時
                            error: function () {
                                // loading アイコン削除
                                $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
                            },

                            // ogp 情報 取得開始時
                            loadingStart: function () {
                                // loading アイコン表示
                                $('<i class="fa fa-refresh fa-spin"></i>')
                                    .attr('id', 'CommentSiteInfoLoadingIcon_' + post_id)
                                    .addClass('mr_8px lh_20px')
                                    .insertBefore('#CommentSubmit_' + post_id);
                            },

                            // ogp 情報 取得完了時
                            loadingEnd: function () {
                                // loading アイコン削除
                                $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
                            }
                        });
                    };
                    var timer = null;
                    $('#CommentFormBody_' + post_id).on('keyup', function () {
                        clearTimeout(timer);
                        timer = setTimeout(onKeyUp, 800);
                    });
                });
            }
        }
    });
}

/**
 * Add a new comment
 *
 * @param e
 */
function addComment(e) {
    e.preventDefault();

    attrUndefinedCheck(e.target, 'error-msg-id');
    var result_msg_id = $(e.target).attr('error-msg-id');
    var $error_msg_box = $('#' + result_msg_id);
    attrUndefinedCheck(e.target, 'submit-id');
    var submit_id = $(e.target).attr('submit-id');
    var $submit = $('#' + submit_id);
    attrUndefinedCheck(e.target, 'first-form-id');
    var first_form_id = $(e.target).attr('first-form-id');
    var $first_form = $('#' + first_form_id);
    attrUndefinedCheck(e.target, 'refresh-link-id');
    var refresh_link_id = $(e.target).attr('refresh-link-id');
    var $refresh_link = $('#' + refresh_link_id);
    var $loader_html = $('<i class="fa fa-refresh fa-spin mr_8px"></i>');

    $error_msg_box.text("");
    appendSocketId($(e.target), cake.pusher.socket_id);

    // Display loading button
    $("#" + submit_id).before($loader_html);

    // アップロードファイルの上限数をリセット
    if (typeof Dropzone.instances[0] !== "undefined" && Dropzone.instances[0].files.length > 0) {
        // ajax で submit するので、アップロード完了後に Dropzone のファイルリストを空にする
        // （参照先の配列を空にするため空配列の代入はしない）
        Dropzone.instances[0].files.length = 0;
    }

    var $f = $(e.target);
    var ajaxProcess = $.Deferred();
    var formData = new FormData(e.target);

    // Add content of ogp box if visible
    var comment_id = submit_id.split('_')[1];
    var $ogp_box = $('#CommentOgpSiteInfo_' + comment_id);
    if ($ogp_box.find('.media-object').length > 0) {
        var image = $ogp_box.find('.media-object').attr('src');
        var title = $ogp_box.find('.media-heading').text().trim();
        var site_url = $ogp_box.find('.media-url').text();
        var description = $ogp_box.find('.site-info-txt').text().trim();
        var type = $ogp_box.find('.media-body').attr('data-type');
        var site_name = $ogp_box.find('.media-body').attr('data-site-name');

        formData.append('data[OGP][image]', image);
        formData.append('data[OGP][title]', title);
        formData.append('data[OGP][url]', site_url);
        formData.append('data[OGP][description]', description);
        formData.append('data[OGP][type]', type);
        formData.append('data[OGP][site_name]', site_name);
    }

    $.ajax({
        url: $f.prop('action'),
        method: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        timeout: 300000 //5min
    })
        .done(function (data) {
            if (!data.error) {
                // 通信が成功したときの処理
                evCommentLatestView.call($refresh_link.get(0), {
                    afterSuccess: function () {
                        $first_form.children().toggle();
                        $f.remove();
                        ajaxProcess.resolve();
                    }
                });
            }
            else {
                $error_msg_box.text(data.msg);
                ajaxProcess.reject();
            }
        })
        .fail(function (data) {
            $error_msg_box.text(cake.message.notice.g);
            ajaxProcess.reject();
        });

    ajaxProcess.always(function () {
        // 通信が完了したとき
        $loader_html.remove();
        $submit.removeAttr('disabled');
    });

}

/**
 * Return the comment id from a given comment block on screen
 *
 * @param $commentBlock
 * @returns {string}
 */
function getCommentBlockLatestId($commentBlock) {
    var commentNum = $commentBlock.children("div.comment-box").length;
    var $lastCommentBox = $commentBlock.children("div.comment-box:last");
    var lastCommentId = "";
    if (commentNum > 0) {
        // コメントが存在する場合
        attrUndefinedCheck($lastCommentBox, 'comment-id');
        lastCommentId = $lastCommentBox.attr("comment-id");
    } else {
        // コメントがまだ0件の場合
        lastCommentId = "";
    }
    return lastCommentId;
}

/**
 * Get the newest comment version and display on the screen
 *
 * @param options
 * @returns {boolean}
 */
function evCommentLatestView(options) {
    attrUndefinedCheck(this, 'post-id');
    attrUndefinedCheck(this, 'get-url');

    options = $.extend({
        afterSuccess: function () {
        }
    }, options);

    var $obj = $(this);
    var $commentBlock = $obj.closest(".comment-block");
    var lastCommentId = getCommentBlockLatestId($commentBlock);

    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    var $errorBox = $obj.siblings("div.new-comment-error");
    var get_url = $obj.attr('get-url') + "/" + lastCommentId;
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    //ローダー表示

    $.ajax({
        type: 'GET',
        url: get_url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);

                // Get the comment id for the new post
                var $comment = $posts.closest('[comment-id]').last();
                var newCommentId = $comment.attr("comment-id");

                // Get the last comment id displayed on the page
                $commentBlock = $obj.closest(".comment-block");
                lastCommentId = getCommentBlockLatestId($commentBlock);

                // Do nothing if the new comment is already rendered on the page
                if (newCommentId == lastCommentId) {
                    return;
                }

                //画像をレイジーロード
                imageLazyOn($posts);
                //一旦非表示
                $posts.hide();
                $($obj).before($posts);
                showMore($posts);
                $posts.show();
                //ローダーを削除
                $loader_html.remove();
                //リンクを削除
                $obj.css("display", "none").css("opacity", 0);
                $posts.imagesLoaded(function () {
                    $posts.find('.comment_gallery').each(function (index, element) {
                        bindCommentBalancedGallery($(element));
                    });
                    changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
                });
                $obj.removeAttr("disabled");

                initCommentNotify($obj);

                options.afterSuccess();
            }
            else {
                //ローダーを削除
                $loader_html.remove();
                //親を取得
                //noinspection JSCheckFunctionSignatures
                $obj.removeAttr("disabled");
                //「もっと読む」リンクを初期化
                initCommentNotify($obj);
                var message = $errorBox.children(".message");
                message.html(cake.message.notice.i);
                $errorBox.css("display", "block");
            }
        },
        error: function (ev) {
            //ローダーを削除
            $loader_html.remove();
            //親を取得
            //noinspection JSCheckFunctionSignatures
            $obj.removeAttr("disabled");
            //「もっと読む」リンクを初期化
            initCommentNotify($obj);
            var message = $errorBox.children(".message");
            message.html(cake.message.notice.i);
            $errorBox.css("display", "block");
        }
    });
    return false;
}

/**
 * Display a modal to confirm the deletion of comment
 * @param e
 * @returns {boolean}
 */
function evCommentDelete(e) {
    e.preventDefault();
    var $delBtn = $(this);
    attrUndefinedCheck($delBtn, 'comment-id');
    var commentId = $delBtn.attr("comment-id");

    // Modal popup
    var modalTemplate =
        '<div class="modal on fade" tabindex="-1">' +
        '  <div class="modal-dialog">' +
        '    <div class="modal-content">' +
        '      <div class="modal-header none-border">' +
        '        <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span class="close-icon">×</span></button>' +
        '        <h5 class="modal-title text-danger">' + __("Delete comment") + '</h5>' +
        '     </div>' +
        '     <div class="modal-body">' +
        '         <h4>' + __("Do you really want to delete this comment?") +'</h4>' +
        '     </div>' +
        '     <div class="modal-footer">' +
        '        <button type="button" class="btn-sm btn-default" data-dismiss="modal" aria-hidden="true">' + cake.word.cancel + '</button>' +
        '        <button type="button" class="btn-sm btn-primary js-click-comment-confirm-delete" comment-id="' + commentId + '" aria-hidden="true"><img id="loader" src="img/lightbox/loading.gif" style="height: 17px; width:17px; margin: 0 10px; display: none;"  /><span id="message">' + cake.word.delete + '</span></button>' +
        '     </div>' +
        '   </div>' +
        ' </div>' +
        '</div>';

    var $modal_elm = $(modalTemplate);
    $modal_elm.modal();
    return false;
}

/**
 * Send the delete request
 * @returns {boolean}
 */
function evCommentDeleteConfirm() {
    var $delBtn = $(this);
    attrUndefinedCheck($delBtn, 'comment-id');
    var commentId = $delBtn.attr("comment-id");
    var url = "/api/v1/comments/" + commentId;
    var $modal = $delBtn.closest('.modal');
    var $commentBox = $("div.comment-box[comment-id='" + commentId + "']");

    // Show loading spinner and hide button text
    $delBtn.children('#loader').toggle();
    $delBtn.children('#message').toggle();
    $delBtn.attr('disabled', 'disabled');

    $.ajax({
        url: url,
        type: 'DELETE',
        success: function () {
            // Remove modal and comment box
            $modal.modal('hide');
            $commentBox.fadeOut('slow', function(){
                $(this).remove();
            });
        },
        error: function (res) {
            // Display error message
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.i,
            }).show();
            $modal.modal('hide');
        }
    });
    return false;
}

/**
 * Submit comment form to API
 * @param e
 * @returns {boolean}
 */
function evCommendEditSubmit(e) {
    e.preventDefault();
    var $form = $(this).parents('form');
    var formUrl = $form.attr('action');
    var commentId = formUrl.split(':')[1];

    var token = $form.find('[name="data[_Token][key]"]').val();
    var body = $form.find('[name="data[Comment][body]"]').val();

    var formData = {
        "data[_Token][key]": token,
        Comment: {
            body: body
        },
        OGP: null
    };

    var $ogp = $('#CommentOgpEditBox_'+commentId);
    if ($ogp.find('.media-object').length > 0) {
        var image = $ogp.find('.media-object').attr('src');
        var title = $ogp.find('.media-heading').text().trim();
        var site_url = $ogp.find('.media-url').text();
        var description = $ogp.find('.site-info-txt').text().trim();
        var type = $ogp.find('.media-body').attr('data-type');
        var site_name = $ogp.find('.media-body').attr('data-site-name');

        var ogpData = {
            image: image,
            title: title,
            url: site_url,
            description: description,
            type: type,
            site_name: site_name
        };
        formData.OGP = ogpData;
    }

    $.ajax({
        type: 'PUT',
        url: "/api/v1/comments/" + commentId,
        cache: false,
        dataType: 'json',
        data: formData,
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                var $updatedComment = $(data.html);
                // update comment box
                imageLazyOn($updatedComment);
                var $box = $('.comment-box[comment-id="' + commentId + '"]');
                $updatedComment.insertBefore($box);
                $updatedComment.imagesLoaded(function () {
                    $updatedComment.find('.comment_gallery').each(function (index, element) {
                        bindCommentBalancedGallery($(element));
                    });
                    changeSizeFeedImageOnlyOne($updatedComment.find('.feed_img_only_one'));
                });
                $box.remove();
            }
            else {
                // Cancel editing
                $('[target-id="CommentEditForm_' + commentId + '"]').click();
            }
        },
        error: function (ev) {
            // Display error message
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.i,
            }).show();
            // Cancel editing
            $('[target-id="CommentEditForm_' + commentId + '"]').click();
        }
    });
    return false;
}

// 通知から投稿、メッセージに移動
// TODO: メッセージ通知リンクと投稿通知リンクのイベントを分けるか、このメソッドを汎用的に使えるようにする。
//       そうしないとメッセージ詳細へのリンクをajax化する際に、ここのロジックが相当複雑になってしまう予感がする。
function evNotifyPost(options) {
    //とりあえずドロップダウンは隠す
    $(".has-notify-dropdown").removeClass("open");
    $('body').removeClass('notify-dropdown-open');

    var opt = $.extend({
        recursive: false,
        loader_id: null
    }, options);

    //フィード読み込み中はキャンセル
    if (feed_loading_now) {
        return false;
    }
    feed_loading_now = true;

    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var get_url = $obj.attr('get-url');

    //layout-mainが存在しないところではajaxでコンテンツ更新しようにもロードしていない
    //要素が多すぎるので、おとなしくページリロードする
    //urlにpost_permanentを含まない場合も対象外
    if (!$(".layout-main").exists() || !get_url.match(/post_permanent/)) {
        // 現状、メッセージページに遷移する場合はこのブロックを通る
        feed_loading_now = false;
        window.location.href = get_url;
        return false;
    }

    //アドレスバー書き換え
    if (!updateAddressBar(get_url)) {
        return false;
    }

    $('#jsGoTop').click();

    //ローダー表示
    var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>');
    if (!opt.recursive) {
        $(".layout-main").html($loader_html);
    }

    // URL生成
    var url = get_url.replace(/post_permanent/, "ajax_post_permanent");

    var button_notifylist = '<a href="#" get-url="/notifications" class="btn-back btn-back-notifications"> <i class="fa fa-chevron-left font_18px font_lightgray lh_20px"></i> </a> ';

    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //notify一覧に戻るhtmlを追加
                //画像をレイジーロード
                imageLazyOn($posts);
                //一旦非表示
                $posts.hide();

                $(".layout-main").html(button_notifylist);
                $(".layout-main").append($posts);
                $(".layout-main").append(button_notifylist);

                showMore($posts);
                $posts.show();

                //リンクを有効化
                $obj.removeAttr('disabled');
                $("#ShowMoreNoData").hide();
                $posts.imagesLoaded(function () {
                    $posts.find('.post_gallery').each(function (index, element) {
                        bindPostBalancedGallery($(element));
                    });
                    $posts.find('.comment_gallery').each(function (index, element) {
                        bindCommentBalancedGallery($(element));
                    });
                    changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
                });
            }

            //ローダーを削除
            $loader_html.remove();

            // Google tag manager トラッキング
            if (cake.data.google_tag_manager_id !== "") {
                sendToGoogleTagManager('app');
            }

            action_autoload_more = false;
            autoload_more = false;
            feed_loading_now = false;
            do_reload_header_bellList = true;
        },
        error: function () {
            feed_loading_now = false;
            $loader_html.remove();
        },
    });
    return false;
}

function evCommentOldView() {
    attrUndefinedCheck(this, 'parent-id');
    attrUndefinedCheck(this, 'get-url');
    var $obj = $(this);
    var parent_id = $obj.attr('parent-id');
    var get_url = $obj.attr('get-url');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    $.ajax({
        type: 'GET',
        url: get_url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //画像をレイジーロード
                imageLazyOn($posts);
                //一旦非表示
                $posts.hide();
                $("#" + parent_id).before($posts);
                showMore($posts);
                $posts.fadeIn();
                //ローダーを削除
                $loader_html.remove();
                //リンクを削除
                $obj.css("display", "none").css("opacity", 0);
                $posts.imagesLoaded(function () {
                    $posts.find('.comment_gallery').each(function (index, element) {
                        bindCommentBalancedGallery($(element));
                    });
                    changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
                });

            }
            else {
                //ローダーを削除
                $loader_html.remove();
                //親を取得
                //noinspection JSCheckFunctionSignatures
                var $parent = $obj.parent();
                //「もっと読む」リンクを削除
                $obj.remove();
                //「データが無かった場合はデータ無いよ」を表示
                $parent.append(cake.message.info.g);
            }
        },
        error: function () {
            alert(cake.message.notice.c);
        }
    });
    return false;
}

function evTargetToggleClick() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'click-target-id');

    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    var comment_id = target_id.split('_')[1];
    if ($obj.attr("hidden-target-id")) {
        var $commentBox = $('#' + $obj.attr("hidden-target-id"));
        $commentBox.toggle();
        // Hide OGP box
        var $ogpBox = $('#CommentOgpBox_' + comment_id);
        if ($ogpBox.length > 0) {
            $ogpBox.toggle();
        }
    }

    //開いている時と閉じてる時のテキストの指定があった場合は置き換える
    if ($obj.attr("opend-text") != undefined && $obj.attr("closed-text") != undefined) {
        //開いてるとき
        if ($("#" + target_id).is(':visible')) {
            //閉じてる表示
            $obj.text($obj.attr("closed-text"));
        }
        //閉じてるとき
        else {
            //開いてる表示
            $obj.text($obj.attr("opend-text"));
        }
    }
    if (0 == $("#" + target_id).length && $obj.attr("ajax-url") != undefined) {
        $.ajax({
            url: $obj.attr("ajax-url"),
            async: false,
            success: function (data) {
                //noinspection JSUnresolvedVariable
                if (data.error) {
                    //noinspection JSUnresolvedVariable
                    alert(data.msg);
                }
                else {
                    var $editForm = $(data.html);
                    var $ogp = $editForm.find('.js-ogp-box');
                    if ($ogp.length > 0) {
                        var $btnClose = $editForm.find('.js-ogp-close');
                        $btnClose.on('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            $ogp.remove();
                            $btnClose.remove();
                            var $submitButton = $('#CommentEditSubmit_' + comment_id);
                            if ($submitButton.length > 0) {
                                $submitButton.removeAttr("disabled");
                            }
                        });
                    }
                    $("#" + $obj.attr("hidden-target-id")).after($editForm);

                    // Load OGP for edit field
                    var $editField = $('#CommentEditFormBody_' + comment_id);
                    if ($editField.length > 0) {
                        require(['ogp'], function (ogp) {
                            var onKeyUp = function () {
                                // Do not search for new OGP if there is one already present
                                var $ogpBox = $('#CommentOgpEditBox_' + comment_id);
                                if ($ogpBox.length > 0) {
                                    return;
                                }

                                // Search OGP info
                                ogp.getOGPSiteInfo({
                                    // Give text to OGP class
                                    text: $editField.val(),

                                    // Only search if there is none OGP info box displayed
                                    readyLoading: function () {
                                        if ($ogpBox.length > 0) {
                                            return false;
                                        }
                                        return true;
                                    },

                                    // ogp data acquired
                                    success: function (data) {
                                        // Remove any OGP if already exists
                                        var $ogpBox = $('#CommentOgpEditBox_' + comment_id);
                                        if ($ogpBox.length > 0) {
                                            $ogpBox.remove();
                                            return;
                                        }

                                        // Display the new acquired OGP on the edit form
                                        var $newOgp = $(data.html);
                                        $newOgp.attr('id', 'CommentOgpEditBox_' + comment_id);
                                        $editField.after($newOgp);
                                        var $closeButton = $('<a>');
                                        $newOgp.before($closeButton);
                                        $closeButton.attr('href', '#')
                                            .addClass('font_lightgray comment-ogp-close')
                                            .append('<i class="fa fa-times"></i>')
                                            .on('click', function (e) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                $closeButton.remove();
                                                $newOgp.remove();
                                            });
                                    },

                                    error: function () {
                                        // loading アイコン削除
                                        $('#CommentSiteInfoLoadingIcon_' + comment_id).remove();
                                    },

                                    loadingStart: function () {
                                        // loading アイコン表示
                                        $('<i class="fa fa-refresh fa-spin"></i>')
                                            .attr('id', 'CommentSiteInfoLoadingIcon_' + comment_id)
                                            .addClass('mr_8px lh_20px')
                                            .insertBefore('#CommentEditSubmit_' + comment_id);
                                    },

                                    loadingEnd: function () {
                                        // loading アイコン削除
                                        $('#CommentSiteInfoLoadingIcon_' + comment_id).remove();
                                    }
                                });
                            };
                            var timer = null;
                            $editField.on('keyup', function () {
                                clearTimeout(timer);
                                timer = setTimeout(onKeyUp, 800);
                            });
                        });
                    }
                }
            }
        });
    }

    $("form#" + target_id).bootstrapValidator();
    $("#" + target_id).find('.custom-radio-check').customRadioCheck();

    //noinspection JSJQueryEfficiency
    $("#" + target_id).toggle();
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).trigger('click');
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).focus();
    return false;
}

function initCommentNotify(notifyBox) {
    var numInBox = notifyBox.find(".num");
    numInBox.html("0");
    notifyBox.css("display", "none").css("opacity", 0);
}

// Used on circle and feed
function bindCommentBalancedGallery($obj) {
    $obj.removeClass('none');
    $obj.BalancedGallery({
        autoResize: false,                   // re-partition and resize the images when the window size changes
        //background: '#DDD',                   // the css properties of the gallery's containing element
        idealHeight: 130,                  // ideal row height, only used for horizontal galleries, defaults to half the containing element's height
        //idealWidth: 100,                   // ideal column width, only used for vertical galleries, defaults to 1/4 of the containing element's width
        //maintainOrder: false,                // keeps images in their original order, setting to 'false' can create a slightly better balance between rows
        orientation: 'horizontal',          // 'horizontal' galleries are made of rows and scroll vertically; 'vertical' galleries are made of columns and scroll horizontally
        padding: 1,                         // pixels between images
        shuffleUnorderedPartitions: true,   // unordered galleries tend to clump larger images at the begining, this solves that issue at a slight performance cost
        //viewportHeight: 400,               // the assumed height of the gallery, defaults to the containing element's height
        //viewportWidth: 482                // the assumed width of the gallery, defaults to the containing element's width
    });
};

//bootstrapValidatorがSuccessした時
function validatorCallback(e) {
    if (e.target.id.startsWith('CommentAjaxGetNewCommentForm_')) {
        addComment(e);
    }
    else if (e.target.id == "ActionCommentForm") {
        addComment(e);
    }
}
