/**
 * This file contains script to feed scrolling
 */
"use strict";

$(function () {
    // white list for back and forth cache to be cleared 
    // do not clear at post_edit or something like that for edit
    var clearPath = ['/']
    $(window).on("pageshow", function(event){
        // event.originalEvent.persisted is always false (kind of browser bug)
        // and checking dummy element value does not work for this kind of cache
        // so check the location here and clear textarea if needed
        if (clearPath.indexOf(location.pathname) >= 0) {
            // We want to clear all of form values but we will clear just each textarea just in case
            $('#CommonPostBody').val('')
            $('#CommonActionName').val('')
        }
    });

    // お知らせ一覧ページの次のページ読込みボタン
    $(document).on("click", ".click-notify-read-more-page", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this);
        var currentScrollTop = $this.scrollTop();
        evNotifyMoreView.call(this, e, {
            locationType: "page"
        });
    });

    // ヘッダーのお知らせ一覧ポップアップの次のページ読込みボタン
    $(document).on("click", ".click-notify-read-more-dropdown", function (e) {
        e.preventDefault();
        e.stopPropagation();
        evNotifyMoreView.call(this, e, {
            locationType: "dropdown",
            showLoader: function ($loader_html) {
                $('#bell-dropdown').append($('<div>').append($loader_html).css({
                    textAlign: 'center',
                }));

            },
            hideLoader: function ($loader_html) {
                $loader_html.remove();
            }
        });
    });

    // Translation enable button
    $(document).on("click", ".click-translation", evTranslation);

    $(document).on("click", ".click-feed-read-more", evFeedMoreView);
    $(document).on("click", ".btn-back-notifications", evNotifications);

    // Like button
    $(document).on("click", ".click-like", evLike);

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 2000) {
            if (!autoload_more) {
                autoload_more = true;

                if (network_reachable) {
                    var $FeedMoreReadLink = $('#FeedMoreReadLink');
                    var $GoalPageFollowerMoreLink = $('#GoalPageFollowerMoreLink');
                    var $GoalPageMemberMoreLink = $('#GoalPageMemberMoreLink');
                    var $GoalPageKeyResultMoreLink = $('#GoalPageKeyResultMoreLink');

                    if ($FeedMoreReadLink.is(':visible')) {
                        $FeedMoreReadLink.trigger('click');
                    }
                    if ($GoalPageFollowerMoreLink.is(':visible')) {
                        $GoalPageFollowerMoreLink.trigger('click');
                    }
                    if ($GoalPageMemberMoreLink.is(':visible')) {
                        $GoalPageMemberMoreLink.trigger('click');
                    }
                    if ($GoalPageKeyResultMoreLink.is(':visible')) {
                        $GoalPageKeyResultMoreLink.trigger('click');
                    }
                } else {
                    autoload_more = false;
                    return false;
                }
            }
        }
    });

    if (cake.data.j == "0") {
        $('#FeedMoreReadLink').trigger('click');
    }
    showMore();
});

function evTranslation() {
  attrUndefinedCheck(this, 'model_id');

  var $obj = $(this);
  var model_id = $obj.attr('model_id');

  var isOn = $obj.hasClass('on');
  var dd = $('#TranslationDropDown_' + model_id);
  $obj.toggleClass('on');
  if (isOn) {
    if (dd) {
      dd.hide();
    }
  } else {
    if (dd) {
      dd.show();
    }
  }

}

/**
 * Show more posts as user scroll the feed
 * @type {boolean}
 */
