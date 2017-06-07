/**
 * This file contains script related to page header
 */
"use strict";

$(function () {
    console.log("LOADING: header.js");

    // Click at Message Icon
    $(document).on("click", "#click-header-message", function (e) {
        // 未読件数が 0 件の場合は、直接メッセージ一覧ページに遷移させる
        if (getMessageNotifyCnt() == 0) {
            evMessageList(null);
            return;
        }

        initTitle();
        updateMessageListBox();
    });

    // Click at notifications icon
    var click_cnt = 0;
    $(document).on("click", "#click-header-bell", function () {
        click_cnt++;
        var isExistNewNotify = isExistNewNotify();
        initBellNum();
        initTitle();

        if (isExistNewNotify || click_cnt == 1 || do_reload_header_bellList) {
            updateListBox();
            do_reload_header_bellList = false;
        }

        function isExistNewNotify() {
            var newNotifyCnt = getNotifyCnt();
            if (newNotifyCnt > 0) {
                return true;
            }
            return false;
        }
    });

    // Auto update notify cnt
    if (cake.data.team_id) {
        setIntervalToGetNotifyCnt(cake.notify_auto_update_sec);
    }
    setNotifyCntToBellAndTitle(cake.new_notify_cnt);
    //メッセージ詳細ページの場合は実行しない。(メッセージ取得後に実行される為)
    if (cake.request_params.controller != 'posts' || cake.request_params.action != 'message') {
        setNotifyCntToMessageAndTitle(cake.new_notify_message_cnt);
    }

    // ヘッダーのお知らせ一覧ポップアップのオートローディング
    var prevScrollTop = 0;
    $('#bell-dropdown').scroll(function () {
        var $this = $(this);
        var currentScrollTop = $this.scrollTop();

        if (prevScrollTop < currentScrollTop && ($this.get(0).scrollHeight - currentScrollTop == $this.height())) {
            if (!autoload_more) {
                autoload_more = true;
                $('#NotifyDropDownReadMore').trigger('click');
            }
        }
        prevScrollTop = currentScrollTop;
    });

    //SubHeaderMenu
    var showNavFlag = false;
    var subNavbar = $("#SubHeaderMenu");
    $(window).scroll(function () {
        if ($(this).scrollTop() > 1) {
            if (showNavFlag == false) {
                showNavFlag = true;
                subNavbar.stop().animate({"top": "-60"}, 800);
            }
        } else {
            if (showNavFlag) {
                showNavFlag = false;
                var scroll_offset = 0;
                subNavbar.stop().animate({"top": scroll_offset}, 400);
            }
        }
    });
    $(window).scroll(function () {
        if ($(this).scrollTop() > 10) {
            $(".navbar").addClass("mod-box-shadow");
        } else {
            $(".navbar").removeClass("mod-box-shadow");
        }
    });

    $('#SubHeaderMenu a').click(function () {
        //既に選択中の場合は何もしない
        if ($(this).hasClass('sp-feed-active')) {
            return;
        }

        if ($(this).attr('id') == 'SubHeaderMenuFeed') {
            $('#SubHeaderMenuGoal').removeClass('sp-feed-active');
            $(this).addClass('sp-feed-active');
            //表示切り換え
            $('[role="goal_area"]').addClass('visible-md visible-lg');
            $('[role="main"]').removeClass('visible-md visible-lg');
        }
        else if ($(this).attr('id') == 'SubHeaderMenuGoal') {
            $('#SubHeaderMenuFeed').removeClass('sp-feed-active');
            $(this).addClass('sp-feed-active');
            //表示切り換え
            $('[role="main"]').addClass('visible-md visible-lg');
            $('[role="goal_area"]').removeClass('visible-md visible-lg');
            // HACK:reactの進捗グラフをリサイズするため架空要素(表示はしない)のクリックイベントを使用
            $('.js-flush-chart').trigger('click');
        }
        else {
            //noinspection UnnecessaryReturnStatementJS
            return;
        }
    });
});


/**
 * Initialize page title
 */
function initTitle() {
    console.log("header.js: initTitle");
    var $title = $("title");
    $title.text(sanitize($title.attr("origin-title")));
}

/**
 * Return Jquery object for Notification icon on the header
 * @returns {jQuery|HTMLElement}
 */
function getBellBoxSelector() {
    console.log("header.js: getBellBoxSelector");
    return $("#bellNum");
}

/**
 * Return count of notifications displayed on notification icon
 * @returns {Number}
 */
function getNotifyCnt() {
    console.log("header.js: getNotifyCnt");
    var $bellBox = getBellBoxSelector();
    return parseInt($bellBox.children('span').html(), 10);
}

/**
 * Initialize notification icon count to 0
 */
function initBellNum() {
    console.log("header.js: initBellNum");
    var $bellBox = getBellBoxSelector();
    $bellBox.css("opacity", 0);
    $bellBox.children('span').html("0");
}

/**
 * Return Jquery Object for Message icon on the header
 * @returns {jQuery|HTMLElement}
 */
function getMessageBoxSelector() {
    console.log("header.js: getMessageBoxSelector");
    return $("#messageNum");
}

/**
 * Return Count of messages displayed on messages icons
 * @returns {Number}
 */
function getMessageNotifyCnt() {
    console.log("header.js: getMessageNotifyCnt");
    var $box = getMessageBoxSelector();
    return parseInt($box.children('span').html(), 10);
}

