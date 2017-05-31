/**
 * This file contains script related to comments on posts
 */

$(function () {
    console.log("LOADING: comments.js");
    $(document).on("click", ".js-click-comment-delete", evCommentDelete);
    $(document).on("click", ".js-click-comment-confirm-delete", evCommentDeleteConfirm);
    $(document).on("click", '[id*="CommentEditSubmit_"]', evCommendEditSubmit);
});

/**
 * Add a new comment
 *
 * @param e
 */
function addComment(e) {
    console.log("comments.js: addComment");
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
    console.log("comments.js: getCommentBlockLatestId");

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
    console.log("comments.js: evCommentLatestView");

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
                $posts.fadeOut();
                $($obj).before($posts);
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
    console.log("comments.js: evCommentDelete");

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
    console.log("comments.js: evCommentDeleteConfirm");

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
            new PNotify({
                title: cake.word.error,
                text: cake.message.notice.i,
                type: 'error'
            });
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
    console.log("comments.js: evCommendEditSubmit");

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
            new PNotify({
                title: cake.word.error,
                text: cake.message.notice.i,
                type: 'error'
            });
            // Cancel editing
            $('[target-id="CommentEditForm_' + commentId + '"]').click();
        }
    });
    return false;
}