var action_autoload_more = false;
var autoload_more = false;
var feed_loading_now = false;
var do_reload_header_bellList = false;
function evFeedMoreView(options) {
    var opt = $.extend({
        recursive: false,
        loader_id: null
    }, options);

    //フィード読み込み中はキャンセル
    if (feed_loading_now) {
        return false;
    }
    feed_loading_now = true;

    attrUndefinedCheck(this, 'parent-id');
    attrUndefinedCheck(this, 'next-page-num');
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var parent_id = sanitize($obj.attr('parent-id'));
    var next_page_num = sanitize($obj.attr('next-page-num'));
    var get_url = $obj.attr('get-url');
    var month_index = sanitize($obj.attr('month-index'));
    var no_data_text_id = sanitize($obj.attr('no-data-text-id'));
    var oldest_post_time = sanitize($obj.attr('oldest-post-time')) || 0;
    var append_target_id = sanitize($obj.attr('append-target-id'));
    // この時間より前の投稿のみ読み込む
    var post_time_before = sanitize($obj.attr('post-time-before')) || 0;

    //リンクを無効化
    $obj.attr('disabled', 'disabled');

    //ローダー表示
    var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<i id="__feed_loader" class="fa fa-refresh fa-spin"></i>');
    if (!opt.recursive) {
        $obj.after($loader_html);
    }

    // URL生成
    // 投稿の更新時間が指定されていれば、それ以前の投稿のみを取得する
    var url = get_url + '/page:' + next_page_num;
    if (post_time_before) {
        url += '/post_time_before:' + post_time_before;
    }
    if (month_index != undefined && month_index > 0) {
        url = url + '/month_index:' + month_index;
    }
    $.ajax({
        type: 'GET',
        url: url,
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
                if (append_target_id != undefined) {
                    $("#" + append_target_id).append($posts);
                }
                else {
                    $("#" + parent_id).before($posts);
                }
                $posts.show();
                showMore($posts);

                //ページ番号をインクリメント
                next_page_num++;
                //次のページ番号をセット
                $obj.attr('next-page-num', next_page_num);
                //ローダーを削除
                $loader_html.remove();
                //リンクを有効化
                $obj.text(cake.message.info.e);
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

            // 取得したデータ件数が、１ページの表示件数未満だった場合
            if (data.count < data.page_item_num) {
                // 前月以前のデータを取得する必要がある場合
                if (month_index != undefined) {
                    month_index++;
                    $obj.attr('month-index', month_index);
                    //次のページ番号をセット
                    $obj.attr('next-page-num', 1);

                    // 取得した件数が 1 件以上の場合
                    // 「さらに読み込む」リンクを表示
                    if (data.count > 0) {
                        $obj.removeAttr('disabled');
                        $loader_html.remove();
                        $obj.text(cake.message.info.f);
                    }
                    // 取得したデータ件数が 0 件の場合
                    else {
                        // さらに古い投稿が存在する可能性がある場合は、再度同じ関数を呼び出す
                        if (data.start && data.start > oldest_post_time) {
                            setTimeout(function () {
                                feed_loading_now = false;
                                evFeedMoreView.call($obj[0], { recursive: true, loader_id: '__feed_loader' });
                            }, 200);
                            return;
                        }
                        // これ以上古い投稿が存在しない場合
                        else {
                            $loader_html.remove();
                            $("#" + no_data_text_id).show();
                            $('#' + parent_id).find('.panel-read-more-body').removeClass('panel-read-more-body').addClass('panel-read-more-body-no-data');
                            $obj.css("display", "none");
                            feed_loading_now = false;
                            return;
                        }
                    }
                }
                // 前月以前のデータを取得する必要がない場合
                else {
                    //ローダーを削除
                    $loader_html.remove();
                    $("#" + no_data_text_id).show();
                    $('#' + parent_id).find('.panel-read-more-body').removeClass('panel-read-more-body').addClass('panel-read-more-body-no-data');
                    //もっと読む表示をやめる
                    $obj.css("display", "none");
                }
            }
            action_autoload_more = false;
            autoload_more = false;
            feed_loading_now = false;
        },
        error: function () {
            feed_loading_now = false;
        },
    });
    return false;
}

/**
 *
 * @param obj
 */
