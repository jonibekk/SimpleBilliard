/**
 * This file contains script related to comments on posts
 */
"use strict";

$(function () {
    require.config({
        baseUrl: '/js/modules/'
    });
    $(document).on("click", ".click-get-ajax-form-toggle", toggleCommentForm);
    $(document).on("click", ".click-comment-new", evCommentLatestView);
    $(document).on("click", ".js-click-comment-delete", evCommentDelete);
    $(document).on("click", ".js-click-comment-confirm-delete", evCommentDeleteConfirm);
    $(document).on("click", '[id*="CommentEditSubmit_"]', evCommentEditSubmit);

    $(document).on("click", ".notify-click-target", evNotifyPost);
    $(document).on("click", ".click-comment-all", evCommentOldView);
    $(document).on("click", ".target-toggle-click", evTargetToggleClick);

    // コメント
    bindCtrlEnterAction('.comment-form', function (e) {
        $(this).find('.comment-submit-button').trigger('click');
    });
    $(".comment-post-form,.comment-form").off("focus.comment-form").off("blur.comment-form").on("focus.comment-form", function() {
        $("#jsGoTop").hide();
    }).on("blur.comment-form", function() {
        $("#jsGoTop").show();
    });
    $(".comment-ogp-close").remove();
});

// for resizing certainly, exec after window loaded
window.addEventListener("load", function () {
    bindCommentBalancedGallery($('.comment_gallery'));
});

/**
 * Checks valid Url
 */
var url_pattern = {
    protocol: 'https?:\/\/(www\.)?',
    domain: '[a-zA-Z0-9-_\.]+',
    tld: '(\.[a-zA-Z0-9]{2,})',
    params: '([-a-zA-Z0-9:%_\+.~#?&//=]*)'
}

function getValidURL(input){
    var regex = new RegExp(url_pattern.protocol + url_pattern.domain + url_pattern.tld + url_pattern.params, 'g');
    var result = regex.exec(input);
    if(result){
        return result[0];
    } else {
        return null;
    }
}

/**
 * Display comment input
 */
