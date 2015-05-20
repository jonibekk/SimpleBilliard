$.ajaxSetup({
    cache: false
});
$(document).ready(function () {
    //すべてのformで入力があった場合に行う処理
    $("select,input").change(function () {
        $(this).nextAll(".help-block" + ".text-danger").remove();
        if ($(this).is("[name='data[User][agree_tos]']")) {
            //noinspection JSCheckFunctionSignatures
            $(this).parent().parent().nextAll(".help-block" + ".text-danger").remove();
        }
    });
    //ヘッダーサブメニューでのフィード、ゴール切り換え処理
    //noinspection JSJQueryEfficiency
    $('#SubHeaderMenu a').click(function () {
        //既に選択中の場合は何もしない
        if ($(this).hasClass('sp-feed-active')) {
            return;
        }

        if ($(this).attr('id') == 'SubHeaderMenuFeed') {
            $('#SubHeaderMenuGoal').removeClass('sp-feed-active');
            $(this).addClass('sp-feed-active');
            //表示切り換え
            $('[role="goal_area"]').addClass('visible-md visible-lg');
            $('[role="main"]').removeClass('visible-md visible-lg');
        }
        else if ($(this).attr('id') == 'SubHeaderMenuGoal') {
            $('#SubHeaderMenuFeed').removeClass('sp-feed-active');
            $(this).addClass('sp-feed-active');
            //表示切り換え
            $('[role="main"]').addClass('visible-md visible-lg');
            $('[role="goal_area"]').removeClass('visible-md visible-lg');
        }
        else {
            //noinspection UnnecessaryReturnStatementJS
            return;
        }
    });
    //アップロード画像選択時にトリムして表示
    $('.fileinput').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 150, height: 150, fitDirection: 'center center'});
    });
    //アップロード画像選択時にトリムして表示
    $('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 96, height: 96, fitDirection: 'center center'});
    });
    //アップロード画像選択時にトリムして表示
    $('.fileinput_very_small').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 34, height: 34, fitDirection: 'center center'});
    });
    //アップロード画像選択時にトリムして表示
    $('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 50, height: 50, fitDirection: 'center center'});
    });

    $('.fileinput-enabled-submit').fileinput()
        //ファイル選択時にsubmitボタンを有効化する
        .on('change.bs.fileinput', function () {
            attrUndefinedCheck(this, 'submit-id');
            var id = $(this).attr('submit-id');
            $("#" + id).removeAttr('disabled');
        })
        //リセット時にsubmitボタンを無効化する
        .on('clear.bs.fileinput', function () {
            attrUndefinedCheck(this, 'submit-id');
            var id = $(this).attr('submit-id');
            $("#" + id).attr('disabled', 'disabled');
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
    $('textarea:not(.not-autosize)').autosize();
    //noinspection JSJQueryEfficiency
    $('textarea:not(.not-autosize)').show().trigger('autosize.resize');

    //noinspection JSJQueryEfficiency,JSUnresolvedFunction
    imageLazyOn();
    //showmore
    //noinspection JSUnresolvedFunction
    showMore();
    //carousel
    $('.carousel').carousel({interval: false});

    $('.custom-radio-check').customRadioCheck();

    //bootstrap switch
    $(".bt-switch").bootstrapSwitch();
    //bootstrap tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    //form二重送信防止
    $(document).on('submit', 'form', function () {
        $(this).find('input:submit').attr('disabled', 'disabled');
    });
    /**
     * ajaxで取得するコンテンツにバインドする必要のあるイベントは以下記述で追加
     */
    $(document).on("blur", ".blur-height-reset", evThisHeightReset);
    $(document).on("focus", ".click-height-up", evThisHeightUp);
    $(document).on("click", ".tiny-form-text", evShowAndThisWide);
    $(document).on("click", ".tiny-form-text-close", evShowAndThisWideClose);
    $(document).on("click", ".click-show", evShow);
    $(document).on("click", ".trigger-click", evTriggerClick);
    //noinspection SpellCheckingInspection
    $(document).on("keyup", ".blank-disable", evBlankDisable);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-feed-read-more", evFeedMoreView);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-comment-all", evCommentOldView);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-like", evLike);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".target-toggle-click", evTargetToggleClick);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".target-show-this-del", evTargetShowThisDelete);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".target-show-target-del", evTargetShowTargetDelete);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-target-enabled", evTargetEnabled);
    //noinspection JSUnresolvedVariable
    $(document).on("change", ".change-target-enabled", evTargetEnabled);
    //noinspection JSUnresolvedVariable
    $(document).on("change", ".change-select-target-hidden", evSelectOptionTargetHidden);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".check-target-toggle", evToggle);
    $(document).on("click", ".target-toggle", evTargetToggle);
    //noinspection JSUnresolvedVariable,JSUnresolvedFunction
    $(document).on("click", ".click-show-post-modal", getModalPostList);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".toggle-follow", evFollowGoal);
    $(document).on("click", ".click-get-ajax-form-replace", getAjaxFormReplaceElm);
    $(document).on("submit", "form.ajax-csv-upload", uploadCsvFileByForm);
    $(document).on("touchend", "#layer-black", function () {
        $('.navbar-offcanvas').offcanvas('hide');
    });
    //evToggleAjaxGet
    $(document).on("click", ".toggle-ajax-get", evToggleAjaxGet);
    $(document).on("click", ".ajax-get", evAjaxGetElmWithIndex);
    $(document).on("click", ".click-target-remove", evTargetRemove);
    //dynamic modal
    $(document).on("click", '.modal-ajax-get', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        //noinspection CoffeeScriptUnusedLocalSymbols,JSUnusedLocalSymbols
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        $modal_elm.on('hide.bs.modal', function (e) {
            if($(this).data('form-data') != $(this).find('form').serialize()) {
                if(!confirm(cake.message.notice.a)) {
                    e.preventDefault();
                }
            }
        });

        $modal_elm.on('shown.bs.modal', function (e) {
            $(this).data('form-data', $(this).find('form').serialize());
        });


        $modal_elm.modal();
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                //画像をレイジーロード
                imageLazyOn($modal_elm);
                //画像リサイズ
                $modal_elm.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 50,
                        height: 50,
                        fitDirection: 'center center'
                    });
                });

                $modal_elm.find("form").bootstrapValidator();

                $modal_elm.find('.custom-radio-check').customRadioCheck();

            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    });
    $(document).on("click", '.modal-ajax-get-share-circles-users', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        //noinspection JSUnusedLocalSymbols,CoffeeScriptUnusedLocalSymbols
        $modal_elm.on('hide.bs.modal', function (e) {
            if($(this).data('form-data') != $(this).find('form').serialize()) {
                if(!confirm(cake.message.notice.a)) {
                    e.preventDefault();
                }
            }
        });

        $modal_elm.on('shown.bs.modal', function (e) {
            $(this).data('form-data', $(this).find('form').serialize());
        });

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
            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    });

    //noinspection JSUnresolvedVariable
    $(document).on("click", '.modal-ajax-get-collabo', getModalFormFromUrl);
    //noinspection JSUnresolvedVariable
    $(document).on("click", '.modal-ajax-get-add-key-result', getModalFormFromUrl);
    $(document).on("click", '.modal-ajax-get-circle-edit', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        //noinspection JSUnusedLocalSymbols,CoffeeScriptUnusedLocalSymbols
        $modal_elm.on('hide.bs.modal', function (e) {
            if($(this).data('form-data') != $(this).find('form').serialize()) {
                if(!confirm(cake.message.notice.a)) {
                    e.preventDefault();
                }
            }
        });
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        //noinspection JSUnusedLocalSymbols,CoffeeScriptUnusedLocalSymbols
        $modal_elm.on('shown.bs.modal', function (e) {
            $(this).data('form-data', $(this).find('form').serialize());
            $(this).find('textarea').each(function () {
                $(this).autosize();
            });
        });
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                //noinspection JSUnresolvedFunction
                bindSelect2Members($modal_elm);
                //アップロード画像選択時にトリムして表示
                $modal_elm.find('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 96,
                        height: 96,
                        fitDirection: 'center center'
                    });
                });

                $modal_elm.find('#EditCircleForm').bootstrapValidator({
                    excluded: [':disabled'],
                    live: 'enabled',
                    feedbackIcons: {
                        valid: 'fa fa-check',
                        invalid: 'fa fa-times',
                        validating: 'fa fa-refresh'
                    },
                    fields: {
                        "data[Circle][photo]": {
                            feedbackIcons: 'false',
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,gif',
                                    type: 'image/jpeg,image/png,image/gif',
                                    maxSize: 10485760,   // 10mb
                                    message: cake.message.validate.c
                                }
                            }
                        }
                    }
                });
                $modal_elm.modal();
            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    });
    //lazy load
    $(document).on("click", '.target-toggle-click', function (e) {
        e.preventDefault();
        imageLazyOn();
    });


    //noinspection JSJQueryEfficiency
    $('.navbar-offcanvas').on('show.bs.offcanvas', function () {
        $('#layer-black').css('display', 'block');
        $(".toggle-icon").addClass('rotate').removeClass('rotate-reverse').addClass('fa-arrow-right').removeClass('fa-navicon');
    });
    //noinspection JSJQueryEfficiency
    $('.navbar-offcanvas').on('hide.bs.offcanvas', function () {
        $('#layer-black').css('display', 'none');
        $(".toggle-icon").removeClass('rotate').addClass('rotate-reverse').removeClass('fa-arrow-right').addClass('fa-navicon');
    });

});
function imageLazyOn($elm_obj) {
    if ($elm_obj === undefined) {
        $("img.lazy").lazy({
            bind: "event",
            attribute: "data-original",
            combined: true,
            delay: 100,
            visibleOnly: false,
            effect: "fadeIn",
            removeAttribute: false,
            onError: function (element) {
                if (element.attr('error-img') != undefined) {
                    element.attr("src", element.attr('error-img'));
                }
            }
        });
    }
    else {
        $elm_obj.find("img.lazy").lazy({
            bind: "event",
            attribute: "data-original",
            combined: true,
            delay: 100,
            visibleOnly: false,
            effect: "fadeIn",
            removeAttribute: false,
            onError: function (element) {
                if (element.attr('error-img') != undefined) {
                    element.attr("src", element.attr('error-img'));
                }
            }
        });
    }
}
function evTargetRemove() {
    attrUndefinedCheck(this, 'target-selector');
    var $obj = $(this);
    var target_selector = $obj.attr("target-selector");
    $(target_selector).remove();
    return false;
}
function evAjaxGetElmWithIndex(e) {
    e.preventDefault();
    attrUndefinedCheck(this, 'target-selector');
    attrUndefinedCheck(this, 'index');
    var $obj = $(this);
    var target_selector = $obj.attr("target-selector");
    var index = parseInt($obj.attr("index"));

    $.get($obj.attr('href') + "/index:" + index, function (data) {
        $(target_selector).append(data);
        if ($obj.attr('max_index') != undefined && index >= parseInt($obj.attr('max_index'))) {
            $obj.attr('disabled', 'disabled');
            return false;
        }
        //increment
        $obj.attr('index', index + 1);
    });
    return false;
}

