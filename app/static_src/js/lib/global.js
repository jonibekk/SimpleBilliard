"use strict";

Noty.overrideDefaults({
    theme    : 'bootstrap-v3',
    killer   : true,
    timeout  : 4000,
    progressBar : false,
});

// Sentry:js error tracking
if (cake.sentry_dsn && (cake.env_name !== 'local' && cake.env_name !== 'develop')) {
    Raven.config(
        cake.sentry_dsn,
        {
            environment: cake.env_name
        }
    ).install();
}

$.ajaxSetup({
    cache: false,
    timeout: 10000 // 10 sec
});

if (typeof String.prototype.startsWith != 'function') {
    // see below for better implementation!
    String.prototype.startsWith = function (str) {
        return this.indexOf(str) === 0;
    };
}

require.config({
    baseUrl: '/js/modules/'
});

// selectorの存在確認用
jQuery.fn.exists = function () {
    // TODO: Remove console log
    console.log("gl_basic.js: jQuery.fn.exists");
    return Boolean(this.length > 0);
}

// scrollbarの存在確認用
jQuery.fn.hasScrollBar = function () {
    // TODO: Remove console log
    console.log("gl_basic.js: jQuery.fn.hasScrollBar");
    return this.get(0) ? this.get(0).scrollHeight > this.innerHeight() : false;
}


$(function () {
    // TODO: Remove console log
    console.log("LOADING: globals.js");

    /**
     * ajaxで取得するコンテンツにバインドする必要のあるイベントは以下記述で追加
     */
    $(document).on("blur", ".blur-height-reset", evThisHeightReset);
    $(document).on("focus", ".click-height-up", evThisHeightUp);
    $(document).on("click", ".toggle-follow", evFollowGoal);
    //lazy load
    $(document).on("click", '.target-toggle-click', function (e) {
        e.preventDefault();
        imageLazyOn();
    });
    imageLazyOn();

    //Monitoring of the communication state of App Server | Appサーバーの通信状態の監視
    window.addEventListener("online", function () {
        updateNotifyCnt();
        updateMessageNotifyCnt();
        network_reachable = true;
    }, false);

    window.addEventListener("offline", function () {
        network_reachable = false;
    }, false);

    $(document).ajaxComplete(setChangeWarningForAjax);

    // Default tab
    setDefaultTab();

    // for setting the team_id_current in local storage
    clickToSetCurrentTeamId();

    // if team changed from other tab then don't allow user to proceed without reload
    $('body').click(function () {
        if (Number(cake.data.team_id) !== Number(localStorage.team_id_current)) {
            var r = confirm(cake.translation["Team has been changed, press ok to reload!"]);
            if (r == true) {
                document.location.reload(true);
                return false;
            } else {
                return false;
            }
        }
    });

    // Androidアプリかiosアプリの場合のみfastClickを実行する。
    // 　→iosでsafari/chromeでfastClick使用時、チェックボックス操作に不具合が見つかったため。
    if (cake.is_mb_app === 'true' || cake.is_mb_app_ios === 'true') {
        fastClick();
    }

    if (typeof cake.request_params.named.after_click !== 'undefined') {
        $("#" + cake.request_params.named.after_click).trigger('click');
    }
    if (typeof cake.request_params.after_click !== 'undefined') {
        $("#" + cake.request_params.after_click).trigger('click');
    }

    $(".click-show").on("click", function () {
            // TODO: Remove console log
            console.log("globals.js: click");
            $("#PostFormPicture").css("display", "block")
        }
    )

    setChangeWarningForAllStaticPage();
    warningCloseModal();
});

var network_reachable = true;

/**
 * 属性が存在するかチェック
 * 存在しない場合はエラーを吐いて終了
 * @param obj
 * @param attr_name
 */
function attrUndefinedCheck(obj, attr_name) {
    // TODO: Remove console log
    console.log("global.js: attrUndefinedCheck");
    if ($(obj).attr(attr_name) == undefined) {
        var msg = "'" + attr_name + "'" + " is undefined!";
        throw new Error(msg);
    }
}

