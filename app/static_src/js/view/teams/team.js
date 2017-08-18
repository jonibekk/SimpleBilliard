"use strict";

$(function () {
    $(document).on("submit", "form.ajax-csv-upload", uploadCsvFileByForm);

    // Insight 画面の処理
    if ($('#InsightForm').length) {
        require(['insight'], function (insight) {
            if ($('#InsightResult').length) {
                insight.insight.setup();
            }
            else if ($('#InsightCircleResult').length) {
                insight.circle.setup();
            }
            else if ($('#InsightRankingResult').length) {
                insight.ranking.setup();
            }
            insight.reload();
        });
    }
});

/**
 * uploading csv file from form.
 */
function uploadCsvFileByForm(e) {
    e.preventDefault();

    attrUndefinedCheck(this, 'loader-id');
    var loader_id = $(this).attr('loader-id');
    var $loader = $('#' + loader_id);
    attrUndefinedCheck(this, 'result-msg-id');
    var result_msg_id = $(this).attr('result-msg-id');
    var $result_msg = $('#' + result_msg_id);
    attrUndefinedCheck(this, 'submit-id');
    var submit_id = $(this).attr('submit-id');
    var $submit = $('#' + submit_id);
    //set display none for loader and result message elm

    $loader.removeClass('none');
    $result_msg.addClass('none');
    $result_msg.children('.alert').removeClass('alert-success');
    $result_msg.children('.alert').removeClass('alert-danger');
    $submit.attr('disabled', 'disabled');

    var $f = $(this);
    $.ajax({
        url: $f.prop('action'),
        method: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: new FormData(this),
        timeout: 600000 //10min
    }).done(function (data) {
        // 通信が成功したときの処理
        $result_msg
            .children('.alert').addClass(data.css)
            .children('.alert-heading').text(data.title);
        //noinspection JSUnresolvedVariable
        $result_msg.find('.alert-msg').text(data.msg);
        $submit.removeAttr('disabled');
    }).fail(function (data) {
        // 通信が失敗したときの処理
        $result_msg
            .children('.alert').addClass('alert-danger')
            .children('.alert-heading').text('Connection Error');
        //noinspection JSUnresolvedVariable
        $result_msg.find('.alert-msg').empty();
    }).always(function (data) {
        // 通信が完了したとき
        $result_msg.removeClass('none');
        $loader.addClass('none');
    });
}