function toggleCommentForm() {
    attrUndefinedCheck(this, 'post-id');
    var $txtArea = $(this);
    var post_id = sanitize($txtArea.attr("post-id"));
    var $commentButtons = $('#Comment_' + post_id);
    var $commentForm = $('#CommentAjaxGetNewCommentForm_' + post_id);

    if ($commentButtons.is(':visible')) {
        return;
    }
    new Mention($txtArea)

    $(".comment-post-form,.comment-form").off("focus").off("blur").on("focus", function() {
        $("#jsGoTop").hide();
    }).on("blur", function() {
        $("#jsGoTop").show();
    });

    evTargetCancelAnyEdit();

    // reset textarea
    $txtArea.val("");
  
    // Register the form for submit
    $commentForm.off('submit').on('submit', function (e) {
        $('#CommentOgpClose_' + post_id).remove();
        $(".comment-ogp-close").remove();
        // アップロードファイルの有効期限が切れていなければコメント投稿
        var res = checkUploadFileExpire($(this).attr('id'));
        if (res) {
            validatorCallback(e);
        }
        return res;
    });
    $('#CommentSubmit_' + post_id).on('click', function() {
        $("#jsGoTop").show();
    });
    autosize($txtArea);

    // Display the buttons
    $commentButtons.removeClass('no-border');
    $commentButtons.toggle();
    $txtArea.addClass('no-border');
    // Remove comment file field
    $commentForm.find("input[name^='data[file_id]']").remove();
    // Clear OGP info
    $commentForm.find("input[name^='data[Comment][site_info_url]']").val('');

    // Enables drag and drop functionality to the comments section
    var $uploadFileForm = $(document).data('uploadFileForm');
    var commentParams = {
        formID: function () {
            return $(this).attr('data-form-id');
        },
        previewContainerID: function () {
            return $(this).attr('data-preview-container-id');
        },
        requestParams: function () {
            return {};
        },
        beforeSending: function () {
            if ($uploadFileForm._sending) {
                return;
            }
            $uploadFileForm._sending = true;
            // While submitting disables form submit
            $('#CommentSubmit_' + post_id).on('click', $uploadFileForm._forbitSubmit);
        },
        afterQueueComplete: function () {
            $uploadFileForm._sending = false;
            // Enables form submit
            $('#CommentSubmit_' + post_id).off('click', $uploadFileForm._forbitSubmit);
        },
        afterError: function (file) {
            var $preview = $(file.previewTemplate);
            // Make it stand out and last long so the user can see and recognize the error
            $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.error + ')');
            setTimeout(function () {
                $preview.remove();
            }, 4000);
        }
    };
    
    $uploadFileForm.trigger('reset');
    $uploadFileForm.registerDragDropArea('#CommentBlock_' + post_id, commentParams);
    $uploadFileForm.registerAttachFileButton('#CommentUploadFileButton_' + post_id, commentParams);

    // OGP preview and get procedure
    require(['ogp'], function (ogp) {
        $('#CommentFormBody_' + post_id).off('keyup.comment-form').on('keyup.comment-form', function (e) {
            if ($('#CommentOgpSiteInfo_' + post_id).html() !== '') {
                return false;
            }
            var position = $('#CommentFormBody_' + post_id).get(0).selectionStart - 1;
            var key = this.value.charCodeAt(position);
            if(key == 32 || key == 10) {
                var url = getValidURL($('#CommentFormBody_' + post_id).val());
                if(url) {
                    ogpComments(ogp, url);
                }
            }
        });
        $('#CommentFormBody_' + post_id).off('paste').on('paste', function (e) {
            if($('#CommentOgpSiteInfo_' + post_id).html() === ''){
                var url = getValidURL(e.originalEvent.clipboardData.getData('text'));
                if(url) {
                    ogpComments(ogp, url);
                }
            }
        });
        function ogpComments(ogp, text) {
            var options = {
                // Text containing the url
                text: text,

                // Checks if necessary to obtain ogp
                readyLoading: function () {
                    return true;
                },

                // On success retreiving the ogp data
                success: function (data) {
                    // Display the new acquired OGP on the edit form
                    var $siteInfoUrl = $('#CommentSiteInfoUrl_' + post_id);
                    var $siteInfo = $('#CommentOgpSiteInfo_' + post_id).html(data.html);
                    var $btnClose = $('<div id="CommentOgpClose_' +  post_id + '" class="font_lightgray comment-ogp-close"><i class="fa fa-times fa-2x js-ogp-close"></i></div>');
                    $btnClose.off('click').on('click', function() {
                        $siteInfo.empty();
                        $btnClose.remove();
                        var $submitButton = $('#CommentEditSubmit_' + post_id);
                        if ($submitButton.length) {
                            $submitButton.removeAttr("disabled");
                        }
                    });
                    $btnClose.insertBefore($siteInfo);

                    // add url to hidden
                    $siteInfoUrl.val(data.url);
                },

                // On failure retreiving the ogp data
                error: function () {
                    // remove loading icon
                    $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
                },

                // Start retreiving the ogp data
                loadingStart: function () {
                    // show loading icon
                    $('<i class="fa fa-refresh fa-spin"></i>')
                        .attr('id', 'CommentSiteInfoLoadingIcon_' + post_id)
                        .addClass('mr_8px lh_20px')
                        .insertBefore('#CommentSubmit_' + post_id);
                },

                // Finish retreiving the ogp data
                loadingEnd: function () {
                    // remove loading icon
                    $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
                }
            };
            ogp.getOGPSiteInfo(options);
            return false;
        }
    });
}

/**
 * Hide comment input
 */