function evToggleAjaxGet() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'ajax-url');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var ajax_url = $obj.attr("ajax-url");

    //noinspection JSJQueryEfficiency
    if (!$('#' + target_id).hasClass('data-exists')) {
        $.get(ajax_url, function (data) {
            $('#' + target_id).append(data.html);
        });
    }
    $obj.find('i').each(function () {
        if ($(this).hasClass('fa-caret-down')) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-up');
        }
        else if ($(this).hasClass('fa-caret-up')) {
            $(this).removeClass('fa-caret-up');
            $(this).addClass('fa-caret-down');
        }
    });
    //noinspection JSJQueryEfficiency
    $('#' + target_id).addClass('data-exists');
    //noinspection JSJQueryEfficiency
    $('#' + target_id).toggle();
    return false;
}

function getAjaxFormReplaceElm() {
    attrUndefinedCheck(this, 'replace-elm-parent-id');
    attrUndefinedCheck(this, 'click-target-id');
    attrUndefinedCheck(this, 'tmp-target-height');
    attrUndefinedCheck(this, 'ajax-url');
    var $obj = $(this);
    var replace_elm_parent_id = $obj.attr("replace-elm-parent-id");
    var replace_elm = $('#' + replace_elm_parent_id);
    var click_target_id = $obj.attr("click-target-id");
    var ajax_url = $obj.attr("ajax-url");
    var tmp_target_height = $obj.attr("tmp-target-height");
    replace_elm.children().toggle();
    replace_elm.height(tmp_target_height + "px");
    //noinspection JSJQueryEfficiency
    $.ajax({
        url: ajax_url,
        async: false,
        success: function (data) {
            //noinspection JSUnresolvedVariable
            if (data.error) {
                //noinspection JSUnresolvedVariable
                alert(data.msg);
            }
            else {
                replace_elm.css("height", "");
                replace_elm.append(data.html);
                replace_elm.children("form").bootstrapValidator().on('success.form.bv', function (e) {
                    validatorCallback(e)
                });
                $('#' + click_target_id).trigger('click').focus();
            }
        }
    });
}

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
    })
        .done(function (data) {
            // 通信が成功したときの処理
            $result_msg
                .children('.alert').addClass(data.css)
                .children('.alert-heading').text(data.title);
            //noinspection JSUnresolvedVariable
            $result_msg.find('.alert-msg').text(data.msg);
            $submit.removeAttr('disabled');
        })
        .fail(function (data) {
            // 通信が失敗したときの処理
            $result_msg
                .children('.alert').addClass('alert-danger')
                .children('.alert-heading').text('Connection Error');
            //noinspection JSUnresolvedVariable
            $result_msg.find('.alert-msg').empty();
        })
        .always(function (data) {
            // 通信が完了したとき
            $result_msg.removeClass('none');
            $loader.addClass('none');
        });
}

