"use strict";
$(function () {
    // Register circle events.
    $(document).on("click", ".js-dashboard-circle-list", evCircleFeed);
    $(document).on("click", ".circle-link", evCircleFeed);
    $(document).on("submit", "form.ajax-leave-circle", evAjaxLeaveCircle);
    $(document).on("submit", "form.ajax-edit-circle-admin-status", evAjaxEditCircleAdminStatus);
    $(document).on("click", '#CircleFilterMenuDropDown .modal-circle-setting', function (e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.hasClass('double_click')) {
            return false;
        }
        $this.addClass('double_click');
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function () {
            $(this).remove();
        });
        var url = $(this).attr('href');
        $.get(url, function (data) {
            $modal_elm.append(data);
            $modal_elm.modal();
            $modal_elm.find(".bt-switch").bootstrapSwitch({
                size: "small"
            })
            // スイッチ切り替えた時、即時データを更新する
                .on('switchChange.bootstrapSwitch', function () {
                    var $form = $('#CircleSettingForm');
                    $.ajax({
                        url: cake.url.circle_setting,
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
                            }
                            else {
                                new Noty({
                                    type: 'success',
                                    text: '<h4>'+cake.word.success+':</h4> '+res.msg,
                                    closeWith: ['click', 'button'],
                                }).show();
                            }
                        })
                        .fail(function () {
                            new Noty({
                                type: 'error',
                                text: cake.message.notice.d,
                            }).show();
                        });
                });
        }).done(function () {
            $('body').addClass('modal-open');
            $this.removeClass('double_click');
        });
    });
    $(document).on("click", '.modal-ajax-get-share-circles-users', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        var url = $(this).data('url');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                $modal_elm.modal();
            }).done(function () {
                $('body').addClass('modal-open');
            });
        }
    });
    $(document).on("click", '.modal-ajax-get-circle-edit', function (e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.hasClass('double_click')) {
            return false;
        }
        $this.addClass('double_click');

        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        var url = $(this).data('url');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                //noinspection JSUnresolvedFunction
                bindSelect2Members($modal_elm);
                //アップロード画像選択時にトリムして表示
                $modal_elm.find('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 96,
                        height: 96,
                        fitDirection: 'center center'
                    });
                    //EXIF
                    exifRotate(this);
                });

                // var $editCircleForm = $modal_elm.find('#EditCircleForm');
                // $editCircleForm.bootstrapValidator({
                //     excluded: [':disabled'],
                //     live: 'enabled',
                //
                //     fields: {
                //         "data[Circle][photo]": {
                //
                //             validators: {
                //                 file: {
                //                     extension: 'jpeg,jpg,png,gif',
                //                     type: 'image/jpeg,image/png,image/gif',
                //                     maxSize: 10485760,   // 10mb
                //                     message: cake.message.validate.c
                //                 }
                //             }
                //         }
                //     }
                // });
                // // submit ボタンが form 外にあるので、自力で制御する
                // $editCircleForm
                //     .on('error.field.bv', function (e) {
                //         $('#EditCircleFormSubmit').attr('disabled', 'disabled');
                //     })
                //     .on('success.field.bv', function (e) {
                //         $('#EditCircleFormSubmit').removeAttr('disabled');
                //     });
                $modal_elm.modal();
            }).done(function () {
                $this.removeClass('double_click');
                $('body').addClass('modal-open');
            }).fail(function () {
                $this.removeClass('double_click')
                new Noty({
                    type: 'error',
                    text: cake.message.notice.d,
                }).show();
            });
        }
    });
    // サークル編集画面のタブ切り替え
    // タブによって footer 部分を切り替える
    $(document).on('shown.bs.tab', '#CircleEdit a[data-toggle="tab"]', function (e) {
        var $target = $(e.target);
        var tabId = $target.attr('href').replace('#', '');
        $target.closest('#CircleEdit').find('.modal-footer').hide().filter('.' + tabId + '-footer').show();
    });


    $(".click-circle-trigger").on("click", function () {
        var txt = $(this).text();
        if ($(this).is('.on')) {
            $(this).text(txt.replace(/すべて表示/g, "閉じる")).removeClass("on");
            $(".circleListMore:nth-child(n+9)").css("display", "block");
            $(".circle-toggle-icon").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
        } else {
            $(this).text(txt.replace(/閉じる/g, "すべて表示")).addClass("on");
            $(".circleListMore:nth-child(n+9)").css("display", "none");
            $(".circle-toggle-icon").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
        }
    });

    // $('#AddCircleForm').bootstrapValidator({
    //     excluded: [':disabled'],
    //     live: 'enabled',
    //
    //     fields: {
    //         "data[Circle][photo]": {
    //
    //             validators: {
    //                 file: {
    //                     extension: 'jpeg,jpg,png,gif',
    //                     type: 'image/jpeg,image/png,image/gif',
    //                     maxSize: 10485760,   // 10mb
    //                     message: __("10MB or less, and Please select one of the formats of JPG or PNG and GIF.")
    //                 }
    //             }
    //         }
    //     }
    // });

    // ハンバーガーメニューのサークル未読点描画
    updateNotifyOnHamburger();
});