function hideCommentForm(element) {
    var $txtArea = $(element);
    attrUndefinedCheck($txtArea, 'post-id');

    var post_id = sanitize($txtArea.attr("post-id"));
    var $commentButtons = $('#Comment_' + post_id);
    var $commentForm = $('#CommentAjaxGetNewCommentForm_' + post_id);
    var $commentFormBody = $('CommentFormBody_' + post_id);
    var $uploadPreview = $('#CommentUploadFilePreview_' + post_id);
    var $ogpSiteInfo = $('#CommentOgpSiteInfo_' + post_id);
    var $commentSiteInfoUrl = $('#CommentSiteInfoUrl_' + post_id);

    // Clear upload data
    $commentFormBody.css("height",null);
    $uploadPreview.empty();
    $ogpSiteInfo.empty();
    $commentFormBody.removeClass('no-border');

    // Clears comment input field
    $txtArea.val("");

    // Resets ogp
    $ogpSiteInfo.val('');
    $ogpSiteInfo.empty();

    // unregister the form for submit
    $commentForm.off('submit');

    // Toggle display the buttons
    $commentButtons.removeClass('no-border');
    $commentButtons.toggle();
    $txtArea.removeClass('no-border');

    // Remove comment file field
    $commentForm.find("input[name^='data[file_id]']").remove();
    // Clear OGP info
    $commentForm.find("input[name^='data[Comment][site_info_url]']").val('');

    // Enables drag and drop functionality to the comments section
    $(document).data('uploadFileForm').trigger('reset');

    $txtArea.css('height', '19px');
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

    // Set max upload count
    if (typeof Dropzone.instances[0] !== "undefined" && Dropzone.instances[0].files.length > 0) {
        Dropzone.instances[0].files.length = 0;
    }

    var $f = $(e.target);
    var ajaxProcess = $.Deferred();
    var formData = new FormData(e.target);
    var textarea = $(e.target).find('textarea')
    formData.append('data[Comment][body]', textarea[0].submitValue())
    // Add content of ogp box if visible
    var comment_id = submit_id.split('_')[1];
    var $ogp_box = $('#CommentOgpSiteInfo_' + comment_id);
    if ($ogp_box.find('.media-object').length > 0) {
        var image = $ogp_box.find('.media-object').attr('src');
        var title = $ogp_box.find('.media-heading').text().trim();
        var description = $ogp_box.find('.site-info-txt').text().trim();
        var $media_body = $ogp_box.find('.media-body');
        var site_url = $media_body.attr('data-url');
        var type = $media_body.attr('data-type');
        var site_name = $media_body.attr('data-site-name');

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
                // on Success transmitting
                evCommentLatestView.call($refresh_link.get(0), {
                    afterSuccess: function () {
                        var post_id = sanitize($f.attr("post-id"));
                        var $commentButtons = $('#Comment_' + post_id);
                        var $uploadFileForm = $(document).data('uploadFileForm');
                        // Reset forms
                        $f.trigger('reset');
                        $f.find("input[name^='data[file_id]']").remove();
                        $f.find("input[name^='data[Comment][site_info_url]']").val('');
                        $uploadFileForm.trigger('reset');
                        // Clear upload data
                        document.getElementById('CommentFormBody_' + post_id).style.height = null;
                        $('#CommentUploadFilePreview_' + post_id).empty();
                        $('#CommentOgpSiteInfo_' + post_id).empty();
                        $('#CommentFormBody_' + post_id).removeClass('no-border');
                        $commentButtons.toggle();
                        ajaxProcess.resolve();
                        // always blur since the focus remains in textarea even after CTRL+ENTER 
                        $('#CommentFormBody_' + post_id).blur()
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
        // When done transmitting
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
    // disable link
    $obj.attr('disabled', 'disabled');
    // show loader

    $.ajax({
        type: 'GET',
        url: get_url,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                // create object from retreived data
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
                $posts.show();
                showMore($posts);
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
        '         <h4>' + __("Do you really want to delete this comment?") + '</h4>' +
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
            $commentBox.fadeOut('slow', function () {
                $(this).remove();
            });
        },
        error: function (res) {
            // Display error message
            new Noty({
                type: 'error',
                text: '<h4>' + cake.word.error + '</h4>' + cake.message.notice.i,
            }).show();
            $modal.modal('hide');
        }
    });
    $("#jsGoTop").show();
    return false;
}

/**
 * Submit comment form to API
 * @param e
 * @returns {boolean}
 */
function evCommentEditSubmit(e) {
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

    var $ogp = $('#CommentOgpEditBox_' + commentId);
    if ($ogp.find('.media-object').length > 0) {
        var image = $ogp.find('.media-object').attr('src');
        var title = $ogp.find('.media-heading').text().trim();
        var description = $ogp.find('.site-info-txt').text().trim();
        var $media_body = $ogp.find('.media-body');
        var site_url = $media_body.attr('data-url');
        var type = $media_body.attr('data-type');
        var site_name = $media_body.attr('data-site-name');

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

    formData['data[Comment][body]'] = $('textarea#CommentEditFormBody_'+commentId)[0].submitValue()

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
                text: '<h4>' + cake.word.error + '</h4>' + cake.message.notice.i,
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

    // https://jira.goalous.com/browse/GL-7019
    $(".force-open").removeClass("force-open");

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

                $posts.show();
                showMore($posts);

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
                $posts.show();
                showMore($posts);
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
    attrUndefinedCheck(this, 'ajax-url');
    attrUndefinedCheck(this, 'opend-text');
    attrUndefinedCheck(this, 'closed-text');

    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    var comment_id = target_id.split('_')[1];

    if($obj.text() === $obj.attr("closed-text")){
      turnOffTranslation(comment_id);
        evTargetCancelAnyEdit();
    }

    if (0 == $("#" + target_id).length) {
        $.ajax({
            url: $obj.attr("ajax-url"),
            success: function (data) {
                //noinspection JSUnresolvedVariable
                if (data.error) {
                    //noinspection JSUnresolvedVariable
                    alert(data.msg);
                }
                else {

                    var $editForm = $(data.html);
                    var $ogp = $($editForm.find('.js-ogp-box'));
                    $('#CommentOgpBackup_' + comment_id).html($ogp.html());
                    $ogp.prop('id','CommentOgpEditBox_' + comment_id);
                    $("#" + $obj.attr("hidden-target-id")).after($editForm);
                    $('#CommentOgpBackup_' + comment_id).hide();
                    var $btnClose = $('<div id="CommentOgpClose_' +  comment_id + '" class="font_lightgray comment-ogp-close"><i class="fa fa-times fa-2x js-ogp-close"></i></div>');
                    $btnClose.off('click').on('click', function() {
                        $ogp.remove();
                        $btnClose.remove();
                        var $submitButton = $('#CommentEditSubmit_' + comment_id);
                        if ($submitButton.length) {
                            $submitButton.removeAttr("disabled");
                        }
                    });
                    $btnClose.insertBefore($ogp);

                    // Load OGP for edit field
                    var $editField = $('#CommentEditFormBody_' + comment_id);
                    require(['ogp'], function (ogp) {
                        $('#CommentEditFormBody_' + comment_id).off('keyup.comment-form').on('keyup.comment-form', function (e) {
                            if($('#CommentEditSubmit_' + comment_id).prop('disabled') && $('#CommentEditFormBody_' + comment_id).val() !== $('#CommentOgpBackup_' + comment_id).data('text')){
                                $('#CommentEditSubmit_' + comment_id).prop('disabled', false);
                            }
                            if ($('#CommentOgpEditBox_' + comment_id).text() !== '') {
                                return false;
                            }
                            var position = $('#CommentEditFormBody_' + comment_id).get(0).selectionStart - 1;
                            var key = this.value.charCodeAt(position);
                            if(key == 32 || key == 10) {
                                var url = getValidURL($('#CommentEditFormBody_' + comment_id).val());
                                if(url) {
                                    ogpComments(ogp, url);
                                }
                            }
                        });
                        $('#CommentEditFormBody_' + comment_id).off('paste').on('paste', function (e) {
                            if ($('#CommentOgpEditBox_' + comment_id).text() === '') {
                                var url = getValidURL(e.originalEvent.clipboardData.getData('text'));
                                if(url) {
                                    ogpComments(ogp, url);
                                }
                            }
                        });
                        function ogpComments(ogp, text) {
                            var options = {
                                // Text containing the url
                                text: text,

                                // Checks if necessary to obtain ogp
                                readyLoading: function () {
                                    return true;
                                },

                                // On success retreiving the ogp data
                                success: function (data) {
                                    // Display the new acquired OGP on the edit form
                                    var $newOgp = $(data.html);
                                    $newOgp.attr('id', 'CommentOgpEditBox_' + comment_id);
                                    $('#CommentEditFormBody_' + comment_id).after($newOgp);
                                    var $btnClose = $('<div id="CommentOgpClose_' +  comment_id + '" class="font_lightgray comment-ogp-close"><i class="fa fa-times fa-2x js-ogp-close"></i></div>');
                                    $btnClose.off('click').on('click', function() {
                                        $newOgp.remove();
                                        $btnClose.remove();
                                        var $submitButton = $('#CommentEditSubmit_' + comment_id);
                                        if ($submitButton.length) {
                                            $submitButton.removeAttr("disabled");
                                        }
                                    });
                                    $btnClose.insertBefore($newOgp);
                                },

                                // On failure retreiving the ogp data
                                error: function () {
                                    // remove loading icon
                                    $('#CommentSiteInfoLoadingIcon_' + comment_id).remove();
                                },

                                // Start retreiving the ogp data
                                loadingStart: function () {
                                    // show loading icon
                                    $('<i class="fa fa-refresh fa-spin"></i>')
                                        .attr('id', 'CommentSiteInfoLoadingIcon_' + comment_id)
                                        .addClass('mr_8px lh_20px')
                                        .insertBefore('#CommentEditSubmit_' + comment_id);
                                },

                                // Finish retreiving the ogp data
                                loadingEnd: function () {
                                    // remove loading icon
                                    $('#CommentSiteInfoLoadingIcon_' + comment_id).remove();
                                }
                            };
                            ogp.getOGPSiteInfo(options);
                            return false;
                        }
                    });
                }
            }
        }).then(function(){
            $obj.text($obj.attr("opend-text"));
            $("#jsGoTop").hide();
            $('#CommentTextBody_' + comment_id).hide();
            $('#CommentEdit_' + comment_id).show();
            $('#CommentOgpBox_' + comment_id).hide();
            $('#CommentEditForm_' + comment_id).show();
            new Mention($('#CommentEditFormBody_' + comment_id))
            $('#CommentOgpBackup_' + comment_id).data('text', $('#CommentEditFormBody_' + comment_id).val());
            var $newOgp = $('CommentOgpEditBox_' + comment_id);
            var $btnClose = $('<div id="CommentOgpClose_' +  comment_id + '" class="font_lightgray comment-ogp-close"><i class="fa fa-times fa-2x js-ogp-close"></i></div>');
            $btnClose.off('click').on('click', function() {
                $newOgp.remove();
                $btnClose.remove();
                var $submitButton = $('#CommentEditSubmit_' + comment_id);
                if ($submitButton.length) {
                    $submitButton.removeAttr("disabled");
                }
            });
            $btnClose.insertBefore($newOgp);
        });
    } else {
        if ($('#CommentEditForm_' + comment_id).is(':visible')) {
            turnOnTranslation(comment_id);
            $("#jsGoTop").show();
            $obj.text($obj.attr("closed-text"));
            $('#CommentOgpClose_' + comment_id).hide();
            $('#CommentOgpBox_' + comment_id).show();
            $('#CommentTextBody_' + comment_id).show();
            $('#CommentOgpEditBox_' + comment_id).html($('#CommentOgpBackup_' + comment_id).html());
            $('#CommentEditFormBody_' + comment_id).val($('#CommentOgpBackup_' + comment_id).data('text'));
            $('#CommentOgpEditBox_' + comment_id).hide();
        }
        else {
            $obj.text($obj.attr("opend-text"));
            $("#jsGoTop").hide();
            $('#CommentOgpBox_' + comment_id).hide();
            $('#CommentTextBody_' + comment_id).hide();
            $('#CommentOgpEditBox_' + comment_id).html($('#CommentOgpBackup_' + comment_id).html());
            $('#CommentEditFormBody_' + comment_id).val($('#CommentOgpBackup_' + comment_id).data('text'));
            $('#CommentOgpEditBox_' + comment_id).show();
            var $siteInfo = $('#CommentOgpEditBox_' + comment_id);
            if($siteInfo.length) {
                var $btnClose = $('<div id="CommentOgpClose_' +  comment_id + '" class="font_lightgray comment-ogp-close"><i class="fa fa-times fa-2x js-ogp-close"></i></div>');
                $btnClose.off('click').on('click', function() {
                    $siteInfo.remove();
                    $btnClose.remove();
                    var $submitButton = $('#CommentEditSubmit_' + comment_id);
                    if ($submitButton.length) {
                        $submitButton.removeAttr("disabled");
                    }
                });
                $btnClose.insertBefore($siteInfo);
            }
            evTargetCancelAnyEdit();
        }
        new Mention($('#CommentEditFormBody_' + comment_id))
    }
    

    $("form#" + target_id).bootstrapValidator();
    $("#" + target_id).find('.custom-radio-check').customRadioCheck();

    //noinspection JSJQueryEfficiency
    $("#" + target_id).toggle();
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).trigger('click');
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).focus();
    $('.dropdown-comment.open').removeClass('open')

    return false;
}

function evTargetToggleClickByElement(elem) {
    attrUndefinedCheck(elem, 'target-id');
    attrUndefinedCheck(elem, 'click-target-id');
    attrUndefinedCheck(elem, 'opend-text');
    attrUndefinedCheck(elem, 'closed-text');
    attrUndefinedCheck(elem, 'hidden-target-id');

    var $obj = $(elem);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    var comment_id = target_id.split('_')[1];
    var $commentBox = $('#' + $obj.attr("hidden-target-id"));
    $commentBox.toggle();
    // Hide OGP box
    var $ogpBox = $('#CommentOgpBox_' + comment_id);
    if ($ogpBox.length > 0) {
        $ogpBox.toggle();
    }

    if ($("#" + target_id).is(':visible')) {
        $obj.text($obj.attr("closed-text"));
        $('#CommentOgpBox_' + comment_id).show();
        $('#CommentTextBody_' + comment_id).show();
    }

    //noinspection JSJQueryEfficiency
    $("#" + target_id).toggle();
    return false;
}

function evTargetCancelAnyEdit() {
    var openForm = $(".comment-edit-form:visible");
    if(openForm.length == 1){
        var editId = openForm.prop("id").replace("CommentEditFormBody_","");
        var targetLink = $("[target-id=CommentEditForm_" + editId +"]").get(0);
        evTargetToggleClickByElement(targetLink);
    }
    var commentButton = $(".comment-btn:visible");
    if(commentButton.length === 1) {
        var commentEdit = commentButton.find(".comment-submit-button");
        var postId = commentEdit.prop("id").replace("CommentSubmit_","");
        var targetEdit = document.getElementById("CommentFormBody_" + postId);
        hideCommentForm(targetEdit);
    }
    
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