function addComment(e) {
    e.preventDefault();

    attrUndefinedCheck(e.target, 'error-msg-id');
    var result_msg_id = $(e.target).attr('error-msg-id');
    var $error_msg_box = $('#' + result_msg_id);
    attrUndefinedCheck(e.target, 'submit-id');
    var submit_id = $(e.target).attr('submit-id');
    var $submit = $('#' + submit_id);
    attrUndefinedCheck(e.target, 'first-form-id');
    var first_form_id = $(e.target).attr('first-form-id');
    var $first_form = $('#' + first_form_id);
    attrUndefinedCheck(e.target, 'refresh-link-id');
    var refresh_link_id = $(e.target).attr('refresh-link-id');
    var $refresh_link = $('#' + refresh_link_id);
    var $loader_html = $('<i class="fa fa-refresh fa-spin mr_8px"></i>');

    $error_msg_box.text("");
    appendSocketId($(e.target), cake.pusher.socket_id);

    // Display loading button
    $("#" + submit_id).before($loader_html);

    var $f = $(e.target);
    $.ajax({
        url: $f.prop('action'),
        method: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: new FormData(e.target),
        timeout: 300000 //5min
    })
        .done(function (data) {
            // 通信が成功したときの処理
            if (!data.error) {
                $first_form.children().toggle();
                $f.remove();
                $refresh_link.click();
            }
            else {
                $error_msg_box.text(data.msg);
            }
        })
        .fail(function (data) {
            $error_msg_box.text(cake.message.notice.g);
        })
        .always(function (data) {
            // 通信が完了したとき
            $submit.removeAttr('disabled');
        });
}

function evTargetToggle() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).toggle();
    return false;
}
function evTargetToggleClick() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'click-target-id');

    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    if ($obj.attr("hidden-target-id")) {
        $('#' + $obj.attr("hidden-target-id")).toggle();
    }
    //開いている時と閉じてる時のテキストの指定があった場合は置き換える
    if ($obj.attr("opend-text") != undefined && $obj.attr("closed-text") != undefined) {
        //開いてるとき
        if ($("#" + target_id).is(':visible')) {
            //閉じてる表示
            $obj.text($obj.attr("closed-text"));
        }
        //閉じてるとき
        else {
            //開いてる表示
            $obj.text($obj.attr("opend-text"));
        }
    }
    if (0 == $("#" + target_id).size() && $obj.attr("ajax-url") != undefined) {
        $.ajax({
            url: $obj.attr("ajax-url"),
            async: false,
            success: function (data) {
                //noinspection JSUnresolvedVariable
                if (data.error) {
                    //noinspection JSUnresolvedVariable
                    alert(data.msg);
                }
                else {
                    $("#" + $obj.attr("hidden-target-id")).after(data.html);
                }
            }
        });
    }

    $("form#" + target_id).bootstrapValidator();
    $("#" + target_id).find('.custom-radio-check').customRadioCheck();

    //noinspection JSJQueryEfficiency
    $("#" + target_id).toggle();
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).trigger('click');
    //noinspection JSJQueryEfficiency
    $("#" + click_target_id).focus();
    return false;
}
function evTargetShowThisDelete() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).show();
    $obj.remove();
    return false;
}
function evTargetShowTargetDelete() {
    attrUndefinedCheck(this, 'show-target-id');
    attrUndefinedCheck(this, 'delete-target-id');
    var $obj = $(this);
    var show_target_id = $obj.attr("show-target-id");
    var delete_target_id = $obj.attr("delete-target-id");
    $("#" + show_target_id).show();
    $("#" + delete_target_id).remove();
    return false;
}

function evTargetEnabled() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).removeAttr("disabled");
    return true;
}
function evSelectOptionTargetHidden() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'hidden-option-value');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var hidden_option_value = $obj.attr("hidden-option-value");
    if ($obj.val() == hidden_option_value) {
        $("#" + target_id).hide();
    }
    else {
        $("#" + target_id).show();
    }
    return true;
}

//noinspection FunctionWithInconsistentReturnsJS
function evToggle() {
    attrUndefinedCheck(this, 'target-id');
    var target_id = $(this).attr('target-id');
    if ($(this).attr('disabled')) {
        return;
    }
    $("#" + target_id).toggle();
    return true;
}

function evBlankDisable() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    if ($obj.val().length == 0) {
        $("#" + target_id).attr("disabled", "disabled");
    }
    else {
        $("#" + target_id).removeAttr("disabled");
    }
}

function evTriggerClick() {
    attrUndefinedCheck(this, 'target-id');
    var target_id = $(this).attr("target-id");
    //noinspection JSJQueryEfficiency
    $("#" + target_id).trigger('click');
    //noinspection JSJQueryEfficiency
    $("#" + target_id).focus();
    return false;
}
/**
 * クリックしたら、
 * 指定した要素を表示する。(一度だけ)
 */