// サークル投稿リアルタイム通知
window.addEventListener("load", function() {
  setupCircleRealtimeNotification();
});

/**
 * サークル投稿リアルタイム通知
 */
function setupCircleRealtimeNotification() {

    var pusher = new Pusher(cake.pusher.key);
    var socketId = "";
    pusher.connection.bind('connected', function () {
        socketId = pusher.connection.socket_id;
        if (!cake.pusher.socket_id) {
            cake.pusher.socket_id = socketId;
        }
    });

    // サークル投稿リアルタイム通知設定
    if ($('.js-dashboard-circle-list-body')[0] !== undefined) {
        pusher.subscribe('team_' + cake.data.team_id).bind('circle_list_update', function (data) {
            var $circle_list = $('.js-dashboard-circle-list-body');
            var my_joined_circles = data.circle_ids;
            $.each(my_joined_circles, function (i, circle_id) {
                // $circlesはdashboardとhamburgerそれぞれのサークルリストを含むインスタンス。
                var $circles = $circle_list.children('[circle_id=' + circle_id + ']');
                $circles.each(function () {
                    var $circle = $(this);
                    if ($circle === undefined) {
                        return true;
                    }

                    // サークル未読数のアップデート
                    var $unread_box = $circle.find('.js-circle-count-box');
                    var unread_count = $unread_box.text().trim();
                    if (unread_count == "") {
                        $unread_box.text(1);
                    } else if (Number(unread_count) == 9) {

                        $unread_box.text("9+");
                    } else if (unread_count != "9+") {
                        $unread_box.html(Number(unread_count) + 1);
                    }

                    $circle.find('.js-dashboard-circle-list').removeClass('is-read').addClass('is-unread');
                    $circle.parent().prepend($circle);
                });
            });
            // サークルの未読件数がUIに反映されたら実行
            updateNotifyOnHamburger();
        });
    }

    return false;
}

