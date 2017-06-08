/**
 * This file contains script related to comments on posts
 */
"use strict";

$(function () {
    console.log("LOADING: home.js");

    // 投稿フォーム
    // Ctrl(Command) + Enter 押下時のコールバック
    bindCtrlEnterAction('#PostDisplayForm', function (e) {
        $('#PostSubmit').trigger('click');
    });

    $('#PostDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {}
    });
});