function evShow() {
    //クリック済みの場合は処理しない
    if ($(this).hasClass('clicked'))return;

    //autosizeを一旦、切る。
    $(this).trigger('autosize.destroy');
    //再度autosizeを有効化
    $(this).autosize();
    //submitボタンを表示
    $("#" + $(this).attr('target_show_id')).show();
    //クリック済みにする
    $(this).addClass('clicked');
}

/**
 * クリックした要素のheightを倍にし、
 * 指定した要素を表示する。(一度だけ)
 */
function evShowAndThisWide() {
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
    $(this).autosize();

    //submitボタンを表示
    $("#" + $(this).attr('target_show_id')).show();
    //クリック済みにする
    $(this).addClass('clicked');
}
function setSelectOptions(url, select_id) {
    var options_elem = null;
    $.get(url, function (data) {
        $.each(data, function (k, v) {
            var option = '<option value="' + k + '">' + v + '</option>';
            options_elem += option;
        });

        $("#" + select_id).append(options_elem);
    });
}

function evShowAndThisWideClose() {
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

function evThisHeightUp() {
    attrUndefinedCheck(this, 'after-height');
    var after_height = $(this).attr("after-height");
    $(this).height(after_height);
}
function evThisHeightReset() {
    $(this).css('height', "");
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

$(document).on("mouseover", ".develop--forbiddenLink", function () {
    $(this).append($('<div class="develop--forbiddenLink__design">準備中です</div>'));
});
$(document).on("mouseout", ".develop--forbiddenLink", function () {
    $(this).find("div:last").remove();
});

$(function () {
    $(".develop--search").on("click", function () {
            $(this).attr('placeholder', '準備中です。');
        }
    );
});

// Workaround for buggy header/footer fixed position when virtual keyboard is on/off
$(document).on('focus', 'input, textarea', function () {
    $('.navbar').css('position', 'absolute');
});
$(document).on('blur', 'input, textarea', function () {
    $('.navbar').css('position', 'fixed');
    //force page redraw to fix incorrectly positioned fixed elements
    setTimeout(function () {
        //noinspection JSUnresolvedVariable
        if (typeof $.mobile != "undefined") {
            //noinspection JSUnresolvedVariable
            window.scrollTo($.mobile.window.scrollLeft(), $.mobile.window.scrollTop());
        }
    }, 20);
});

// goTop
$(function () {
    var showFlag = false;
    var topBtn = $("#gotop");
    topBtn.css("bottom", "-100px");
    $(window).scroll(function () {
        if ($(this).scrollTop() > 500) {
            if (showFlag == false) {
                showFlag = true;
                topBtn.stop().animate({"bottom": "28px"}, 200);
            }
        } else {
            if (showFlag) {
                showFlag = false;
                topBtn.stop().animate({"bottom": "-100px"}, 200);
            }
        }
    });
    topBtn.click(function () {
        $("body,html").stop().animate({
            scrollTop: 0
        }, 500, 'swing');
        return false;
    });
});

//SubHeaderMenu
$(function () {
    var showNavFlag = false;
    var subNavbar = $("#SubHeaderMenu");
    $(window).scroll(function () {
        if ($(this).scrollTop() > 1) {
            if (showNavFlag == false) {
                showNavFlag = true;
                subNavbar.stop().animate({"top": "-10"}, 800);
            }
        } else {
            if (showNavFlag) {
                showNavFlag = false;
                subNavbar.stop().animate({"top": "50"}, 400);
            }
        }
    });
    $(window).scroll(function () {
        if ($(this).scrollTop() > 10) {
            $(".navbar").css("box-shadow", "0 2px 4px rgba(0, 0, 0, .15)");

        } else {
            $(".navbar").css("box-shadow", "none");

        }
    });
});

$(function () {
    var goT = $("#gotop");
    goT.hover(
        function () {
            $("#gotop-text").stop().animate({'right': '14px'}, 500);
        },
        function () {
            $("#gotop-text").stop().animate({'right': '-140px'}, 500);
        }
    );
});


$(function () {
    $(".hoverPic").hover(
        function () {
            $("img", this).stop().attr("src", $("img", this).attr("src").replace("_off", "_on"));
        },
        function () {
            $("img", this).stop().attr("src", $("img", this).attr("src").replace("_on", "_off"));
        });
});

$(function () {
    $(".header-link").hover(
        function () {
            $(this).stop().css("color", "#ae2f2f").animate({opacity: "1"}, 200);//ONマウス時のカラーと速度
        }, function () {
            $(this).stop().animate({opacity: ".88"}, 400).css("color", "#505050");//OFFマウス時のカラーと速度
        });
});
$(function () {
    $(".header-function-link").hover(
        function () {
            $(".header-function-icon").stop().css("color", "#ae2f2f").animate({opacity: "1"}, 200);//ONマウス時のカラーと速度
        }, function () {
            $(".header-function-icon").stop().animate({opacity: ".88"}, 400).css("color", "#505050");//OFFマウス時のカラーと速度
        });
});

$(function () {
    $(".header-user-profile").hover(
        function () {
            $(".header-profile-icon").stop().css("color", "#ae2f2f").animate({opacity: "1"}, 200);//ONマウス時のカラーと速度
        }, function () {
            $(".header-profile-icon").stop().animate({opacity: ".88"}, 400).css("color", "#505050");//OFFマウス時のカラーと速度
        });
});

$(function () {
    $("#header").hover(
        function () {
            $(".header-link , .header-profile-icon,.header-logo-img ,.header-function-link").stop().animate({opacity: ".88"}, 300);//ONマウス時のカラーと速度
        }, function () {
            $(".header-link , .header-profile-icon,.header-logo-img,.header-function-link").stop().animate({opacity: ".54"}, 600);//OFFマウス時のカラーと速度
        });
});

$(function () {
    $(".click-show").on("click", function () {
            $("#PostFormPicture").css("display", "block")
        }
    )
});

/*表示件数調整*/

$(function () {
    $(".click-circle-trigger").on("click", function () {
        var txt = $(this).text();
        if ($(this).is('.on')) {
            $(this).text(txt.replace(/すべて表示/g, "閉じる")).removeClass("on");
            $(".circleListMore:nth-child(n+10)").css("display", "block");
            $(".circle-toggle-icon").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
        } else {
            $(this).text(txt.replace(/閉じる/g, "すべて表示")).addClass("on");
            $(".circleListMore:nth-child(n+10)").css("display", "none");
            $(".circle-toggle-icon").removeClass("fa-angle-double-up").addClass("fa-angle-double-down");
        }
    });
});

//noinspection JSUnresolvedVariable
$(document).on("click", ".target-show", evTargetShow);

function evTargetShow() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).show();
    return false;
}