/**
 * サニタイズ処理
 * DOMから取得するデータはサーバサイドのサニタイズがリセットされてしまうため、
 * 改めてこのメソッドでサニタイズする必要がある。
 *
 * @param string
 * @returns string
 */
function sanitize(string) {
    // TODO: Remove console log
    console.log("global.js: sanitize");
    if (typeof string !== 'string') {
        return string;
    }
    return string.replace(/[&'`"<>]/g, function (match) {
        return {
            '&': '&amp;',
            "'": '&#x27;',
            '`': '&#x60;',
            '"': '&quot;',
            '<': '&lt;',
            '>': '&gt;',
        }[match]
    });
}

/**
 *  仮アップロードされたファイルの有効期限（保存期限） が過ぎていないか確認
 *
 * @param formID
 * @returns {boolean}
 */
function checkUploadFileExpire(formID) {
    // TODO: Remove console log
    console.log("global.js: checkUploadFileExpire");
    var $form = $('#' + formID);

    var res = true;
    $form.find('input[type=hidden][name="data[file_id][]"]').each(function () {
        var $hidden = $(this);
        var now = Math.floor(new Date().getTime() / 1000);

        // ファイルの有効期限が切れている場合（30 秒余裕を持たす）
        if (now - parseInt($hidden.attr('data-uploaded'), 10) > cake.pre_file_ttl - 30) {
            var $uploadFileForm = $(document).data('uploadFileForm');

            // Dropzone の管理ファイルから外す
            var removed_file;
            for (var i = 0; i < $uploadFileForm._files[formID].length; i++) {
                if ($hidden.val() == $uploadFileForm._files[formID][i].file_id) {
                    removed_file = $uploadFileForm._files[formID].splice(i, 1)[0];
                    break;
                }
            }
            // hidden を削除
            $hidden.remove();
            // プレビューエリアを非表示にする
            $(removed_file.previewElement).fadeOut();

            res = false;
        }
    });
    if (!res) {
        new Noty({
            type: 'error',
            text: '<h4>'+cake.word.error+'</h4>'+cake.message.validate.dropzone_uploaded_file_expired,
        }).show();
    }
    return res;
}

function imageLazyOn($elm_obj) {
    // TODO: Remove console log
    console.log("global.js: imageLazyOn");
    var lazy_option = {
        bind: "event",
        attribute: "data-original",
        combined: true,
        delay: 100,
        visibleOnly: false,
        removeAttribute: false,
        onError: function (element) {
            if (element.attr('error-img') != undefined) {
                element.attr("src", element.attr('error-img'));
            }
        }
    };
    if ($elm_obj === undefined) {
        return $("img.lazy").lazy(lazy_option);
    }
    else {
        return $elm_obj.find("img.lazy").lazy(lazy_option);
    }
}

function setDefaultTab() {
    // TODO: Remove console log
    console.log("global.js: setDefaultTab");
    if (cake.common_form_type == "") {
        return;
    }
    switch (cake.common_form_type) {
        case "action":
            $('#CommonFormTabs li:eq(0) a').tab('show');
            break;
        case "post":
            $('#CommonFormTabs li:eq(1) a').tab('show');
            if (!isMobile()) {
                $('#CommonPostBody').focus();
            } else {
                $('#CommonPostBody').blur();
            }
            break;
        case "message":
            $('#CommonFormTabs li:eq(2) a').tab('show');
            if (!isMobile()) {
                $('#s2id_autogen1').focus();
            }
            break;
    }
}

function clickToSetCurrentTeamId() {
    // TODO: Remove console log
    console.log("global.js: clickToSetCurrentTeamId");
    if (typeof(Storage) !== "undefined") {
        localStorage.team_id_current = Number(cake.data.team_id);
    } else {
        console.log("Sorry, your browser does not support web storage...");
    }
};


function warningCloseModal() {
    // TODO: Remove console log
    console.log("global.js: warningCloseModal");
    warningAction($('.modal'));
}

