"use strict";


$.clearInput = function ($obj) {
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
});

/**
 * selector の要素に Control(Command) + Enter 押下時のアクションを設定する
 *
 * @param selector
 * @param callback Control + Enter が押された時に実行されるコールバック関数
 */
var bindCtrlEnterAction = function (selector, callback) {
    console.log("forms.js: bindCtrlEnterAction");
    $(document).on('keydown', selector, function (e) {
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 13) {
            callback.call(this, e);
        }
    })
};