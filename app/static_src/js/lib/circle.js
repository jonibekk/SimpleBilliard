"use strict";
$(function () {
    // Register circle events.
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