//noinspection JSUnresolvedVariable
$(document).on("click", ".target-show-target-click", evTargetShowTargetClick);

function evTargetShowTargetClick() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'click-target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    $("#" + target_id).show();
    $("#" + click_target_id).trigger('click');
    return false;
}

function disabledAllInput(selector) {
    $(selector).find("input,select,textarea").attr('disabled', 'disabled');
}

function enabledAllInput(selector) {
    $(selector).find('input,select,textarea').removeAttr('disabled');
}


$(".ln_trigger-f5").each(function () {
    var $minHeight = 24;
    if ($(this).height() > $minHeight) {
        $(this).addClass('ln_2');
    }
});
$(".ln_trigger-ff").each(function () {
    var $minHeight = 24;
    if ($(this).height() > $minHeight) {
        $(this).addClass('ln_2-f');
    }

});
//noinspection JSUnusedGlobalSymbols
function ajaxAppendCount(id, url) {
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    $('#' + id).append($loader_html);
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            //ローダーを削除
            $loader_html.remove();
            //カウント数を表示
            $('#' + id).text(data.count);
        },
        error: function () {
        }
    });
    return false;
}

$(function () {
    var tutorialNum = 1;
    if (tutorialNum == 1) {
        $("#modalTutorialPrev").hide();
    }
    $("#modalTutorialNext").on("click", function () {
            if (tutorialNum == 1) {
                $("#modalTutorialBox").addClass("tutorial-box2").removeClass("tutorial-box1");
                $("#tutorialText1").hide();
                $("#tutorialText2").show();
                tutorialNum++;
                $("#modalTutorialPrev").show();
            }
            else if (tutorialNum == 2) {
                $("#modalTutorialBox").addClass("tutorial-box3").removeClass("tutorial-box2");
                $("#tutorialText2").hide();
                $("#tutorialText3").show();
                tutorialNum++;
            }
            else if (tutorialNum == 3) {
                $("#modalTutorialBox").addClass("tutorial-box4").removeClass("tutorial-box3");
                $("#tutorialText3").hide();
                $("#tutorialText4").show();
                $(this).hide();
                $("#modalTutorialGo").show();
                tutorialNum++;
            }
        }
    );
    $("#modalTutorialPrev").on("click", function () {
            if (tutorialNum == 2) {
                $("#modalTutorialBox").addClass("tutorial-box1").removeClass("tutorial-box2");
                $("#tutorialText2").hide();
                $("#tutorialText1").show();
                tutorialNum--;
                $("#modalTutorialPrev").hide();
            }
            else if (tutorialNum == 3) {
                $("#modalTutorialBox").addClass("tutorial-box2").removeClass("tutorial-box3");
                $("#tutorialText3").hide();
                $("#tutorialText2").show();
                tutorialNum--;
            }
            else {
                $("#modalTutorialBox").addClass("tutorial-box3").removeClass("tutorial-box4");
                $("#tutorialText4").hide();
                $("#tutorialText3").show();
                $("#modalTutorialNext").show();
                $("#modalTutorialGo").hide();
                tutorialNum--;
            }
        }
    );
    $("#modalTutorialGo").on("click", function () {
            $(this).fadeOut(function () {
                $("#modalTutorialBox").addClass("tutorial-box1").removeClass("tutorial-box4");
                $("#tutorialText4").hide();
                $("#tutorialText1").show();
                $("#modalTutorialNext").show();
                $("#modalTutorialPrev").hide();
            });
            tutorialNum = 1;
        }
    );

});

//入力途中での警告表示
//静的ページのにはすべて適用
function setChangeWarningForAllStaticPage() {
    var flag = false;
    //オートコンプリートでchangeしてしまうのを待つ
    setTimeout(function () {
        $("select,input,textarea").change(function () {
            $(document).on('submit', 'form', function () {
                flag = true;
            });
            $("input[type=submit]").click(function () {
                flag = true;
            });
            if (!$(this).hasClass('disable-change-warning')) {
                $(window).on('beforeunload', function () {
                    if (!flag) {
                        return cake.message.notice.a;
                    }
                    else{
                        return;
                    }
                });
            }
        });
    }, 2000);
}

function warningCloseModal()
{
    warningAction('modal');
}


function warningAction(class_name)
{
    $('.'+class_name).on('shown.bs.modal, loaded.bs.modal', function(e) {
        $(this).data('form-data', $(this).find('form').serialize());
    });

    $('.modal').on('hide.bs.modal', function(e) {
        if($(this).data('form-data') != $(this).find('form').serialize()) {
            if(!confirm(cake.message.notice.a)) {
                $.clearInput();
                e.preventDefault();
            }

        }
    });
}

$.clearInput = function () {
    $('form').find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
    $('form').bootstrapValidator('resetForm', true);
};

//入力途中での警告表示
//Ajaxエレメント中の適用したい要素にchange-warningクラスを指定
function setChangeWarningForAjax() {
    var flag = true;
    $(".change-warning").keyup(function (e) {
        $(document).on('submit', 'form', function () {
            flag = false;
        });
        $("input[type=submit]").click(function () {
            flag = false
        });
        $(window).on('beforeunload', function () {
            if (e.target.value !== "" && flag) {
                return cake.message.notice.a;
            }
        })
    })
}


$(function () {
    $(document).ajaxComplete(setChangeWarningForAjax);
});

