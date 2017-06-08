"use strict";

$(function () {
    console.log("LOADING: no_reference.js");

    // メッセージフォーム submit 時
    $(document).on('submit', '#MessageDisplayForm', function (e) {
        return checkUploadFileExpire('messageDropArea');
    });

    // メッセージフォーム
    bindCtrlEnterAction('#MessageDisplayForm', function (e) {
        $('#MessageSubmit').trigger('click');
    });
    // メッセージ個別ページ
    bindCtrlEnterAction('#message_text_input', function (e) {
        $('#message_submit_button').trigger('click');
    });

    $('#MessageDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {}
    });

    $(document).on('keyup', '#message_text_input', function () {
        autosize($(this));
        //$('body').animate({
        //    scrollTop: $(document).height()
        //});
    });
    $(document).on("click", ".target-toggle", evTargetToggle);
    //マイページのゴール切替え
    $('#SwitchGoalOnMyPage').change(function () {
        var goal_id = $(this).val();
        if (goal_id == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/goal_id:" + goal_id;
        }
        location.href = url;
    });
});

function ajaxAppendCount(id, url) {
    console.log("no_reference.js: ajaxAppendCount");
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    $('#' + id).append($loader_html);
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            //ローダーを削除
            $loader_html.remove();
            //カウント数を表示
            $('#' + id).text(data.count);
        },
        error: function () {
        }
    });
    return false;
}

function enabledAllInput(selector) {
    console.log("no_reference.js: enabledAllInput");
    $(selector).find('input,select,textarea').removeAttr('disabled');
}

function disabledAllInput(selector) {
    console.log("no_reference.js: disabledAllInput");
    $(selector).find("input,select,textarea").attr('disabled', 'disabled');
}

// reset bell notify num call from app.
function resetBellNum() {
    console.log("no_reference.js: resetBellNum");
    initBellNum();
    var url = cake.url.g;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            updateNotifyCnt();
        },
        error: function () {
            // do nothing.
        }
    });
}

// reset bell message num call from app.
function resetMessageNum() {
    console.log("no_reference.js: resetMessageNum");
    initMessageNum();
    var url = cake.url.ag;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            // do nothing.
        },
        error: function () {
            // do nothing.
        }
    });
}

function isOnline() {
    console.log("no_reference.js: isOnline");
    return Boolean(network_reachable);
}

function evTargetToggle() {
    console.log("no_reference.js: evTargetToggle");
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).toggle();
    return false;
}