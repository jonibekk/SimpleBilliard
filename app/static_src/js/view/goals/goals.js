/**
 * This file contains script related to comments on goals
 */
"use strict";

$(function () {
    // TODO: Remove console log
    console.log("LOADING: goals.js");

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

    // アクションの編集画面の場合は、画像選択の画面をスキップし、
    // ajax で動いている select を選択済みにする
    var $button = $('#ActionForm').find('.post-action-image-add-button.skip');
    if ($button.length) {
        // 画像選択の画面をスキップ
        evTargetShowThisDelete.call($button.get(0));
        // ゴール選択の ajax 処理を動かす
        $('#GoalSelectOnActionForm').trigger('change');
    }

});