$(document).ready(function () {

    setChangeWarningForAllStaticPage();

    warningCloseModal();

    //noinspection JSUnresolvedFunction
    var client = new ZeroClipboard($('.copy_me'));
    //noinspection JSUnusedLocalSymbols
    client.on("ready", function (readyEvent) {
        client.on("aftercopy", function (event) {
            alert(cake.message.info.a + ": " + event.data["text/plain"]);
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
                        message: cake.message.validate.a
                    }
                }
            },
            "data[User][password_confirm]": {
                validators: {
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
        feedbackIcons: {},
        fields: {}
    });

    //noinspection JSUnusedLocalSymbols
    $('#select2Member').select2({
        multiple: true,
        minimumInputLength: 2,
        placeholder: cake.message.notice.b,
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
        placeholder: cake.word.a,
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
        $modal_elm.on('hide.bs.modal', function (e) {
            if($(this).data('form-data') != $(this).find('form').serialize()) {
                if(!confirm(cake.message.notice.a)) {
                    e.preventDefault();
                }
            }
        });

        $modal_elm.on('shown.bs.modal', function (e) {
            $(this).data('form-data', $(this).find('form').serialize());
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
                    onText: cake.word.b,
                    offText: cake.word.c
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
        placeholder: cake.message.notice.b,
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
            return cake.word.d;
        },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            return cake.word.e + n + cake.word.f;
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            return cake.word.g + n + cake.word.h;
        },
        formatSelectionTooBig: function (limit) {
            return cake.word.i + limit + cake.word.j;
        },
        formatLoadMore: function (pageNumber) {
            return cake.message.info.b;
        },
        formatSearching: function () {
            return cake.message.info.c;
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
                        $(this).children('span').text(cake.message.info.d);
                        $(this).children('i').hide();
                        $(this).removeClass('follow-off');
                        $(this).addClass('follow-on');
                    });
                }
                else {
                    $("." + data_class + "[goal-id=" + kr_id + "]").each(function () {
                        $(this).children('span').text(cake.message.info.z);
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
                text: cake.message.notice.c
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

    $modal_elm.on('hide.bs.modal', function (e) {
        if($(this).data('form-data') != $(this).find('form').serialize()) {
            if(!confirm(cake.message.notice.a)) {
                e.preventDefault();
            }
        }
    });

    $modal_elm.on('shown.bs.modal', function (e) {
        $(this).data('form-data', $(this).find('form').serialize());
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
                    alert(cake.message.info.a + ": " + event.data["text/plain"]);
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
            $modal_elm.find('form').bootstrapValidator().on('success.form.bv', function (e) {
                validatorCallback(e)
            });

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
    if (month_index != undefined && month_index > 0) {
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
                        alert(cake.message.info.a + ": " + event.data["text/plain"]);
                    });
                });

                //ページ番号をインクリメント
                next_page_num++;
                //次のページ番号をセット
                $obj.attr('next-page-num', next_page_num);
                //ローダーを削除
                $loader_html.remove();
                //リンクを有効化
                $obj.text(cake.message.info.e);
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
                    $obj.text(cake.message.info.f);
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
            alert(cake.message.notice.c);
        }
    });
    return false;
}

function evCommentOldView() {
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
                //「データが無かった場合はデータ無いよ」を表示
                $parent.append(cake.message.info.g);
            }
        },
        error: function () {
            alert(cake.message.notice.c);
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
        url = cake.url.e + "/" + model_id;
    }

    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert(cake.message.notice.d);
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
            alert(cake.message.notice.d);
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
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
        $(obj).find('.showmore-comment').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '105px',
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
    }
    else {
        $('.showmore').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '128px',
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
        $('.showmore-comment').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '105px',
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
    }
}
function getModalFormFromUrl(e) {
    e.preventDefault();
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    $modal_elm.on('hidden.bs.modal', function (e) {
        $(this).remove();
    });

    $modal_elm.on('hide.bs.modal', function (e) {
        if($(this).data('form-data') != $(this).find('form').serialize()) {
            if(!confirm(cake.message.notice.a)) {
                e.preventDefault();
            }
        }
    });

    $modal_elm.on('shown.bs.modal', function (e) {
        $(this).data('form-data', $(this).find('form').serialize());
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
                                message: cake.message.notice.e,
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
                                message: cake.message.notice.f,
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

$(document).ready(function () {

    var pusher = new Pusher(cake.pusher.key);
    var socketId = "";
    var prevNotifyId = "";
    pusher.connection.bind('connected', function () {
        socketId = pusher.connection.socket_id;
        cake.pusher.socket_id = socketId;
    });
    // フォームがsubmitされた際にsocket_idを埋め込む
    $(document).on('submit', 'form.form-feed-notify', function () {
        appendSocketId($(this), socketId);
    });

    // keyResultの完了送信時にsocket_idを埋め込む
    $(document).on("click", ".kr_achieve_button", function () {
        var formId = $(this).attr("form-id");
        var $form = $("form#" + formId);
        appendSocketId($form, socketId);
        $form.submit();
        return false;
    });

    // page type idをセットする
    setPageTypeId();

    // connectionをはる
    for (var i in cake.data.c) {
        pusher.subscribe(cake.data.c[i]).bind('post_feed', function (data) {
            var isFeedNotify = viaIsSet(data.is_feed_notify);
            var isNewCommentNotify = viaIsSet(data.is_comment_notify);
            var notifyId = data.notify_id;

            // not allowed multple notify
            if (notifyId === prevNotifyId) {
                return;
            }

            // フィード通知の場合
            if (isFeedNotify) {
                var pageTypeId = getPageTypeId();
                var feedTypeId = data.feed_type;
                var canNotify = pageTypeId === feedTypeId || pageTypeId === "all";
                if (canNotify) {
                    prevNotifyId = notifyId;
                    notifyNewFeed();
                }
            }

            // 新しいコメント通知の場合
            if (isNewCommentNotify) {
                var postId = data.post_id;
                var notifyBox = $("#Comments_new_" + String(postId));
                notifyNewComment(notifyBox);
            }
        });
    }

});

function notifyNewFeed() {
    var notifyBox = $(".feed-notify-box");
    var numArea = notifyBox.find(".num");
    var num = parseInt(numArea.html());
    var title = $("title");

    // Increment unread number
    if (num >= 1) {
        // top of feed
        numArea.html(num + 1);
        return;
    }

    // Case of not existing unread post yet
    numArea.html("1");
    notifyBox.css("display", function () {
        return "block";
    });

    // 通知をふんわり出す
    var i = 0.2;
    var roop = setInterval(function () {
        notifyBox.css("opacity", i);
        i = i + 0.2;
        if (i > 1) {
            clearInterval(roop);
        }
    }, 100);
}

function appendSocketId(form, socketId) {
    $('<input>').attr({
        type: 'hidden',
        name: 'socket_id',
        value: socketId
    }).appendTo(form);
}

// notify boxにpage idをセット
function setPageTypeId() {
    var notifyBox = $(".feed-notify-box");
    var pageTypeId = cake.data.d;
    if (pageTypeId === "null") {
        return;
    }
    if (pageTypeId === "circle") {
        pageTypeId += "_" + cake.data.h;
    }
    notifyBox.attr("id", pageTypeId + "_feed_notify");
}

// notify boxのpage idをゲット
function getPageTypeId() {
    var pageTypeId = $(".feed-notify-box").attr("id");
    if (!pageTypeId) return "";
    return pageTypeId.replace("_feed_notify", "");
}

$(document).ready(function () {
    $(document).on("click", ".click-my-goals-read-more", evGoalsMoreView);
    $(document).on("click", ".click-collabo-goals-read-more", evGoalsMoreView);
    $(document).on("click", ".click-follow-goals-read-more", evGoalsMoreView);
});

function evGoalsMoreView() {
    attrUndefinedCheck(this, 'next-page-num');
    attrUndefinedCheck(this, 'get-url');
    attrUndefinedCheck(this, 'goal-type');

    var $obj = $(this);
    var next_page_num = $obj.attr('next-page-num');
    var get_url = $obj.attr('get-url');
    var type = $obj.attr('goal-type');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    //url生成
    var url = get_url + '/page:' + next_page_num + '/type:' + type;
    var listBox;
    var moreViewButton = $obj;
    var limitNumber;
    if (type === "leader") {
        listBox = $("#LeaderGoals");
        limitNumber = cake.data.e;
    } else if (type === "collabo") {
        listBox = $("#CollaboGoals");
        limitNumber = cake.data.f;
    } else if (type === "follow") {
        listBox = $("#FollowGoals");
        limitNumber = cake.data.g;
    }
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $goals = $(data.html);
                //一旦非表示
                $goals.hide();
                listBox.append($goals);
                //html表示
                // もっと見るボタン非表示
                moreViewButton.hide();
                $goals.show();
                //ページ番号をインクリメント
                next_page_num++;
                //次のページ番号をセット
                $obj.attr('next-page-num', next_page_num);
                //ローダーを削除
                $loader_html.remove();
                //もっと見るボタン表示
                moreViewButton.show();
                //リンクを有効化
                $obj.removeAttr('disabled');
                //画像をレイジーロード
                imageLazyOn();
                //画像リサイズ
                $goals.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 50,
                        height: 50,
                        fitDirection: 'center cgenter'
                    });
                });
                if (data.count < limitNumber) {
                    moreViewButton.hide();
                }

                $('.custom-radio-check').customRadioCheck();

            } else {
                // もっと見るボタンの削除
                moreViewButton.hide();
            }

        },
        error: function () {
            alert(cake.message.notice.c);
        }
    });
    return false;
}

