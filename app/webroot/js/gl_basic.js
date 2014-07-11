$(document).ready(function () {
    //すべてのformで入力があった場合に行う処理
    $("select,input").change(function () {
        $(this).nextAll(".help-block" + ".text-danger").remove();
        if ($(this).is("[name='data[User][agree_tos]']")) {
            //noinspection JSCheckFunctionSignatures
            $(this).parent().parent().nextAll(".help-block" + ".text-danger").remove();
        }
    });
    //アップロード画像選択時にトリムして表示
    $('.fileinput').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 150, height: 150, fitDirection: 'center center'});
    });
    //チーム切り換え
    $('#SwitchTeam').change(function () {
        var val = $(this).val();
        var url = "/teams/ajax_switch_team/" + val;
        $.get(url, function (data) {
            location.href = data;
        });
    });
    //autosize
    //noinspection JSJQueryEfficiency
    $('textarea').autosize();
    //noinspection JSJQueryEfficiency
    $('textarea').show().trigger('autosize.resize');

    //tiny-form
    $(".tiny-form-text").bind("click", evShowAndThisWide);
    //チームフィードの「もっと見る」のイベント
    $('.click-feed-read-more').bind('click', evFeedMoreView);

});
/**
 * クリックした要素のheightを倍にし、
 * 指定した要素を表示する。(一度だけ)
 */
function evShowAndThisWide() {
    //クリック済みの場合は処理しない
    if ($(this).hasClass('clicked'))return;

    //autosizeを一旦、切る。
    $(this).trigger('autosize.destroy');
    var current_height = $(this).height();
    //現在のheightを倍にする。
    $(this).height(current_height * 2);
    //再度autosizeを有効化
    $(this).autosize();
    //submitボタンを表示
    $("#" + $(this).attr('target_show_id')).show();
    //クリック済みにする
    $(this).addClass('clicked');
}

function evFeedMoreView() {
    attrUndefinedCheck(this, 'parent-id');
    attrUndefinedCheck(this, 'next-page-num');
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var parent_id = $obj.attr('parent-id');
    var next_page_num = $obj.attr('next-page-num');
    var get_url = $obj.attr('get-url');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    $.ajax({
        type: 'GET',
        url: get_url + '/' + next_page_num,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //一旦非表示
                $posts.hide();
                $("#" + parent_id).before($posts);
                //html表示
                $posts.show("slow");
                next_page_num++;
                //次のページ番号をセット
                $obj.attr('next-page-num', next_page_num);
                //リンクを有効化
                $obj.removeAttr('disabled');
                //ローダーを削除
                $loader_html.remove();
            }
            else {
            }
        },
        error: function () {
            alert('問題がありました。');
        }
    });
    return false;
}
/**
 * Created by bigplants on 5/23/14.
 */
function getLocalDate() {
    var getTime = jQuery.now();
    var date = new Date(getTime);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    //noinspection UnnecessaryLocalVariableJS
    var fullDate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    return fullDate;
}
/**
 * 属性が存在するかチェック
 * 存在しない場合はエラーを吐いて終了
 * @param obj
 * @param attr_name
 */
function attrUndefinedCheck(obj, attr_name) {
    if ($(obj).attr(attr_name) == undefined) {
        var msg = "'" + attr_name + "'" + " is undefined!";
        throw new Error(msg);
    }
}