//入力途中での警告表示
//静的ページのにはすべて適用
function setChangeWarningForAllStaticPage() {
    // TODO: Remove console log
    console.log("global.js: setChangeWarningForAllStaticPage");
    //オートコンプリートでchangeしてしまうのを待つ
    setTimeout(function () {
        var flag = false;
        $(":input").each(function () {
            var default_val = "";
            var changed_val = "";
            default_val = $(this).val();
            $(this).on("change keyup keydown", function () {
                if ($(this).hasClass('disable-change-warning')) {
                    return;
                }
                changed_val = $(this).val();
                if (default_val != changed_val) {
                    $(this).addClass("changed");
                } else {
                    $(this).removeClass("changed");
                }
            });
        });
        $(document).on('submit', 'form', function () {
            flag = true;
        });
        $(window).on("beforeunload", function () {
            if ($(".changed").length != "" && flag == false) {
                return cake.message.notice.a;
            }
        });
    }, 2000);
}

function isMobile() {
    // TODO: Remove console log
    console.log("globals.js: isMobile");
    var agent = navigator.userAgent;
    if (agent.search(/iPhone/) != -1 ||
        agent.search(/iPad/) != -1 ||
        agent.search(/iPod/) != -1 ||
        agent.search(/Android/) != -1
    ) {
        return true;
    }
    return false;
}

function copyToClipboard(url) {
    // TODO: Remove console log
    console.log("globals.js: copyToClipboard");
    window.prompt(cake.message.info.copy_url, url);
}

/**
 * Created by bigplants on 5/23/14.
 */
function getLocalDate() {
    // TODO: Remove console log
    console.log("globals.js: getLocalDate");
    var getTime = jQuery.now();
    var date = new Date(getTime);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    //noinspection UnnecessaryLocalVariableJS
    var fullDate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    return fullDate;
}

//入力途中での警告表示
//Ajaxエレメント中の適用したい要素にchange-warningクラスを指定
function setChangeWarningForAjax() {
    console.log("globals.js: setChangeWarningForAjax");
    var flag = true;
    $(".change-warning").keyup(function (e) {
        $(document).on('submit', 'form', function () {
            flag = false;
        });
        $("input[type=submit]").click(function () {
            flag = false
        });
        $(window).on('beforeunload', function () {
            if (e.target.value !== "" && flag) {
                return cake.message.notice.a;
            }
        })
    })
}

//アドレスバー書き換え
function updateAddressBar(url) {
    // TODO: Remove console log
    console.log("globals.js: updateAddressBar");
    if (typeof history.pushState == 'function') {
        try {
            history.pushState(null, null, url);
            return true;
        } catch (e) {
            window.location.href = url;
            return false;
        }
    }
}

function evThisHeightUp() {
    // TODO: Remove console log
    console.log("globals.js: evThisHeightUp");
    attrUndefinedCheck(this, 'after-height');
    var after_height = $(this).attr("after-height");
    $(this).height(after_height);
}

function evThisHeightReset() {
    // TODO: Remove console log
    console.log("globals.js: evThisHeightReset");
    $(this).css('height', "");
}

function evFollowGoal() {
    // TODO: Remove console log
    console.log("gl_basic.js: evFollowGoal");
    attrUndefinedCheck(this, 'goal-id');
    attrUndefinedCheck(this, 'data-class');
    var $obj = $(this);
    var goal_id = sanitize($obj.attr('goal-id'));
    var data_class = sanitize($obj.attr('data-class'));
    var url = cake.url.c;
    $.ajax({
        type: 'GET',
        url: url + goal_id,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                new Noty({
                    type: 'error',
                    text: '<h4>'+cake.word.error+'</h4>'+data.msg,
                }).show();
            }
            else {
                if (data.add) {
                    $("." + data_class + "[goal-id=" + goal_id + "]").each(function () {
                        $(this).children('span').text(cake.message.info.d);
                        $(this).children('i').hide();
                        $(this).removeClass('follow-off');
                        $(this).addClass('follow-on');
                    });
                }
                else {
                    $("." + data_class + "[goal-id=" + goal_id + "]").each(function () {
                        $(this).children('span').text(cake.message.info.z);
                        $(this).children('i').show();
                        $(this).removeClass('follow-on');
                        $(this).addClass('follow-off');
                    });
                }
            }
        },
        error: function () {
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.c,
            }).show();
        }
    });
    return false;
}