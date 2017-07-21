/**
 * This file contains script related to comments on posts
 */
"use strict";

$(function () {
    // 投稿フォーム
    // Ctrl(Command) + Enter 押下時のコールバック
    bindCtrlEnterAction('#PostDisplayForm', function (e) {
        $('#PostSubmit').trigger('click');
    });

    $('#PostDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {}
    });

    $('#PostDisplayForm, #CommonActionDisplayForm, #MessageDisplayForm').change(function (e) {
        var $target = $(e.target);
        switch ($target.attr('id')) {
            case "CommonPostBody":
                $('#CommonActionName').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonActionName'));
                $('#CommonMessageBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonMessageBody'));
                break;
            case "CommonActionName":
                $('#CommonPostBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonPostBody'));
                $('#CommonMessageBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonMessageBody'));
                break;
            case "CommonMessageBody":
                $('#CommonPostBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonPostBody'));
                $('#CommonActionName').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonActionName'));
                break;
        }
    });

    /* For moving right_column by sub header */
    // TODO:delete after upgrading native app version
    if (typeof cake.request_params.named.after_click !== 'undefined') {
      $("#" + cake.request_params.named.after_click).trigger('click');
    }
    if (typeof cake.request_params.after_click !== 'undefined') {
      $("#" + cake.request_params.after_click).trigger('click');
    }
});