function viaIsSet(data) {
    var isExist = typeof( data ) !== 'undefined';
    if (!isExist) return false;
    return data;
}

function notifyNewComment(notifyBox) {
    var numInBox = notifyBox.find(".num");
    var num = parseInt(numInBox.html());

    hideCommentNotifyErrorBox(notifyBox);

    // Increment unread number
    if (num >= 1) {
        // top of feed
        numInBox.html(String(num + 1));
    } else {
        // Case of not existing unread post yet
        numInBox.html("1");
    }

    if (notifyBox.css("display") === "none") {
        notifyBox.css("display", "block");

        // 通知をふんわり出す
        var i = 0.2;
        var roop = setInterval(function () {
            notifyBox.css("opacity", i);
            i = i + 0.2;
            if (i > 1) {
                clearInterval(roop);
            }
        }, 100);
    }
}

function hideCommentNotifyErrorBox(notifyBox) {
    errorBox = notifyBox.siblings(".new-comment-error");
    if (errorBox.attr("display") === "none") {
        return;
    }
    errorBox.css("display", "none");
}

$(document).ready(function () {
    $(document).on("click", ".click-comment-new", evCommentLatestView);
});

function evCommentLatestView() {
    attrUndefinedCheck(this, 'post-id');
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var commentBlock = $obj.closest(".comment-block");
    var commentNum = commentBlock.children("div.comment-box").length;
    var lastCommentBox = commentBlock.children("div.comment-box:last");
    var lastCommentId = "";
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    if (commentNum > 0) {
        // コメントが存在する場合
        attrUndefinedCheck(lastCommentBox, 'comment-id');
        lastCommentId = lastCommentBox.attr("comment-id");
    } else {
        // コメントがまだ0件の場合
        lastCommentId = "";
    }
    var $errorBox = $obj.siblings("div.new-comment-error");
    var get_url = $obj.attr('get-url') + "/" + lastCommentId;
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    //ローダー表示
    $obj.children(".new-comment-read").append($loader_html);
    $.ajax({
        type: 'GET',
        url: get_url,
        async: true,
        dataType: 'json',
        timeout: 10000,
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $posts = $(data.html);
                var postNum = $posts.children("div").length;
                //一旦非表示
                $posts.hide();
                $($obj).before($posts);
                $posts.show("slow", function () {
                    //もっと見る
                    showMore(this);
                });
                //ローダーを削除
                $loader_html.remove();
                //リンクを削除
                $obj.css("display", "none").css("opacity", 0);
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
                $obj.removeAttr("disabled");

                initCommentNotify($obj);
            }
            else {
                //ローダーを削除
                $loader_html.remove();
                //親を取得
                //noinspection JSCheckFunctionSignatures
                $obj.removeAttr("disabled");
                //「もっと読む」リンクを初期化
                initCommentNotify($obj);
                var message = $errorBox.children(".message");
                message.html(cake.message.notice.i);
                $errorBox.css("display", "block");
            }
        },
        error: function (ev) {
            //ローダーを削除
            $loader_html.remove();
            //親を取得
            //noinspection JSCheckFunctionSignatures
            $obj.removeAttr("disabled");
            //「もっと読む」リンクを初期化
            initCommentNotify($obj);
            var message = $errorBox.children(".message");
            message.html(cake.message.notice.i);
            $errorBox.css("display", "block");
        }

    });
    return false;
}