function showMore(obj) {
    obj = obj || null;
    var showText = '<i class="fa fa-angle-double-down mr_5px"></i>' + cake.message.info.e;
    var hideText = '<i class="fa fa-angle-double-up mr_5px"></i>' + cake.message.info.h;
    if (obj) {
        $(obj).find('.showmore').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '128px',
            showText: showText,
            hideText: hideText
        });
        $(obj).find('.showmore-comment').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '105px',
            showText: showText,
            hideText: hideText
        });
        $(obj).find('.showmore-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '900px',
            showText: showText,
            hideText: hideText
        });
        $(obj).find('.showmore-comment-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '920px',
            showText: showText,
            hideText: hideText
        });
        $(obj).find('.showmore-init-none').showMore({
            speedDown: 100,
            speedUp: 100,
            height: '0px',
            showText: showText,
            hideText: hideText
        });
        $(obj).find('.showmore-action').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '42px',
            showText: showText,
            hideText: hideText
        });

    }
    else {
        $('.showmore').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '128px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '900px',
            showText: showText,
            hideText: hideText
        });

        $('.showmore-comment').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '105px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-comment-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '920px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-mini').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '60px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-xtra-mini').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '40px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-profile-content').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '80px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-init-none').showMore({
            speedDown: 100,
            speedUp: 100,
            height: '0px',
            showText: showText,
            hideText: hideText
        });
        $('.showmore-action').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '42px',
            showText: showText,
            hideText: hideText
        });
    }
}

/**
 * お知らせ一覧のページング処理
 *
 * @param e
 * @param params
 *          locationType: string  (*必須) 呼び出し元を表す文字列 'page' | 'dropdown'
 *          showLoader: function($loading_html)  ローディング画像の表示処理を行うコールバック関数
 *          hideLoader: function($loading_html)  ローディング画像の削除処理を行うコールバック関数
 * @returns {boolean}
 */
function evNotifyMoreView(e, params) {
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var oldest_score_id = $("ul.notify-" + params.locationType + "-cards").children("li.notify-card-list:last").attr("data-score");
    var get_url = $obj.attr('get-url');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    if (params.showLoader) {
        params.showLoader.call(this, $loader_html);
    }
    else {
        $obj.after($loader_html);
    }

    //url生成
    var url = get_url + '/' + String(oldest_score_id) + '/' + params.locationType;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            autoload_more = false;
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $notify = $(data.html);
                //一旦非表示
                $notify.hide();
                $(".notify-" + params.locationType + "-cards").append($notify);
                //html表示
                $notify.show("slow", function () {
                    //もっと見る
                    showMore(this);
                });
                //ローダーを削除
                if (params.hideLoader) {
                    params.hideLoader.call($obj.get(0), $loader_html);
                }
                else {
                    $loader_html.remove();
                }
                $obj.removeAttr('disabled');
                $("#ShowMoreNoData").hide();
                //画像をレイジーロード
                imageLazyOn();
                if (parseInt(data.item_cnt) < cake.new_notify_cnt) {
                    //ローダーを削除
                    $loader_html.remove();
                    //もっと読む表示をやめる
                    $(".feed-read-more").remove();
                }

            } else {
                //ローダーを削除
                $loader_html.remove();
                //もっと読む表示をやめる
                $(".feed-read-more").remove();
            }
        },
        error: function () {
            //ローダーを削除
            $loader_html.remove();
            $obj.removeAttr('disabled');
            $("#ShowMoreNoData").hide();
        }
    });
    return false;
}

function evLike() {
    attrUndefinedCheck(this, 'like_count_id');
    attrUndefinedCheck(this, 'model_id');
    attrUndefinedCheck(this, 'like_type');

    var $obj = $(this);
    var like_count_id = $obj.attr('like_count_id');
    var $like_count_text = $("#" + like_count_id);

    var like_type = $obj.attr('like_type');
    var url = null;
    var model_id = $obj.attr('model_id');
    $obj.toggleClass("liked");

    // ajax の結果を待たずに表示されているいいね数を変更する
    // ajax の結果が返ってきたら正しい数字で上書きされる
    var currentCount = parseInt($like_count_text.text(), 10);
    if ($obj.hasClass("liked")) {
        $like_count_text.text(currentCount + 1);
    }
    else {
        $like_count_text.text(currentCount - 1);
    }

    if (like_type == "post") {
        url = cake.url.d + model_id;
    }
    else {
        url = cake.url.e + model_id;
    }

    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert(cake.message.notice.d);
            }
            else {
                $like_count_text.text(data.count);
            }
        },
        error: function () {
            return false;
        }
    });
    return false;
}