// Ajax的なサークルフィード読み込み
function evCircleFeed(options) {

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
    var circle_id = sanitize($obj.attr('circle-id'));
    // DOMから取得し再度DOMに投入するデータなのでサニタイズを行う
    var title = sanitize($obj.attr('title'));
    var public_flg = sanitize($obj.attr('public-flg'));
    var team_all_flg = sanitize($obj.attr('team-all-flg'));
    var oldest_post_time = sanitize($obj.attr('oldest-post-time'));
    // URL生成
    var url = get_url.replace(/circle_feed/, "ajax_circle_feed");
    var more_read_url = get_url.replace(/\/circle_feed\//, "\/posts\/ajax_get_feed\/circle_id:");

    if ($obj.hasClass('is-hamburger')) {
        //ハンバーガーから来た場合は隠す
        $("#header-slide-menu").click();
    }
    //app-view-elements-feed-postsが存在しないところではajaxでコンテンツ更新しようにもロードしていない
    //要素が多すぎるので、おとなしくページリロードする
    //urlにcircle_feedを含まない場合も対象外
    if (!$("#app-view-elements-feed-posts").exists() || !$("#GlobalForms").exists() || !get_url.match(/circle_feed/)) {
        window.location.href = get_url;
        return false;
    }
    //サークルリストのわきに表示されている未読数リセット
    $obj.children(".js-circle-count-box").html("");
    $obj.children(".circle-count_box").children(".count-value").html("");
    $obj.removeClass('is-unread').addClass('is-read');
    updateNotifyOnHamburger();
    //アドレスバー書き換え
    if (!updateAddressBar(get_url)) {
        return false;
    }
    // メインカラム内の要素をリセット
    // FIXME:本来は「$("#app-view-elements-feed-posts").empty();」のようにメインカラム.フィード親要素をemptyにすれば良いだけだがHTMLの作り上そうなっていないので、上記のような処理をせざるをえない。
    $(".panel.panel-default").not(".nav-search-form-group, .feed-read-more, .global-form, .dashboard-krs, .js_progress_graph").remove();
    //ローダー表示
    var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>');
    if (!opt.recursive) {
        $("#app-view-elements-feed-posts").html($loader_html);
    }

    $("#FeedMoreRead").removeClass("hidden");
    // read more 非表示
    $("#FeedMoreReadLink").css("display", "none");

    //サークル名が長すぎる場合は切る
    var panel_title = title;
    if (title.length > 30) {
        panel_title = title.substr(0, 29) + "…";
    }

    $("#circle-filter-menu-circle-name").html(panel_title);
    $("#circle-filter-menu-member-url").data("url", "/circles/ajax_get_circle_members/circle_id:" + circle_id);
    $(".feed-share-range-file-url").attr("href", "/posts/attached_file_list/circle_id:" + circle_id);
    $('#postShareRangeToggleButton').removeAttr('data-toggle-enabled');
    if (public_flg == 1) {
        $("#feed-share-range-public-flg").children("i").removeClass("fa-lock").addClass("fa-unlock");
        $('#postShareRange').val("public");
        $('#PostSecretShareInputWrap').hide();
        $('#PostPublicShareInputWrap').show();

        $('#select2PostCircleMember').val("circle_" + circle_id);
        $('#select2PostSecretCircle').val("");
    } else {
        $("#feed-share-range-public-flg").children("i").removeClass("fa-unlock").addClass("fa-lock");
        $('#postShareRange').val("secret");
        $('#PostPublicShareInputWrap').hide();
        $('#PostSecretShareInputWrap').show();

        $('#select2PostCircleMember').val("");
        $('#select2PostSecretCircle').val("circle_" + circle_id);
    }
    $("#postShareRangeToggleButton").popover({
        'data-toggle': "popover",
        'placement': 'top',
        'trigger': "focus",
        'content': cake.word.share_change_disabled,
        'container': 'body'
    });
    // circle情報パネル表示
    $(".feed-share-range").css("display", "block");

    //Post後のリダイレクトURLを設定
    $("#PostRedirectUrl").val(get_url);

    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            var post_time_before = "";
            var image_url = data.circle_img_url;

            updateCakeValue(circle_id, title, image_url);

            setDefaultTab();
            initCircleSelect2();

            $('#OpenCircleSettingMenu').empty();

            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //notify一覧に戻るhtmlを追加
                //画像をレイジーロード
                imageLazyOn($posts);
                //一旦非表示
                $posts.hide();

                $("#app-view-elements-feed-posts").html($posts);
                //read moreの情報を差し替え

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

            if (data.post_time_before != null) {
                post_time_before = data.post_time_before;
            }

            $("#FeedMoreReadLink").attr("get-url", more_read_url);
            $("#FeedMoreReadLink").attr("month-index", 0);
            $("#FeedMoreReadLink").attr("next-page-num", 2);
            $("#FeedMoreReadLink").attr("oldest-post-time", oldest_post_time);
            $("#FeedMoreReadLink").attr("post-time-before", post_time_before);
            $("#FeedMoreReadLink").css("display", "inline");

            $("#circle-filter-menu-circle-member-count").html(data.circle_member_count);
            $(".js-circle-filter-menu-image").attr('src', image_url);

            //サークル設定メニュー生成
            if (!team_all_flg && data.user_status == "joined") {
                $('#OpenCircleSettingMenu')
                    .append('<li><a href="/posts/unjoin_circle/circle_id:' + circle_id + '">' + cake.word.leave_circle + '</a></li>');
            }
            if (data.user_status == "joined" || data.user_status == "admin") {
                $('#OpenCircleSettingMenu')
                    .append('<li><a href="/circles/ajax_setting/circle_id:' + circle_id + '" class="modal-circle-setting">' + cake.word.config + '</a></li></ul>');
            }

            $loader_html.remove();
            action_autoload_more = false;
            autoload_more = false;
            feed_loading_now = false;
            do_reload_header_bellList = true;
        },
        error: function () {
            feed_loading_now = false;
        }
    });
    return false;
}