/**
 * Initialize Message icon count to 0
 */
function initMessageNum() {
    console.log("header.js: initMessageNum");
    var $box = getMessageBoxSelector();
    $box.css("opacity", 0);
    $box.children('span').html("0");
}

function updateMessageListBox() {
    console.log("header.js: updateMessageListBox");
    var $messageDropdown = $("#message-dropdown");
    $messageDropdown.empty();
    var $loader_html = $('<li class="text-align_c"><i class="fa fa-refresh fa-spin"></i></li>');
    //ローダー表示
    $messageDropdown.append($loader_html);
    var url = cake.url.ag;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            //取得したhtmlをオブジェクト化
            var $notifyItems = data;
            $loader_html.remove();
            $messageDropdown.append($notifyItems);
            //画像をレイジーロード
            imageLazyOn();
        },
        error: function () {
        }
    });
    return false;
}

function evMessageList(options) {
    console.log("header.js: evMessageList");
    //とりあえずドロップダウンは隠す
    $(".has-notify-dropdown").removeClass("open");
    $('body').removeClass('notify-dropdown-open');

    var url = cake.url.message_list;
    location.href = url;
    return false;
}

function updateListBox() {
    console.log("header.js: updateListBox");
    var $bellDropdown = $("#bell-dropdown");
    $bellDropdown.empty();
    var $loader_html = $('<li class="text-align_c"><i class="fa fa-refresh fa-spin"></i></li>');
    //ローダー表示
    $bellDropdown.append($loader_html);
    var url = cake.url.g;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            //取得したhtmlをオブジェクト化
            var $notifyItems = data;
            $loader_html.remove();
            $bellDropdown.append($notifyItems);
            //画像をレイジーロード
            imageLazyOn();
        },
        error: function () {
        }
    });
    return false;
}

function setIntervalToGetNotifyCnt(sec) {
    console.log("header.js: setIntervalToGetNotifyCnt");
    setInterval(function () {
        updateNotifyCnt();
        updateMessageNotifyCnt();
    }, sec * 1000);
}

function updateNotifyCnt() {
    console.log("header.js: updateNotifyCnt");

    var url = cake.url.f + '/team_id:' + $('#SwitchTeam').val();
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (res) {
            if (res.error) {
                location.reload();
                return;
            }
            if (res != 0) {
                setNotifyCntToBellAndTitle(res);
            }
        },
        error: function () {
        }
    });
    return false;
}

function updateMessageNotifyCnt() {
    console.log("header.js: updateMessageNotifyCnt");
    var url = cake.url.af + '/team_id:' + $('#SwitchTeam').val();
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (res) {
            if (res.error) {
                location.reload();
                return;
            }
            setNotifyCntToMessageAndTitle(res);
            if (res != 0) {
            }
        },
        error: function () {
        }
    });
    return false;
}

function setNotifyCntToBellAndTitle(cnt) {
    console.log("header.js: setNotifyCntToBellAndTitle");
    var $bellBox = getBellBoxSelector();
    var existingBellCnt = parseInt($bellBox.children('span').html());

    if (cnt == 0) {
        return;
    }

    // set notify number
    if (parseInt(cnt) <= 20) {
        $bellBox.children('span').html(cnt);
        $bellBox.children('sup').addClass('none');
    } else {
        $bellBox.children('span').html(20);
        $bellBox.children('sup').removeClass('none');
    }
    updateTitleCount();

    if (existingBellCnt == 0) {
        displaySelectorFluffy($bellBox);
    }
    return;
}

function setNotifyCntToMessageAndTitle(cnt) {
    console.log("header.js: setNotifyCntToMessageAndTitle");
    var cnt = parseInt(cnt);
    var $bellBox = getMessageBoxSelector();
    var existingBellCnt = parseInt($bellBox.children('span').html());

    if (cnt != 0) {
        // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
        $('#click-header-message').next().addClass('dropdown-menu');
    }
    else {
        // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
        $('#click-header-message').next().removeClass('dropdown-menu');
    }

    // set notify number
    if (cnt == 0) {
        $bellBox.children('span').html(cnt);
        $bellBox.children('sup').addClass('none');
    } else if (cnt <= 20) {
        $bellBox.children('span').html(cnt);
        $bellBox.children('sup').addClass('none');
    } else {
        $bellBox.children('span').html(20);
        $bellBox.children('sup').removeClass('none');
    }
    updateTitleCount();

    if (existingBellCnt == 0 && cnt >= 1) {
        displaySelectorFluffy($bellBox);
    } else if (cnt == 0) {
        $bellBox.css("opacity", 0);
    }
    return;
}

// <title> に表示される通知数を更新する
function updateTitleCount() {
    console.log("header.js: updateTitleCount");
    var $title = $("title");
    var current_cnt = getNotifyCnt() + getMessageNotifyCnt();
    var current_str = '';

    if (current_cnt > 20) {
        current_str = '(20+)';
    }
    else if (current_cnt > 0) {
        current_str = '(' + current_cnt + ')';
    }
    $title.text(current_str + $title.attr("origin-title"));
}

function displaySelectorFluffy(selector) {
    console.log("header.js: displaySelectorFluffy");
    var i = 0.2;
    var roop = setInterval(function () {
        selector.css("opacity", i);
        i = i + 0.2;
        if (i > 1) {
            clearInterval(roop);
        }
    }, 100);
}

