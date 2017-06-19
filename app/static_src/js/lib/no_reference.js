"use strict";

$(function () {
    // TODO: Remove console log
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
    $(document).on("change", ".change-select-target-hidden", evSelectOptionTargetHidden);

    //carousel
    $('.carousel').carousel({interval: false});

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
    // TODO: Remove console log
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
    // TODO: Remove console log
    console.log("no_reference.js: enabledAllInput");
    $(selector).find('input,select,textarea').removeAttr('disabled');
}

function disabledAllInput(selector) {
    // TODO: Remove console log
    console.log("no_reference.js: disabledAllInput");
    $(selector).find("input,select,textarea").attr('disabled', 'disabled');
}

function evTargetToggle() {
    // TODO: Remove console log
    console.log("no_reference.js: evTargetToggle");
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).toggle();
    return false;
}

function evSelectOptionTargetHidden() {
    // TODO: Remove console log
    console.log("gl_basic.js: evSelectOptionTargetHidden");
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'hidden-option-value');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var hidden_option_value = $obj.attr("hidden-option-value");
    if ($obj.val() == hidden_option_value) {
        $("#" + target_id).hide();
    }
    else {
        $("#" + target_id).show();
    }
    return true;
}
