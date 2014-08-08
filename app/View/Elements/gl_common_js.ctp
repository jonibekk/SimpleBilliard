<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/gl_common_js.ctp -->
<?
//echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('jquery-1.11.1.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('jasny-bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
echo $this->Html->script('jquery.nailthumb.1.1.min');
echo $this->Html->script('jquery.autosize.min');
echo $this->Html->script('jquery.lazy.min');
echo $this->Html->script('lightbox.min');
//echo $this->Html->script('jquery.showmore.min');
echo $this->Html->script('jquery.showmore.min');
echo $this->Html->script('placeholders.min');
echo $this->Html->script('customRadioCheck.min');
echo $this->Html->script('select2.min');
echo $this->Html->script('gl_basic');
?>
<script type="text/javascript">
$(document).ready(function () {

    $('[rel="tooltip"]').tooltip();

    $('.validate').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        },
        fields: {
            "data[User][password]": {
                validators: {
                    stringLength: {
                        min: 8,
                        message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                    }
                }
            },
            "data[User][password_confirm]": {
                validators: {
                    identical: {
                        field: "data[User][password]",
                        message: '<?=__d('validate', "パスワードが一致しません。")?>'
                    }
                }
            }
        }
    });
    $('#PostDisplayForm').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        },
        fields: {}
    });

    //noinspection JSUnusedLocalSymbols
    $('#select2Member').select2({
        multiple: true,
        minimumInputLength: 2,
        placeholder: '<?=__d('gl',"スペルを入力してください。")?>',
        ajax: {
            url: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        }
    });
    //noinspection JSUnusedLocalSymbols
    $('#select2PostCircleMember').select2({
        multiple: true,
        minimumInputLength: 2,
        placeholder: '<?=__d('gl',"スペルを入力してください。")?>',
        ajax: {
            url: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_circles_users'])?>",
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        }
    });
});
function format(item) {
    return "<img style='width:14px;height: 14px' src='" + item.image + "'/> " + item.text;
}
function bindSelect2Members($this) {
    $this.find(".ajax_add_select2_members").select2({
        'val': null,
        multiple: true,
        minimumInputLength: 2,
        placeholder: '<?=__d('gl',"スペルを入力してください。")?>',
        ajax: {
            url: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }
        },
        initSelection: function (element, callback) {
            var circle_id = $(element).attr('circle_id');
            if (circle_id !== "") {
                $.ajax("<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_init_circle_members'])?>/" + circle_id, {
                    dataType: 'json'
                }).done(function (data) {
                    callback(data.results);
                });
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        }
    });
}
/**
 * Select2 translation.
 */
(function ($) {
    "use strict";

    //noinspection JSUnusedLocalSymbols
    $.fn.select2.locales['en'] = {
        formatNoMatches: function () {
            return "<?=__d('gl',"該当なし")?>";
        },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            return "<?=__d('gl',"あと")?>" + n + "<?=__d('gl',"文字入れてください")?>";
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            return "<?=__d('gl',"検索文字列が")?>" + n + "<?=__d('gl',"文字長すぎます")?>";
        },
        formatSelectionTooBig: function (limit) {
            return "<?=__d('gl',"最多で")?>" + limit + "<?=__d('gl',"項目までしか選択できません")?>";
        },
        formatLoadMore: function (pageNumber) {
            return "<?=__d('gl',"読込中･･･")?>";
        },
        formatSearching: function () {
            return "<?=__d('gl',"検索中･･･")?>";
        }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['en']);
})(jQuery);

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
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //一旦非表示
                $posts.hide();
                $("#" + parent_id).before($posts);
                //html表示
                $posts.show("slow", function () {
                    //もっと見る
                    showMore(this);
                });
                //ページ番号をインクリメント
                next_page_num++;
                //次のページ番号をセット
                $obj.attr('next-page-num', next_page_num);
                //ローダーを削除
                $loader_html.remove();
                //リンクを有効化
                $obj.removeAttr('disabled');
                //画像をレイジーロード
                imageLazyOn();

                $('.gl-custom-radio-check').customRadioCheck();

            }
            else {
                //ローダーを削除
                $loader_html.remove();
                //親を取得
                //noinspection JSCheckFunctionSignatures
                var $parent = $obj.parent();
                //「もっと読む」リンクを削除
                $obj.remove();
                //データが無かった場合はデータ無いよ。を表示
                $parent.append("<?=__d('gl','これ以上の投稿がありません。')?>");
            }
        },
        error: function () {
            alert("<?=__d('gl',"エラーが発生しました。データ取得できません。")?>");
        }
    });
    return false;
}

