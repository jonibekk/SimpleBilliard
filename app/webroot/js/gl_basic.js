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

    //チームフィードの「もっと見る」のイベント
    //noinspection JSUnresolvedVariable
    $('.click-feed-read-more').bind('click', evFeedMoreView);
    //noinspection JSUnresolvedVariable
    $('.click-comment-all').bind('click', evCommentAllView);
});
/**
 * ajaxで取得するコンテンツにバインドする必要のあるイベントは以下記述で追加
 */
$(document).on("click", ".tiny-form-text", evShowAndThisWide);
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
$(function () {
    // PageTopヘッダ分ずらす
    var headH = 50;

    // PageTop
    $('a[href^=#], area[href^=#]').not('a[href=#], area[href=#]').each(function () {
        // jquery.easing
        jQuery.easing.quart = function (x, t, b, c, d) {
            return -c * ((t = t / d - 1) * t * t * t - 1) + b;
        };
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname && this.hash.replace(/#/, '')) {
            var $targetId = $(this.hash),
                $targetAnchor = $('[name=' + this.hash.slice(1) + ']');
            var $target = $targetId.length ? $targetId : $targetAnchor.length ? $targetAnchor : false;
            if ($target) {
                var targetOffset = $target.offset().top - headH;
                $(this).click(function () {
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, 500, 'quart');
                    return false;
                });
            }
        }
    });
    if (location.hash) {
        var hash = location.hash;
        window.scroll(0, headH);
        $('a[href=' + hash + ']').click();
    }
});