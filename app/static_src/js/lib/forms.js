"use strict";


$.clearInput = function ($obj) {
    // TODO: Remove console log
    console.log("forms.js: $.clearInput");
    $obj.find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
    $obj.bootstrapValidator('resetForm', true);
};

$(function () {
    // TODO: Remove console log
    console.log("LOADING: forms.js");

    $('[rel="tooltip"]').tooltip();
    $('.validate').bootstrapValidator({
        live: 'enabled',
        fields: {
            "data[User][password]": {
                validators: {
                    stringLength: {
                        min: 8,
                        message: cake.message.validate.a
                    },
                    regexp: {
                        regexp: /^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/,
                        message: cake.message.validate.e
                    }
                }
            },
            "data[User][password_confirm]": {
                validators: {
                    stringLength: {
                        min: 8,
                        message: cake.message.validate.a
                    },
                    identical: {
                        field: "data[User][password]",
                        message: cake.message.validate.b
                    }
                }
            },
            "validate-checkbox": {
                selector: '.validate-checkbox',
                validators: {
                    choice: {
                        min: 1,
                        max: 1,
                        message: cake.message.validate.d
                    }
                }
            }
        }
    });

    //form二重送信防止
    $(document).on('submit', 'form', function () {
        $(this).find('input:submit').attr('disabled', 'disabled');
    });

    //すべてのformで入力があった場合に行う処理
    $("select,input").change(function () {
        $(this).nextAll(".help-block" + ".text-danger").remove();
        if ($(this).is("[name='data[User][agree_tos]']")) {
            //noinspection JSCheckFunctionSignatures
            $(this).parent().parent().nextAll(".help-block" + ".text-danger").remove();
        }
    });
    $(document).on("focus", ".tiny-form-text", evShowAndThisWide);
    $(document).on("keyup", ".tiny-form-text-change", evShowAndThisWide);
    $(document).on("click", ".tiny-form-text-close", evShowAndThisWideClose);
    $('.custom-radio-check').customRadioCheck();
    //bootstrap switch
    $(".bt-switch").bootstrapSwitch();
    //bootstrap tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    autosize($('textarea:not(.not-autosize)'));
    $('textarea:not(.not-autosize)').show().trigger('autosize.resize');
});

/**
 * selector の要素に Control(Command) + Enter 押下時のアクションを設定する
 *
 * @param selector
 * @param callback Control + Enter が押された時に実行されるコールバック関数
 */
var bindCtrlEnterAction = function (selector, callback) {
    // TODO: Remove console log
    console.log("forms.js: bindCtrlEnterAction");
    $(document).on('keydown', selector, function (e) {
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 13) {
            callback.call(this, e);
        }
    })
};

/**
 * クリックした要素のheightを倍にし、
 * 指定した要素を表示する。(一度だけ)
 */
function evShowAndThisWide() {
    // TODO: Remove console log
    console.log("forms.js: evShowAndThisWide");
    //クリック済みの場合は処理しない
    if ($(this).hasClass('clicked'))return;

    //KRのセレクトオプションを取得する。
    if ($(this).hasClass('add-select-options')) {
        setSelectOptions($(this).attr('add-select-url'), $(this).attr('select-id'));
    }
    //autosizeを一旦、切る。
    $(this).trigger('autosize.destroy');
    var current_height = $(this).height();
    if ($(this).attr('init-height') == undefined) {
        $(this).attr('init-height', current_height);
    }
    //$(this).attr('init-height', current_height);
    //現在のheightを倍にする。
    $(this).height(current_height * 2);
    //再度autosizeを有効化
    autosize($(this));

    //submitボタンを表示
    if ($(this).attr('target_show_id') != undefined) {
        var target = $(this).attr('target_show_id');

        var target = target.split(',');
        jQuery.each(target, function () {
            $("#" + this).show();
        });
    }

    //クリック済みにする
    $(this).addClass('clicked');
}


function evShowAndThisWideClose() {
    // TODO: Remove console log
    console.log("gl_basic.js: evShowAndThisWideClose");
    attrUndefinedCheck(this, 'target-id');
    var target_id = $(this).attr("target-id");
    var $target = $("#" + target_id);
    $target.removeClass('clicked');
    if ($target.attr('init-height') != undefined) {
        $target.height($target.attr('init-height'));
    }
    $("#" + $target.attr('target_show_id')).hide();
    return false;
}


function setSelectOptions(url, select_id, target_toggle_id, selected) {
    // TODO: Remove console log
    console.log("forms.js: setSelectOptions");
    var options_elem = '<option value="">' + cake.word.k + '</option>';
    $.get(url, function (data) {
        if (data.length == 0) {
            $("#" + select_id).empty().append('<option value="">' + cake.word.l + '</option>');
        } else {
            $.each(data, function (k, v) {
                var selected_attr = selected == k ? " selected=selected" : "";
                var option = '<option value="' + k + '"' + selected_attr + '>' + v + '</option>';
                options_elem += option;
            });
            $("#" + select_id).empty().append(options_elem);
        }
        if (typeof target_toggle_id != 'undefined' && target_toggle_id != null) {
            if (data.length == 0) {
                $("#" + target_toggle_id).addClass('none');
            }
            else {
                $("#" + target_toggle_id).removeClass('none');
            }
        }
    });
}