function initCommentNotify(notifyBox) {
    var numInBox = notifyBox.find(".num");
    numInBox.html("0");
    notifyBox.css("display", "none").css("opacity", 0);
}

//bootstrapValidatorがSuccessした時
function validatorCallback(e) {
    switch (e.target.id) {
        case "CommentAjaxGetNewCommentFormForm":
            addComment(e);
            break;
        case "ActionCommentForm":
            addComment(e);
            break;
    }
}

// Be Enable or Disabled eval button
$(function () {
    var eva = [];
    if ($('#evaluation-form')[0]) {

        // Initialize
        $(".eva-val").each(function () {
            setKeyValToEvalList(this);
        });
        switchSubmitBtnEnableOrEnabled();

        // event catch
        $(".eva-val").change(function () {
            setKeyValToEvalList(this);
            switchSubmitBtnEnableOrEnabled();
        });
        $("textarea.eva-val").keyup(function () {
            setKeyValToEvalList(this);
            switchSubmitBtnEnableOrEnabled();
        });
    }

    function switchSubmitBtnEnableOrEnabled() {
        if (isNull(eva)) {
            $('.eval-view-btn-submit').removeAttr('disabled');
        }
        else {
            $('.eval-view-btn-submit').attr('disabled', true);
        }
    }

    function isNull(val) {
        for (var i in val) {
            if (val[i] == '') {
                return false;
            }
        }
        return true;
    }

    function setKeyValToEvalList(selector) {
        eva[selector.id] = selector.value;
    }

});

function evNotifyMoreView() {
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var oldest_score_id = $("ul.notify-list-page").children("li.notify-card-list:last").attr("data-score");
    var get_url = $obj.attr('get-url');
    //リンクを無効化
    $obj.attr('disabled', 'disabled');
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    //ローダー表示
    $obj.after($loader_html);
    //url生成
    var url = get_url + '/' + String(oldest_score_id);
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $notify = $(data.html);
                //一旦非表示
                $notify.hide();
                $(".notify-list-page").append($notify);
                //html表示
                $notify.show("slow", function () {
                    //もっと見る
                    showMore(this);
                });
                //ローダーを削除
                $loader_html.remove();
                $obj.removeAttr('disabled');
                $("#ShowMoreNoData").hide();
                //画像をレイジーロード
                imageLazyOn();
                if (parseInt(data.item_cnt) < cake.new_notify_cnt) {
                    //ローダーを削除
                    $loader_html.remove();
                    //もっと読む表示をやめる
                    $(".feed-read-more").remove();
                }

            } else {
                //ローダーを削除
                $loader_html.remove();
                //もっと読む表示をやめる
                $(".feed-read-more").remove();
            }
        },
        error: function () {
            //ローダーを削除
            $loader_html.remove();
            $obj.removeAttr('disabled');
            $("#ShowMoreNoData").hide();
        }
    });
    return false;
}

$(function () {
    $(document).on("click", ".click-notify-read-more", evNotifyMoreView);
});

// Auto update notify cnt
$(function () {
    if (cake.data.i) {
        setIntervalToGetNotifyCnt(cake.notify_auto_update_sec);
    }

    setNotifyCntToBellAndTitle(cake.new_notify_cnt);

    function setIntervalToGetNotifyCnt(sec) {
        setInterval(function () {
            updateNotifyCnt();
        }, sec * 1000);
    }

    function updateNotifyCnt() {

        var url = cake.url.f;
        $.ajax({
            type: 'GET',
            url: url,
            async: true,
            success: function (new_notify_count) {
                if (new_notify_count != 0) {
                    setNotifyCntToBellAndTitle(new_notify_count);
                }
            },
            error: function () {
            }
        });
        return false;
    }

    function setNotifyCntToBellAndTitle(cnt) {
        var $bellBox = getBellBoxSelector();
        var $title = $("title");
        var $originTitle = $("title").attr("origin-title");
        var existingBellCnt = parseInt($bellBox.html());
        var cntIsTooMuch = "20+";

        if (cnt == 0) {
            return;
        }

        // set notify number
        if (parseInt(cnt) <= 20) {
            $bellBox.html(cnt);
            $title.text("(" + cnt + ")" + $originTitle);
        } else {
            $bellBox.html(cntIsTooMuch);
            $title.text("(" + cntIsTooMuch + ")" + $originTitle);
        }

        if (existingBellCnt == 0) {
            displaySelectorFluffy($bellBox);
        }
        return;
    }

    function displaySelectorFluffy(selector) {
        var i = 0.2;
        var roop = setInterval(function () {
            selector.css("opacity", i);
            i = i + 0.2;
            if (i > 1) {
                clearInterval(roop);
            }
        }, 100);
    }

});

$(document).ready(function () {
    var click_cnt = 0;
    $(document).on("click", "#click-header-bell", function () {
        click_cnt++;
        var isExistNewNotify = isExistNewNotify();
        initBellNum();
        initTitle();

        if (isExistNewNotify || click_cnt == 1) {
            updateListBox();
        }

        function isExistNewNotify() {
            var newNotifyCnt = getNotifyCnt();
            if (newNotifyCnt > 0) {
                return true;
            }
            return false;
        }
    });
});

function initBellNum() {
    $bellBox = getBellBoxSelector();
    $bellBox.css("opacity", 0);
    $bellBox.html("0");
}

function initTitle() {
    $title = $("title");
    $title.text($title.attr("origin-title"));
}

function getBellBoxSelector() {
    return $("#bellNum");
}

function getNotifyCnt() {
    var $bellBox = getBellBoxSelector();
    return parseInt($bellBox.html());
}

function updateListBox() {
    $bellDropdown = $("#bell-dropdown");
    $bellDropdown.empty();
    var $loader_html = $('<li class="text-align_c"><i class="fa fa-refresh fa-spin"></i></li>');
    //ローダー表示
    $bellDropdown.append($loader_html);
    var url = cake.url.g;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            //取得したhtmlをオブジェクト化
            var $notifyItems = data;
            $loader_html.remove();
            $bellDropdown.append($notifyItems);
            //画像をレイジーロード
            imageLazyOn();
        },
        error: function () {
            alert(cake.message.notice.c);
        }
    });
    return false;
}