function updateNotifyOnHamburger() {
    var is_visible = $('.circle-list-in-hamburger').css("display") !== "none";
    var existUnreadCircle = $('.circle-list-in-hamburger').find('.js-dashboard-circle-list').hasClass('is-unread');
    if (existUnreadCircle && is_visible) {
        $('.js-unread-point-on-hamburger').removeClass('is-read');
    } else {
        $('.js-unread-point-on-hamburger').addClass('is-read');
    }
}

// サークルフィード用のcake value 更新
function updateCakeValue(circle_id, title, image_url) {

    //サークルフィードでは必ずデフォルト投稿タイプはポスト
    cake.common_form_type = "post";

    cake.data.b = function (element, callback) {
        var data = [];
        var current_circle_item = {
            id: "circle_" + circle_id,
            text: title,
            image: image_url
        };

        data.push(current_circle_item);
        callback(data);
    }

    cake.data.select2_secret_circle = function (element, callback) {
        var data = [];
        var current_circle_item = {
            id: "circle_" + circle_id,
            text: title,
            image: image_url,
            locked: true
        };
        data.push(current_circle_item);
        callback(data);
    }
}

function evAjaxLeaveCircle(e) {
    e.preventDefault();

    var $this = $(this);
    var user_id = $this.attr('data-user-id');

    $.ajax({
        url: $this.attr('action'),
        type: 'POST',
        dataType: 'json',
        processData: false,
        data: $this.serialize()
    })
        .done(function (data) {
            // 処理失敗時
            if (data.error) {
                new Noty({
                    type: 'error',
                    text: '<h4>'+cake.word.error+'</h4>'+data.message.text,
                }).show();
            }
            // 処理成功時
            else {
                new Noty({
                    type: 'success',
                    text: '<h4>'+cake.word.success+'</h4>'+data.message.text,
                }).show();
                // 操作者自身の情報更新した場合
                if (data.self_update) {
                    window.location.href = '/';
                    return;
                }
                // 操作者以外の情報を更新した場合
                else {
                    var $member_row = $('#edit-circle-member-row-' + user_id);
                    $member_row.fadeOut('fast', function () {
                        $(this).remove();
                    });
                }
            }
        })
        .fail(function (data) {
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.d,
            }).show();
        });
}

function evAjaxEditCircleAdminStatus(e) {
    e.preventDefault();

    var $this = $(this);
    var user_id = $this.attr('data-user-id');

    $.ajax({
        url: $this.attr('action'),
        type: 'POST',
        dataType: 'json',
        processData: false,
        data: $this.serialize()
    })
        .done(function (data) {
            // 処理失敗時
            if (data.error) {
                new Noty({
                    type: 'error',
                    text: '<h4>'+cake.word.error+'</h4>'+data.message.text,
                }).show();
            }
            // 処理成功時
            else {
                new Noty({
                    type: 'success',
                    text: '<h4>'+cake.word.success+'</h4>'+data.message.text,
                }).show();

                // 操作者自身を情報を更新した場合
                if (data.self_update) {
                    window.location.href = '/';
                    return;
                }
                // 操作者以外の情報を更新した場合
                else {
                    var $member_row = $('#edit-circle-member-row-' + user_id);
                    // 非管理者 -> 管理者 の場合
                    if (data.result.admin_flg == "1") {
                        $member_row.find('.item-for-non-admin').hide();
                        $member_row.find('.item-for-admin').show();
                    }
                    // 管理者 -> 非管理者 の場合
                    else {
                        $member_row.find('.item-for-admin').hide();
                        $member_row.find('.item-for-non-admin').show();
                    }
                }
            }
        })
        .fail(function (data) {
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.d,
            }).show();
        });
}

var circleListDashboard=document.getElementsByClassName('dashboard-circle-list-body')[0],
    bannerExist = document.getElementsByClassName('banner-alert'),
    bannerOffset = 0;

$('.circle-list-in-hamburger').find('.js-dashboard-circle-list').click(function(){
    $('.js-nav-toggle').click();
});
