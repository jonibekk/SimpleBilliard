"use strict";

$(function () {
    ///////////////////////////////////////////////////////////////////////////
    // Ctrl(Command) + Enter 押下時のコールバック
    ///////////////////////////////////////////////////////////////////////////

    // 投稿フォーム
    bindCtrlEnterAction('#PostDisplayForm', function (e) {
        $('#PostSubmit').trigger('click');
    });

    // メッセージフォーム
    bindCtrlEnterAction('#MessageDisplayForm', function (e) {
        $('#MessageSubmit').trigger('click');
    });

    // メッセージ個別ページ
    bindCtrlEnterAction('#message_text_input', function (e) {
        $('#message_submit_button').trigger('click');
    });


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
    $('#PostDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {}
    });
    $('#MessageDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {}
    });
    $('#CommonActionDisplayForm').bootstrapValidator({
        live: 'enabled',

        fields: {
            photo: {
                // All the email address field have emailAddress class
                selector: '.ActionResult_input_field',
                validators: {
                    callback: {
                        callback: function (value, validator, $field) {
                            var isEmpty = true,
                                // Get the list of fields
                                $fields = validator.getFieldElements('photo');
                            for (var i = 0; i < $fields.length; i++) {
                                if ($fields.eq(i).val() != '') {
                                    isEmpty = false;
                                    break;
                                }
                            }

                            if (isEmpty) {
                                //// Update the status of callback validator for all fields
                                validator.updateStatus('photo', validator.STATUS_INVALID, 'callback');
                                return false;
                            }
                            validator.updateStatus('photo', validator.STATUS_VALID, 'callback');
                            return true;
                        }
                    }
                }
            }
        }
    });
    $('.ActionResult_input_field').on('change', function () {
        $('#CommonActionDisplayForm').bootstrapValidator('revalidateField', 'photo');
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