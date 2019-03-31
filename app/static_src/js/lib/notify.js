"use strict";

var network_reachable = true;

//Monitoring of the communication state of App Server | Appサーバーの通信状態の監視
window.addEventListener("online", function () {
    updateNotifyCnt();
    updateMessageNotifyCnt();
    network_reachable = true;
}, false);

window.addEventListener("offline", function () {
    network_reachable = false;
}, false);

window.addEventListener('load', function() {

    // Pusher
    var pusher = new Pusher(cake.pusher.key);
    var socketId = "";
    var prevNotifyId = "";
    pusher.connection.bind('connected', function () {
        socketId = pusher.connection.socket_id;
        cake.pusher.socket_id = socketId;
    });
    // フォームがsubmitされた際にsocket_idを埋め込む
    $(document).on('submit', 'form.form-feed-notify', function () {
        appendSocketId($(this), socketId);
    });

    // keyResultの完了送信時にsocket_idを埋め込む
    $(document).on("click", ".kr_achieve_button", function () {
        var formId = $(this).attr("form-id");
        var $form = $("form#" + formId);
        appendSocketId($form, socketId);
        $form.submit();
        $(this).prop("disabled", true);
    });

    // page type idをセットする
    setPageTypeId();

    // connectionをはる
    for (var i in cake.data.c) {
        pusher.subscribe(cake.data.c[i]).bind('post_feed', function (data) {
            var isFeedNotify = viaIsSet(data.is_feed_notify);
            var isNewCommentNotify = viaIsSet(data.is_comment_notify);
            var notifyId = data.notify_id;

            // not allowed multple notify
            if (notifyId === prevNotifyId) {
                return;
            }

            // フィード通知の場合
            if (isFeedNotify) {
                var pageTypeId = getPageTypeId();
                var feedTypeId = data.feed_type;
                var canNotify = pageTypeId === feedTypeId || pageTypeId === "all";
                if (canNotify) {
                    prevNotifyId = notifyId;
                    notifyNewFeed();
                }

                var optionData = data.options;
                if (undefined !== optionData && undefined !== optionData.post_draft_id) {
                    var postDraftId = optionData.post_draft_id;
                    var urlPost = optionData.url_post;
                    var elementDraftPost = $('.post_draft_' + postDraftId);
                    elementDraftPost.find('.draft-post-message').toggleClass('hide');
                    elementDraftPost.find('.link_succeed').attr('href', urlPost);
                    elementDraftPost.find('.draft-post-message-succeed').toggleClass('hide');
                    elementDraftPost.find('.dropdown').toggleClass('hide');
                }
            }

            // 新しいコメント通知の場合
            if (isNewCommentNotify) {
                var postId = data.post_id;
                var notifyBox = $("#Comments_new_" + String(postId));
                notifyNewComment(notifyBox);
            }
        });
    }
    pusher.subscribe('user_' + cake.data.user_id + '_team_' + cake.data.team_id).bind('bell_count', function (data) {
        //通知設定がoffもしくは自分自身が送信者の場合はなにもしない。
        if (!cake.notify_setting[data.flag_name]) {
          return;
        }
        if (cake.data.user_id == data.from_user_id) {
          return;
        }

        setNotifyCntToBellForMobileApp(1, true);
        setNotifyCntToBellAndTitle(getCurrentUnreadNotifyCnt() + 1);
    });
    pusher.subscribe('user_' + cake.data.user_id + '_team_' + cake.data.team_id).bind('msg_count', function (data) {
        //通知設定がoffもしくは自分自身が送信者の場合はなにもしない。
        if (!cake.notify_setting[data.flag_name]) {
          return;
        }

        // if display the topic page, nothing to do
        var topic_page_url = "/topics/" + data.topic_id + "/detail";
        if (location.pathname.indexOf(topic_page_url) !== -1) {
          return;
        }

        if (cake.data.user_id == data.from_user_id) {
          return;
        }
        if (cake.unread_msg_topic_ids.indexOf(data.topic_id) >= 0) {
          return;
        }
        cake.unread_msg_topic_ids.push(data.topic_id);

        setNotifyCntToMessageForMobileApp(1, true);
        setNotifyCntToMessageAndTitle(getMessageNotifyCnt() + 1);
    });

});

function appendSocketId(form, socketId) {
    $('<input>').attr({
        type: 'hidden',
        name: 'socket_id',
        value: socketId
    }).appendTo(form);
}

function notifyNewFeed() {
    var notifyBox = $(".feed-notify-box");
    var numArea = notifyBox.find(".num");
    var num = parseInt(numArea.html());
    var title = $("title");
    // Increment unread number
    if (num >= 1) {
        // top of feed
        numArea.html(num + 1);
        return;
    }

    // Case of not existing unread post yet
    numArea.html("1");
    notifyBox.css("display", function () {
        return "block";
    });

    // 通知をふんわり出す
    var i = 0.2;
    var roop = setInterval(function () {
        notifyBox.css("opacity", i);
        i = i + 0.2;
        if (i > 1) {
            clearInterval(roop);
        }
    }, 100);
}

function getCurrentUnreadNotifyCnt() {
    var $bellNum = $(".bellNum").first();
    var $numArea = $bellNum.find("span");
    return parseInt($numArea.html());
}

// notify boxにpage idをセット
function setPageTypeId() {
    var notifyBox = $(".feed-notify-box");
    var pageTypeId = cake.data.d;
    if (pageTypeId === "null") {
        return;
    }
    if (pageTypeId === "circle") {
        pageTypeId += "_" + cake.data.h;
    }
    notifyBox.attr("id", pageTypeId + "_feed_notify");
}

// notify boxのpage idをゲット
function getPageTypeId() {
    var pageTypeId = $(".feed-notify-box").attr("id");
    if (!pageTypeId) return "";
    return pageTypeId.replace("_feed_notify", "");
}

function viaIsSet(data) {
    var isExist = typeof( data ) !== 'undefined';
    if (!isExist) return false;
    return data;
}

function notifyNewComment(notifyBox) {
    var numInBox = notifyBox.find(".num");
    var num = parseInt(numInBox.html());

    hideCommentNotifyErrorBox(notifyBox);

    // Increment unread number
    if (num >= 1) {
        // top of feed
        numInBox.html(String(num + 1));
    } else {
        // Case of not existing unread post yet
        numInBox.html("1");
    }

    if (notifyBox.css("display") === "none") {
        notifyBox.css("display", "block");

        // 通知をふんわり出す
        var i = 0.2;
        var roop = setInterval(function () {
            notifyBox.css("opacity", i);
            i = i + 0.2;
            if (i > 1) {
                clearInterval(roop);
            }
        }, 100);
    }
}

function hideCommentNotifyErrorBox(notifyBox) {
    var errorBox = notifyBox.siblings(".new-comment-error");
    if (errorBox.attr("display") === "none") {
        return;
    }
    errorBox.css("display", "none");
}

function evNotifications(options) {
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
    if (!$(".layout-main").exists()) {
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
    var url = cake.url.notifications;

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

                $(".layout-main").html($posts);
                $posts.show();
            }

            //ローダーを削除
            $loader_html.remove();

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
}