function evCommentAllView() {
    attrUndefinedCheck(this, 'parent-id');
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var parent_id = $obj.attr('parent-id');
    var get_url = $obj.attr('get-url');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    $.ajax({
        type: 'GET',
        url: get_url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                //一旦非表示
                $posts.hide();
                $("#" + parent_id).before($posts);
                //html表示
                $posts.show("slow", function () {
                    //もっと見る
                    showMore(this);
                });
                //ローダーを削除
                $loader_html.remove();
                //リンクを削除
                $obj.remove();
                //画像をレイジーロード
                imageLazyOn();

                $('.gl-custom-radio-check').customRadioCheck();

            }
            else {
                //ローダーを削除
                $loader_html.remove();
                //親を取得
                //noinspection JSCheckFunctionSignatures
                var $parent = $obj.parent();
                //「もっと読む」リンクを削除
                $obj.remove();
                //データが無かった場合はデータ無いよ。を表示
                $parent.append("<?=__d('gl','これ以上のコメントがありません。')?>");
            }
        },
        error: function () {
            alert("<?=__d('gl',"エラーが発生しました。データ取得できません。")?>");
        }
    });
    return false;
}
function evLike() {
    attrUndefinedCheck(this, 'like_count_id');
    attrUndefinedCheck(this, 'model_id');
    attrUndefinedCheck(this, 'like_type');

    var $obj = $(this);
    var like_count_id = $obj.attr('like_count_id');

    var like_type = $obj.attr('like_type');
    var url = null;
    var model_id = $obj.attr('model_id');
    if (like_type == "post") {
        url = "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_post_like'])?>" + "/" + model_id;
    }
    else {
        url = "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_comment_like'])?>" + "/" + model_id;
    }

    //リンクを非表示
    $obj.hide();
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert("<?=__d('gl',"エラーが発生しました。")?>");
            }
            else {
                //「いいね」した場合は「いいね取り消し」表示に
                //noinspection JSUnresolvedVariable
                if (data.created == true) {
                    $obj.text("<?=__d('gl',"いいね取り消し")?>");
                }
                //「いいね取り消し」した場合は「いいね」表示に
                else {
                    $obj.text("<?=__d('gl',"いいね！")?>");
                }
                $("#" + like_count_id).text(data.count);
                //ローダーを削除
                $loader_html.remove();
                $obj.show();
            }
        },
        error: function () {
            alert("<?=__d('gl',"エラーが発生しました。")?>");
        }
    });
    return false;
}
/**
 *
 * @param obj
 */
function showMore(obj) {
    obj = obj || null;
    if (obj) {
        $(obj).find('.showmore').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '125px',
            showText: '<i class="fa fa-angle-double-down"></i><?=__d('gl',"もっと見る")?>',
            hideText: '<i class="fa fa-angle-double-up"></i><?=__d('gl',"閉じる")?>'
        });
    }
    else {
        $('.showmore').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '125px',
            showText: '<i class="fa fa-angle-double-down"></i><?=__d('gl',"もっと見る")?>',
            hideText: '<i class="fa fa-angle-double-up"></i><?=__d('gl',"閉じる")?>'
        });
    }
}
$(function () {
    $(".develop--forbiddenLink").hover(
        function () {
            $(this).append($('<div class="develop--forbiddenLink__design">準備中です</div>'));
        },
        function () {
            $(this).find("div:last").remove();
        }
    );
});

<?if(isset($mode_view)):?>
<?if($mode_view == MODE_VIEW_TUTORIAL):?>
$("#modal_tutorial").modal('show');
<?endif;?>
<?endif;?>
</script>
<?
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
?>
<? if (ENV_NAME == "stg"): ?>
    <p class="ribbon ribbon-staging">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Staging</p>
<? elseif (ENV_NAME == "local"): ?>
    <p class="ribbon ribbon-local">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Local</p>
<?endif; ?>
<!-- END app/View/Elements/gl_common_js.ctp -->
