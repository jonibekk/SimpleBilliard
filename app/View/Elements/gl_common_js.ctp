<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 * @var                    $my_member_status
 */
?>
<!-- START app/View/Elements/gl_common_js.ctp -->
<?
//echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('jquery-1.11.1.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('jasny-bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bootstrap-switch.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
echo $this->Html->script('jquery.nailthumb.1.1.min');
echo $this->Html->script('jquery.autosize.min');
echo $this->Html->script('jquery.lazy.min');
echo $this->Html->script('lightbox.min');
echo $this->Html->script('jquery.showmore.min');
echo $this->Html->script('ZeroClipboard.min');
echo $this->Html->script('placeholders.min');
echo $this->Html->script('customRadioCheck.min');
echo $this->Html->script('select2.min');
echo $this->Html->script('bootstrap-datepicker.min');
echo $this->Html->script('locales/bootstrap-datepicker.ja');
echo $this->Html->script('moment.min');
echo $this->Html->script('gl_basic');
?>
<script type="text/javascript">
    var cake = {};
    cake.message = {
        "a": "<?=__d('gl',"入力が途中です。このまま移動しますか？")?>",
        "b": "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
        "c": "<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>",
        "d": "<?=__d('validate', "パスワードが一致しません。")?>",
        "e": "<?=__d('gl',"スペルを入力してください。")?>",
        "f": "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
        "g": "<?=__d('gl',"自分のみ")?>",
        "h": "<?=__d('gl',"参加")?>",
        "i": "<?=__d('gl',"不参加")?>",
        "j": "<?=__d('gl',"該当なし")?>",
        "k": "<?=__d('gl',"あと")?>",
        "l": "<?=__d('gl',"文字入れてください")?>",
        "m": "<?=__d('gl',"検索文字列が")?>",
        "n": "<?=__d('gl',"文字長すぎます")?>",
        "o": "<?=__d('gl',"最多で")?>",
        "p": "<?=__d('gl',"項目までしか選択できません")?>",
        "q": "<?=__d('gl',"読込中･･･")?>",
        "r": "<?=__d('gl',"検索中･･･")?>",
        "s": "<?=__d('gl',"フォロー中")?>",
        "t": "<?=__d('gl',"フォロー")?>",
        "u": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "v": "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
        "w": "<?=__d('gl', "もっと見る ▼") ?>",
        "x": "<?=__d('gl', "さらに以前の投稿を読み込む ▼") ?>",
        "y": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "z": "<?=__d('gl','これ以上のコメントがありません。')?>",
        "aa": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "bb": "<?=__d('gl',"エラーが発生しました。")?>",
        "cc": "<?=__d('gl',"もっと見る")?>",
        "dd": "<?=__d('gl',"閉じる")?>",
        "ee": "<?=__d('validate',"開始日が期限を過ぎています。")?>",
        "ff": "<?=__d('validate',"期限が開始日以前になっています。")?>"
    };
    cake.data = {
        "a": <?=isset($select2_default)?$select2_default:"[]"?>,
        "b": function (element, callback) {
            <?if(isset($current_circle)&&!empty($current_circle)):?>
                var data = [
                    {
                        id: "circle_<?=$current_circle['Circle']['id']?>",
                        text: "<?=h($current_circle['Circle']['name'])?>",
                        image: "<?=$this->Upload->uploadUrl($current_circle, 'Circle.photo', ['style' => 'small'])?>"
                    }
                ];
            <?else:?>
                var data = [
                    {
                        'id': 'public',
                        'text': "<?=__d('gl',"チーム全体")?>",
                        'image': "<?=isset($my_member_status)?$this->Upload->uploadUrl($my_member_status, 'Team.photo', ['style' => 'small']):null?>"
                    }
                ];
            <?endif;?>
                callback(data);
            }
    };
    cake.url = {
        "a": "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
        "b": "<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_init_circle_members'])?>/",
        "c": "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_toggle_follow'])?>",
        "d": "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_post_like'])?>",
        "e": "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_comment_like'])?>"
    };



    $(document).ready(function () {
        //入力途中での警告表示
        $("input,select,textarea").change(function () {
            if (!$(this).hasClass('disable-change-warning')) {
                $(window).on('beforeunload', function () {
                    return cake.message.a;
                });
            }
        });
        $("input[type=submit]").click(function () {
            $(window).off('beforeunload');
        });

        //noinspection JSUnresolvedFunction
        var client = new ZeroClipboard($('.copy_me'));
        //noinspection JSUnusedLocalSymbols
        client.on("ready", function (readyEvent) {
            client.on("aftercopy", function (event) {
                alert(cake.message.b + ": " + event.data["text/plain"]);
            });
        });

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
                            message: cake.message.c
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: cake.message.d
                        }
                    }
                }
            }
        });
        $('#PostDisplayForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {},
            fields: {}
        });

        //noinspection JSUnusedLocalSymbols
        $('#select2Member').select2({
            multiple: true,
            minimumInputLength: 2,
            placeholder: cake.message.e,
            ajax: {
                url: cake.message.f,
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
                    return {results: data.results};
                }
            },
            formatSelection: format,
            formatResult: format,
            escapeMarkup: function (m) {
                return m;
            },
            containerCssClass: "select2Member"
        });
        //noinspection JSUnusedLocalSymbols,JSDuplicatedDeclaration
        $('#select2PostCircleMember').select2({
            multiple: true,
            placeholder: cake.message.g,
            data: cake.data.a,
            initSelection: cake.data.b,
            formatSelection: format,
            formatResult: format,
            dropdownCssClass: 's2-post-dropdown',
            escapeMarkup: function (m) {
                return m;
            },
            containerCssClass: "select2PostCircleMember"
        });
        $(document).on("click", '.modal-ajax-get-public-circles', function (e) {
            e.preventDefault();
            var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
            $modal_elm.on('hidden.bs.modal', function (e) {
                $(this).remove();
            });
            $modal_elm.modal();
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $(url).modal('open');
            } else {
                $.get(url, function (data) {
                    $modal_elm.append(data);
                    $modal_elm.find(".bt-switch").bootstrapSwitch({
                        size: "small",
                        onText: cake.message.h,
                        offText: cake.message.i
                    });
                }).success(function () {
                    $('body').addClass('modal-open');
                });
            }
        });

    });
    function format(item) {
        return "<img style='width:14px;height: 14px' class='select2-item-img' src='" + item.image + "' alt='icon' /> " + "<span class='select2-item-txt'>" + item.text + "</span";
    }
    function bindSelect2Members($this) {
        //noinspection JSUnusedLocalSymbols
        $this.find(".ajax_add_select2_members").select2({
            'val': null,
            multiple: true,
            minimumInputLength: 2,
            placeholder: cake.message.e,
            ajax: {
                url: cake.url.a,
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
                    return {results: data.results};
                }
            },
            initSelection: function (element, callback) {
                var circle_id = $(element).attr('circle_id');
                if (circle_id !== "") {
                    $.ajax(cake.url.b + circle_id, {
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
            },
            containerCssClass: "select2Member"
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
                return cake.message.j;
            },
            formatInputTooShort: function (input, min) {
                var n = min - input.length;
                return cake.message.k + n + cake.message.l;
            },
            formatInputTooLong: function (input, max) {
                var n = input.length - max;
                return cake.message.m + n + cake.message.n;
            },
            formatSelectionTooBig: function (limit) {
                return cake.message.o + limit + cake.message.p;
            },
            formatLoadMore: function (pageNumber) {
                return cake.message.q;
            },
            formatSearching: function () {
                return cake.message.r;
            }
        };

        $.extend($.fn.select2.defaults, $.fn.select2.locales['en']);
    })(jQuery);

    function evFollowGoal() {
        attrUndefinedCheck(this, 'goal-id');
        attrUndefinedCheck(this, 'data-class');
        var $obj = $(this);
        var kr_id = $obj.attr('goal-id');
        var data_class = $obj.attr('data-class');
        var url = cake.url.c;
        $.ajax({
            type: 'GET',
            url: url + '/' + kr_id,
            async: true,
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    new PNotify({
                        type: 'error',
                        text: data.msg
                    });
                }
                else {
                    if (data.add) {
                        $("." + data_class + "[goal-id=" + kr_id + "]").each(function () {
                            $(this).children('span').text(cake.message.s);
                            $(this).children('i').hide();
                            $(this).removeClass('follow-off');
                            $(this).addClass('follow-on');
                        });
                    }
                    else {
                        $("." + data_class + "[goal-id=" + kr_id + "]").each(function () {
                            $(this).children('span').text(cake.message.s);
                            $(this).children('i').show();
                            $(this).removeClass('follow-on');
                            $(this).addClass('follow-off');
                        });
                    }
                }
            },
            error: function () {
                new PNotify({
                    type: 'error',
                    text: cake.message.u
                });
            }
        });
        return false;
    }

    function getModalPostList(e) {
        e.preventDefault();

        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        //noinspection CoffeeScriptUnusedLocalSymbols,JSUnusedLocalSymbols
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        $modal_elm.modal();
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                //クリップボードコピーの処理を追加
                //noinspection JSUnresolvedFunction
                var client = new ZeroClipboard($modal_elm.find('.copy_me'));
                //noinspection JSUnusedLocalSymbols
                client.on("ready", function (readyEvent) {
                    client.on("aftercopy", function (event) {
                        alert(cake.message.v + ": " + event.data["text/plain"]);
                    });
                });
                //画像をレイジーロード
                imageLazyOn();
                //画像リサイズ
                $modal_elm.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 50,
                        height: 50,
                        fitDirection: 'center center'
                    });
                });

                $modal_elm.find('.custom-radio-check').customRadioCheck();

            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    }
    function evFeedMoreView() {
        attrUndefinedCheck(this, 'parent-id');
        attrUndefinedCheck(this, 'next-page-num');
        attrUndefinedCheck(this, 'get-url');

        var $obj = $(this);
        var parent_id = $obj.attr('parent-id');
        var next_page_num = $obj.attr('next-page-num');
        var get_url = $obj.attr('get-url');
        var month_index = $obj.attr('month-index');
        var no_data_text_id = $obj.attr('no-data-text-id');
        //リンクを無効化
        $obj.attr('disabled', 'disabled');
        var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
        //ローダー表示
        $obj.after($loader_html);
        //url生成
        var url = get_url + '/page:' + next_page_num;
        if (month_index != undefined && month_index > 1) {
            url = url + '/month_index:' + month_index;
        }
        $.ajax({
            type: 'GET',
            url: url,
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
                    //クリップボードコピーの処理を追加
                    //noinspection JSUnresolvedFunction
                    var client = new ZeroClipboard($posts.find('.copy_me'));
                    //noinspection JSUnusedLocalSymbols
                    client.on("ready", function (readyEvent) {
                        client.on("aftercopy", function (event) {
                            alert(cake.message.v + ": " + event.data["text/plain"]);
                        });
                    });

                    //ページ番号をインクリメント
                    next_page_num++;
                    //次のページ番号をセット
                    $obj.attr('next-page-num', next_page_num);
                    //ローダーを削除
                    $loader_html.remove();
                    //リンクを有効化
                    $obj.text(cake.message.w);
                    $obj.removeAttr('disabled');
                    $("#ShowMoreNoData").hide();
                    //画像をレイジーロード
                    imageLazyOn();
                    //画像リサイズ
                    $posts.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                        $(this).children('.nailthumb-container').nailthumb({
                            width: 50,
                            height: 50,
                            fitDirection: 'center center'
                        });
                    });

                    $('.custom-radio-check').customRadioCheck();

                }

                if (data.count < 20) {
                    if (month_index != undefined) {
                        //ローダーを削除
                        $loader_html.remove();
                        //リンクを有効化
                        $obj.removeAttr('disabled');
                        month_index++;
                        $obj.attr('month-index', month_index);
                        //次のページ番号をセット
                        $obj.attr('next-page-num', 1);
                        $obj.text(cake.message.x);
                        $("#" + no_data_text_id).show();
                    }
                    else {
                        //ローダーを削除
                        $loader_html.remove();
                        $("#" + no_data_text_id).show();
                        //もっと読む表示をやめる
                        $obj.remove();
                    }
                }
            },
            error: function () {
                alert(cake.message.y);
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
                    //画像リサイズ
                    $posts.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                        $(this).children('.nailthumb-container').nailthumb({
                            width: 50,
                            height: 50,
                            fitDirection: 'center center'
                        });
                    });

                    $('.custom-radio-check').customRadioCheck();

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
                    $parent.append(cake.message.z);
                }
            },
            error: function () {
                alert(cake.message.aa);
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
            url = cake.url.d + "/" + model_id;
        }
        else {
            url = cake.message.e + "/" + model_id;
        }

        $.ajax({
            type: 'GET',
            url: url,
            async: true,
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    alert(cake.message.bb);
                }
                else {
                    //「いいね」した場合は「いいね取り消し」表示に
                    //noinspection JSUnresolvedVariable
                    if (data.created == true) {
                        $obj.addClass("liked");
                    }
                    //「いいね取り消し」した場合は「いいね」表示に
                    else {
                        $obj.removeClass("liked");
                    }
                    $("#" + like_count_id).text(data.count);
                }
            },
            error: function () {
                alert(cake.message.bb);
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
                height: '128px',
                showText: '<i class="fa fa-angle-double-down">' + cake.message.cc + '</i>',
                hideText: '<i class="fa fa-angle-double-up">' + cake.message.dd + '</i>'
            });
            $(obj).find('.showmore-comment').showMore({
                speedDown: 300,
                speedUp: 300,
                height: '105px',
                showText: '<i class="fa fa-angle-double-down">' + cake.message.cc + '</i>',
                hideText: '<i class="fa fa-angle-double-up">' + cake.message.dd + '</i>'
            });
        }
        else {
            $('.showmore').showMore({
                speedDown: 300,
                speedUp: 300,
                height: '128px',
                showText: '<i class="fa fa-angle-double-down">' + cake.message.cc + '</i>',
                hideText: '<i class="fa fa-angle-double-up">' + cake.message.dd + '</i>'
            });
            $('.showmore-comment').showMore({
                speedDown: 300,
                speedUp: 300,
                height: '105px',
                showText: '<i class="fa fa-angle-double-down">' + cake.message.cc + '</i>',
                hideText: '<i class="fa fa-angle-double-up">' + cake.message.dd + '</i>'
            });
        }
    }
    function getModalFormFromUrl(e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        $modal_elm.on('shown.bs.modal', function (e) {
            $(this).find('textarea').each(function () {
                $(this).autosize();
            });
            $(this).find('.input-group.date').datepicker({
                format: "yyyy/mm/dd",
                todayBtn: 'linked',
                language: "ja",
                autoclose: true,
                todayHighlight: true
                //endDate:"2015/11/30"
            })
                .on('hide', function (e) {
                    $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[KeyResult][start_date]");
                    $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[KeyResult][end_date]");
                });
        });
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                $modal_elm.find('form').bootstrapValidator({
                    live: 'enabled',
                    feedbackIcons: {},
                    fields: {
                        "data[KeyResult][start_date]": {
                            validators: {
                                callback: {
                                    message: cake.message.ee,
                                    callback: function (value, validator) {
                                        var m = new moment(value, 'YYYY/MM/DD', true);
                                        return m.isBefore($('[name="data[KeyResult][end_date]"]').val());
                                    }
                                }
                            }
                        },
                        "data[KeyResult][end_date]": {
                            validators: {
                                callback: {
                                    message: cake.message.ff,
                                    callback: function (value, validator) {
                                        var m = new moment(value, 'YYYY/MM/DD', true);
                                        return m.isAfter($('[name="data[KeyResult][start_date]"]').val());
                                    }
                                }
                            }
                        }
                    }
                });
                $modal_elm.modal();
                $('body').addClass('modal-open');
            });
        }
    }

    <?if(isset($mode_view)):?>
    <?if($mode_view == MODE_VIEW_TUTORIAL):?>
    $("#modal_tutorial").modal('show');
    <?endif;?>
    <?endif;?>
</script>
<?= $this->Session->flash('click_event') ?>
<?
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
?>
<? if (ENV_NAME == "stg"): ?>
    <p class="ribbon ribbon-staging">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Staging</p>
<? elseif (ENV_NAME == "hotfix"): ?>
    <p class="ribbon ribbon-hotfix">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hotfix</p>
<?
elseif (ENV_NAME == "local"): ?>
    <p class="ribbon ribbon-local">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Local</p>
<? endif; ?>
<!-- END app/View/Elements/gl_common_js.ctp -->
