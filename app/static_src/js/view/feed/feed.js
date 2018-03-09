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
    Mention.bind($('#CommonActionName'))
    Mention.bind($('#CommonPostBody'))
    $('#PostDisplayForm').submit(function(e) {
        $('#ActualCommonPostBody').val($('#CommonPostBody')[0].submitValue())
    })
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
});

var bindCtrlEnterAction = function (selector, callback) {
    $(document).on('keydown', selector, function (e) {
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 13) {
            callback.call(this, e);
        }
    })
};
