$.ajaxSetup({
    cache: false
});
if (typeof String.prototype.startsWith != 'function') {
    // see below for better implementation!
    String.prototype.startsWith = function (str) {
        return this.indexOf(str) === 0;
    };
}
;
function bindPostBalancedGallery($obj) {
    $obj.BalancedGallery({
        autoResize: true,                   // re-partition and resize the images when the window size changes
        //background: '#DDD',                   // the css properties of the gallery's containing element
        idealHeight: 150,                  // ideal row height, only used for horizontal galleries, defaults to half the containing element's height
        //idealWidth: 100,                   // ideal column width, only used for vertical galleries, defaults to 1/4 of the containing element's width
        maintainOrder: false,                // keeps images in their original order, setting to 'false' can create a slightly better balance between rows
        orientation: 'horizontal',          // 'horizontal' galleries are made of rows and scroll vertically; 'vertical' galleries are made of columns and scroll horizontally
        padding: 0,                         // pixels between images
        shuffleUnorderedPartitions: true,   // unordered galleries tend to clump larger images at the begining, this solves that issue at a slight performance cost
        //viewportHeight: 400,               // the assumed height of the gallery, defaults to the containing element's height
        //viewportWidth: 482                // the assumed width of the gallery, defaults to the containing element's width
    });

};
function bindCommentBalancedGallery($obj) {
    $obj.BalancedGallery({
        autoResize: true,                   // re-partition and resize the images when the window size changes
        //background: '#DDD',                   // the css properties of the gallery's containing element
        idealHeight: 100,                  // ideal row height, only used for horizontal galleries, defaults to half the containing element's height
        //idealWidth: 100,                   // ideal column width, only used for vertical galleries, defaults to 1/4 of the containing element's width
        maintainOrder: false,                // keeps images in their original order, setting to 'false' can create a slightly better balance between rows
        orientation: 'horizontal',          // 'horizontal' galleries are made of rows and scroll vertically; 'vertical' galleries are made of columns and scroll horizontally
        padding: 0,                         // pixels between images
        shuffleUnorderedPartitions: true,   // unordered galleries tend to clump larger images at the begining, this solves that issue at a slight performance cost
        //viewportHeight: 400,               // the assumed height of the gallery, defaults to the containing element's height
        //viewportWidth: 482                // the assumed width of the gallery, defaults to the containing element's width
    });
};


$('.post_gallery > img').imagesLoaded(function () {
    bindPostBalancedGallery($('.post_gallery'));
    bindCommentBalancedGallery($('.comment_gallery'));
});
$(document).ready(function () {
    setDefaultTab();
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
    //tab open
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var $target = $(e.target);
        if ($target.hasClass('click-target-focus') && $target.attr('target-id') != undefined) {
            $('#' + $target.attr('target-id')).focus();
        }
    })

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
        var url = "/teams/ajax_switch_team/team_id:" + val;
        $.get(url, function (data) {
            location.href = data;
        });
    });
    //マイページのゴール切替え
    $('#SwitchGoalOnMyPage').change(function () {
        var goal_id = $(this).val();
        if (goal_id == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/goal_id:" + goal_id;
        }
        location.href = url;
    });
    //ゴールページのアクション一覧のKR切替え
    $('#SwitchKrOnMyPage').change(function () {
        var key_result_id = $(this).val();
        if (key_result_id == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/key_result_id:" + key_result_id;
        }
        location.href = url;
    });
    //サークルページの添付ファイルタイプ切替え
    $('#SwitchFileType').change(function () {
        var file_type = $(this).val();
        if (file_type == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/file_type:" + file_type;
        }
        location.href = url;
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
    $(document).on("focus", ".tiny-form-text", evShowAndThisWide);
    $(document).on("keyup", ".tiny-form-text-change", evShowAndThisWide);
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
    $(document).on("click", ".click-this-remove", evRemoveThis);
    //noinspection JSUnresolvedVariable
    $(document).on("change", ".change-next-select-with-value", evChangeTargetSelectWithValue);
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
        modalFormCommonBindEvent($modal_elm);
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                $modal_elm.modal();
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
        modalFormCommonBindEvent($modal_elm);
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                $modal_elm.modal();
            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    });

    //noinspection JSUnresolvedVariable
    $(document).on("click", '.modal-ajax-get-collabo', getModalFormFromUrl);
    //noinspection JSUnresolvedVariable
    $(document).on("click", '.modal-ajax-get-add-key-result', getModalFormFromUrl);
    $(document).on("click", '.modal-ajax-get-add-action', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        $modal_elm.on('shown.bs.modal', function (e) {
            $addActionResultForm = $(this).find('#AddActionResultForm');
            $addActionResultForm.bootstrapValidator({
                excluded: [':hidden'],
                live: 'enabled',
                feedbackIcons: {},
                fields: {
                    "data[ActionResult][photo1]": {
                        validators: {
                            notEmpty: {
                                message: cake.message.validate.g
                            }
                        }
                    }
                }
            });
        });

        modalFormCommonBindEvent($modal_elm);

        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);

                //アップロード画像選択時にトリムして表示
                $modal_elm.find('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
                    $(this).children('.nailthumb-container').nailthumb({
                        width: 50,
                        height: 50,
                        fitDirection: 'center center'
                    });
                });
                $modal_elm.modal();
                $modal_elm.find('#select2ActionCircleMember').select2({
                    multiple: true,
                    placeholder: cake.word.select_notify_range,
                    minimumInputLength: 2,
                    ajax: {
                        url: cake.url.select2_circle_user,
                        dataType: 'json',
                        quietMillis: 100,
                        cache: true,
                        data: function (term, page) {
                            return {
                                term: term, //search term
                                page_limit: 10, // page size
                                circle_type: 'all'
                            };
                        },
                        results: function (data, page) {
                            return {results: data.results};
                        }
                    },
                    data: [],
                    initSelection: cake.data.l,
                    formatSelection: format,
                    formatResult: format,
                    dropdownCssClass: 's2-post-dropdown',
                    escapeMarkup: function (m) {
                        return m;
                    },
                    containerCssClass: "select2Member"
                });


            }).success(function () {
                $('body').addClass('modal-open');
            });
        }
    });
    $('.ModalActionResult_input_field').on('change', function () {
        $('#AddActionResultForm').bootstrapValidator('revalidateField', 'photo');
    });

    $(document).on("click", '.modal-ajax-get-circle-edit', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        //noinspection JSUnusedLocalSymbols,CoffeeScriptUnusedLocalSymbols
        modalFormCommonBindEvent($modal_elm);
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

                $editCircleForm = $modal_elm.find('#EditCircleForm');
                $editCircleForm.bootstrapValidator({
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
                // submit ボタンが form 外にあるので、自力で制御する
                $editCircleForm
                    .on('error.field.bv', function (e) {
                        $('#EditCircleFormSubmit').attr('disabled', 'disabled');
                    })
                    .on('success.field.bv', function (e) {
                        $('#EditCircleFormSubmit').removeAttr('disabled');
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

    //team term setting
    $(document).on("change", '#TeamStartTermMonth , #TeamBorderMonths', function () {
        var startTermMonth = $('#TeamStartTermMonth').val();
        var borderMonths = $('#TeamBorderMonths').val();
        if (startTermMonth === "" || borderMonths === "") {
            $('#CurrentTermStr').empty();
            return false;
        }
        var url = cake.url.h + "/" + startTermMonth + "/" + borderMonths;
        $.get(url, function (data) {
            $('#CurrentTermStr').text(data.start + "  -  " + data.end);
        });
    });

    //edit team term setting
    $(document).on("change", '#EditTermChangeFrom1 , #EditTermChangeFrom2 , #EditTermStartTerm , #EditTermBorderMonths', function () {

        if ($("#EditTermChangeFrom1:checked").val()) {
            var changeFrom = $('#EditTermChangeFrom1:checked').val();
        }
        else {
            var changeFrom = $('#EditTermChangeFrom2:checked').val();
        }
        var startTermMonth = $('#EditTermStartTerm').val();
        var borderMonths = $('#EditTermBorderMonths').val();
        if (startTermMonth === "" || borderMonths === "") {
            $('#NewCurrentTerm').addClass('none');
            $('#NewCurrentTerm > div > p').empty();
            $('#NewNextTerm').addClass('none');
            $('#NewNextTerm > div > p').empty();
            return false;
        }
        var url = cake.url.r + "/" + startTermMonth + "/" + borderMonths + "/" + changeFrom;
        $.get(url, function (data) {
            if (data.current.start_date && data.current.end_date) {
                $('#NewCurrentTerm').removeClass('none');
                $('#NewCurrentTerm > div > p').text(data.current.start_date + "  -  " + data.current.end_date);
            }
            else {
                $('#NewCurrentTerm').addClass('none');
                $('#NewCurrentTerm > div > p').empty();
            }
            if (data.next.start_date && data.next.end_date) {
                $('#NewNextTerm').removeClass('none');
                $('#NewNextTerm > div > p').text(data.next.start_date + "  -  " + data.next.end_date);
            }
            else {
                $('#NewNextTerm').addClass('none');
                $('#NewNextTerm > div > p').empty();
            }
        });
    });

    //
    $(document).on("submit", "form.ajax-edit-circle-admin-status", evAjaxEditCircleAdminStatus);
    $(document).on("submit", "form.ajax-leave-circle", evAjaxLeaveCircle);
    $(document).on("click", ".click-goal-follower-more", evAjaxGoalFollowerMore);
    $(document).on("click", ".click-goal-member-more", evAjaxGoalMemberMore);
    $(document).on("click", ".click-goal-key-result-more", evAjaxGoalKeyResultMore);


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

    // サークル編集画面のタブ切り替え
    // タブによって footer 部分を切り替える
    $(document).on('shown.bs.tab', '.modal-dialog.edit-circle a[data-toggle="tab"]', function (e) {
        var $target = $(e.target);
        var tabId = $target.attr('href').replace('#', '');
        $target.closest('.modal-dialog').find('.modal-footer').hide().filter('.' + tabId + '-footer').show();
    })

    if (cake.data.j == "0") {
        $('#FeedMoreReadLink').trigger('click');
    }


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
    var post_id = $obj.attr("post-id");
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

                // コメントフォームをドラッグ＆ドロップ対象エリアにする
                var commentParams = {
                    formID: function () {
                        return $(this).attr('data-form-id');
                    },
                    previewContainerID: function () {
                        return $(this).attr('data-preview-container-id');
                    }
                };
                var $uploadFileForm = $(document).data('uploadFileForm');
                $uploadFileForm.registerDragDropArea('#CommentBlock_' + post_id, commentParams);
                $uploadFileForm.registerAttachFileButton('#CommentUploadFileButton_' + post_id, commentParams);
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

function evRemoveThis() {
    $(this).remove();
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

/**
 * 以下の処理を行う
 * 1. this 要素を remove() する
 * 2. this 要素に target-id 属性が設定されている場合
 *    その値をカンマ区切りの要素IDリストとみなし、各IDに $(#target_id).show() を行う
 *
 * オプション属性
 *   target-id: 表示する要素IDのリスト（カンマ区切り）
 *   delete-method: 'hide' を指定すると、this 要素に対して remove() でなく hide() を行う
 *
 * 例:
 * <a href="#" onclick="evTargetShowThisDelete()" target-id="box1,box2">ボタン</a>
 * <div id="box1" style="display:none">ボタンが押されたら表示される</div>
 * <div id="box2" style="display:none">ボタンが押されたら表示される</div>
 *
 * @returns {boolean}
 */
function evTargetShowThisDelete() {
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var deleteMethod = $obj.attr("delete-method");
    var targets = target_id.split(',');
    if (deleteMethod == 'hide') {
        $obj.hide();
    }
    else {
        $obj.remove();
    }
    $.each(targets, function () {
        $("#" + this).show();
    });
    return false;
}
function evTargetShowTargetDelete() {
    attrUndefinedCheck(this, 'show-target-id');
    attrUndefinedCheck(this, 'delete-target-id');
    var $obj = $(this);
    var show_target_id = $obj.attr("show-target-id");
    var delete_target_id = $obj.attr("delete-target-id");
    $("#" + show_target_id).removeClass('none');
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
    if ($(this).attr("after-replace-target-id") != undefined) {
        $(this).attr("target-id", $(this).attr("after-replace-target-id"));
        $(this).removeAttr("after-replace-target-id");
    }
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
function setSelectOptions(url, select_id, target_toggle_id, selected) {
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

function evChangeTargetSelectWithValue() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'ajax-url');
    var target_id = $(this).attr("target-id");
    var url = $(this).attr("ajax-url") + $(this).val();
    var target_toggle_id = $(this).attr("toggle-target-id") != undefined ? $(this).attr("toggle-target-id") : null;
    var selected = $(this).attr('target-value');
    setSelectOptions(url, target_id, target_toggle_id, selected);
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
        if ($(this).scrollTop() > 30) {
            if (showFlag == false) {
                showFlag = true;
                topBtn.stop().animate({"bottom": "40px"}, 200);
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
                subNavbar.stop().animate({"top": "-60"}, 800);
            }
        } else {
            if (showNavFlag) {
                showNavFlag = false;
                subNavbar.stop().animate({"top": "0"}, 400);
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
            $("#gotop-text").stop().animate({'right': '14px'}, 360);
        },
        function () {
            $("#gotop-text").stop().animate({'right': '-140px'}, 800);
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

/*表示件数調整 -mobilesize*/

$(function () {
    $(".click-circle-trigger").on("click", function () {
        var txt = $(this).text();
        if ($(this).is('.on')) {
            $(this).text(txt.replace(/すべて表示/g, "閉じる")).removeClass("on");
            $(".circleListMore:nth-child(n+9)").css("display", "block");
            $(".circle-toggle-icon").removeClass("fa-angle-double-down").addClass("fa-angle-double-up");
        } else {
            $(this).text(txt.replace(/閉じる/g, "すべて表示")).addClass("on");
            $(".circleListMore:nth-child(n+9)").css("display", "none");
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

function disabledAllInput(selector) {
    $(selector).find("input,select,textarea").attr('disabled', 'disabled');
}

function enabledAllInput(selector) {
    $(selector).find('input,select,textarea').removeAttr('disabled');
}

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
                    else {
                        return;
                    }
                });
            }
        });
    }, 2000);
}

function warningCloseModal() {
    warningAction('modal');
}

function warningAction(class_name) {
    $('.' + class_name).on('shown.bs.modal', function (e) {
        $(this).data('form-data', $(this).find('form').serialize());
    });

    $('.' + class_name).on('hide.bs.modal', function (e) {
        if ($(this).data('form-data') != $(this).find('form').serialize()) {
            if (!confirm(cake.message.notice.a)) {
                e.preventDefault();
            } else {
                $.clearInput($(this));
            }

        }
    });
}

function modalFormCommonBindEvent($modal_elm) {
    modalWarningShownBind($modal_elm);
    modalWarningHideBind($modal_elm);
    $modal_elm.on('shown.bs.modal', function (e) {
        $(this).find('textarea').each(function () {
            $(this).autosize();
        });
    });
}
function modalWarningHideBind($modal_elm) {
    $modal_elm.on('hide.bs.modal', function (e) {
        if ($(this).data('form-data') != $(this).find('form').serialize()) {
            if (!confirm(cake.message.notice.a)) {
                e.preventDefault();
            }
        }
    });
}

function modalWarningShownBind($modal_elm) {
    $modal_elm.on('shown.bs.modal', function (e) {
        $(this).data('form-data', $(this).find('form').serialize());
    });
}


$.clearInput = function ($obj) {
    $obj.find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
    $obj.bootstrapValidator('resetForm', true);
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
                    },
                    regexp: {
                        regexp: /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{0,}$/,
                        message: cake.message.validate.e
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
    $('#CommonActionDisplayForm').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {},
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
        placeholder: cake.word.select_public_circle,
        minimumInputLength: 2,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: "public"
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.b,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2PostCircleMember"
    });

    // select2 秘密サークル選択
    $('#select2PostSecretCircle').select2({
        multiple: true,
        placeholder: cake.word.select_secret_circle,
        minimumInputLength: 2,
        maximumSelectionSize: 1,
        ajax: {
            url: cake.url.select2_secret_circle,
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
        data: [],
        initSelection: cake.data.select2_secret_circle,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2PostCircleMember"
    });

    //noinspection JSUnusedLocalSymbols,JSDuplicatedDeclaration
    $('#select2MessageCircleMember').select2({
        multiple: true,
        placeholder: cake.word.select_public_message,
        minimumInputLength: 2,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: "public"
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.b,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2MessageCircleMember"
    });

    // select2 秘密サークル選択
    $('#select2MessageSecretCircle').select2({
        multiple: true,
        placeholder: cake.word.select_secret_circle,
        minimumInputLength: 2,
        maximumSelectionSize: 1,
        ajax: {
            url: cake.url.select2_secret_circle,
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
        data: [],
        initSelection: cake.data.select2_secret_circle,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2MessageCircleMember"
    });

    // 投稿の共有範囲(公開/秘密)切り替えボタン
    var $shareRangeToggleButton = $('#postShareRangeToggleButton');
    var $shareRange = $('#postShareRange');
    var publicButtonLabel = '<i class="fa fa-unlock"></i> ' + cake.word.public;
    var secretButtonLabel = '<i class="fa fa-lock font_verydark"></i> ' + cake.word.secret;

    // ボタン初期状態
    $shareRangeToggleButton.html(($shareRange.val() == 'public') ? publicButtonLabel : secretButtonLabel);

    // 共有範囲切り替えボタンが有効な場合
    if ($shareRangeToggleButton.attr('data-toggle-enabled')) {
        $shareRangeToggleButton.on('click', function (e) {
            e.preventDefault();
            $shareRange.val($shareRange.val() == 'public' ? 'secret' : 'public');
            if ($shareRange.val() == 'public') {
                $shareRangeToggleButton.html(publicButtonLabel);
                $('#PostSecretShareInputWrap').hide();
                $('#PostPublicShareInputWrap').show();
            }
            else {
                $shareRangeToggleButton.html(secretButtonLabel);
                $('#PostPublicShareInputWrap').hide();
                $('#PostSecretShareInputWrap').show();
            }
        });
    }
    // 共有範囲切り替えボタンが無効な場合（サークルフィードページ）
    else {
        $shareRangeToggleButton.popover({
            'data-toggle': "popover",
            'placement': 'top',
            'trigger': "focus",
            'content': cake.word.share_change_disabled,
            'container': 'body'
        });
    }


    $('#select2ActionCircleMember').select2({
        multiple: true,
        placeholder: cake.word.select_notify_range,
        minimumInputLength: 2,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: 'all'
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.l,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown aaaa',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2ActionCircleMember"
    });
    $(document).on("click", '.modal-ajax-get-public-circles', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        modalFormCommonBindEvent($modal_elm);
        var url = $(this).attr('href');
        if (url.indexOf('#') == 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $modal_elm.append(data);
                $modal_elm.modal();
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

    $('#PostDisplayForm, #CommonActionDisplayForm, #MessageDisplayForm').change(function (e) {
        var $target = $(e.target);
        switch ($target.attr('id')) {
            case "CommonPostBody":
                $('#CommonActionName').val($target.val()).autosize().trigger('autosize.resize');
                $('#CommonMessageBody').val($target.val()).autosize().trigger('autosize.resize');
                break;
            case "CommonActionName":
                $('#CommonPostBody').val($target.val()).autosize().trigger('autosize.resize');
                $('#CommonMessageBody').val($target.val()).autosize().trigger('autosize.resize');
                break;
            case "CommonMessageBody":
                $('#CommonPostBody').val($target.val()).autosize().trigger('autosize.resize');
                $('#CommonActionName').val($target.val()).autosize().trigger('autosize.resize');
                break;
        }
    });
});

function format(item) {
    if ('image' in item) {
        return "<img style='width:14px;height: 14px' class='select2-item-img' src='" + item.image + "' alt='icon' /> " + "<span class='select2-item-txt'>" + item.text + "</span>";
    }
    else if ('icon' in item) {
        return "<span class='select2-item-txt-with-i'><i class='" + item.icon + "'></i> " + item.text + "</span>";
    }
    else {
        return "<span class='select2-item-txt'>" + item.text + "</span>";
    }
}
function bindSelect2Members($this) {
    var $select2elem = $this.find(".ajax_add_select2_members");
    var url = $select2elem.attr('data-url');

    //noinspection JSUnusedLocalSymbols
    $select2elem.select2({
        'val': null,
        multiple: true,
        minimumInputLength: 2,
        placeholder: cake.message.notice.b,
        ajax: {
            url: url ? url : cake.url.a,
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
    var goal_id = $obj.attr('goal-id');
    var data_class = $obj.attr('data-class');
    var url = cake.url.c;
    $.ajax({
        type: 'GET',
        url: url + goal_id,
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
                    $("." + data_class + "[goal-id=" + goal_id + "]").each(function () {
                        $(this).children('span').text(cake.message.info.d);
                        $(this).children('i').hide();
                        $(this).removeClass('follow-off');
                        $(this).addClass('follow-on');
                    });
                }
                else {
                    $("." + data_class + "[goal-id=" + goal_id + "]").each(function () {
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
    modalFormCommonBindEvent($modal_elm);

    var url = $(this).attr('href');
    if (url.indexOf('#') == 0) {
        $(url).modal('open');
    } else {
        $.get(url, function (data) {
            $modal_elm.modal();
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
            imageLazyOn($modal_elm);
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
autoload_more = false;
function evFeedMoreView(options) {
    var opt = $.extend({
        recursive: false,
        loader_id: null
    }, options);

    attrUndefinedCheck(this, 'parent-id');
    attrUndefinedCheck(this, 'next-page-num');
    attrUndefinedCheck(this, 'get-url');

    var $obj = $(this);
    var parent_id = $obj.attr('parent-id');
    var next_page_num = $obj.attr('next-page-num');
    var get_url = $obj.attr('get-url');
    var month_index = $obj.attr('month-index');
    var no_data_text_id = $obj.attr('no-data-text-id');
    var oldest_post_time = $obj.attr('oldest-post-time') || 0;
    var append_target_id = $obj.attr('append-target-id');
    // この時間より前の投稿のみ読み込む
    var post_time_before = $obj.attr('post-time-before') || 0;

    //リンクを無効化
    $obj.attr('disabled', 'disabled');

    //ローダー表示
    var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<i id="__feed_loader" class="fa fa-refresh fa-spin"></i>');
    if (!opt.recursive) {
        $obj.after($loader_html);
    }

    // URL生成
    // 投稿の更新時間が指定されていれば、それ以前の投稿のみを取得する
    var url = get_url + '/page:' + next_page_num;
    if (post_time_before) {
        url += '/post_time_before:' + post_time_before;
    }
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
                if (append_target_id != undefined) {
                    $("#" + append_target_id).append($posts);
                }
                else {
                    $("#" + parent_id).before($posts);
                }
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
                //$posts.children('.post_gallery > img').imagesLoaded(function () {
                $posts.imagesLoaded(function () {
                    $posts.children('.post_gallery').each(function (index, element) {
                        bindPostBalancedGallery($(element));
                    });
                });
                //$posts.children('.comment_gallery > img').imagesLoaded(function () {
                //$posts.children('.post_gallery').each(function (index, element) {
                //    bindCommentBalancedGallery($(element));
                //});
                //});

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

            // 取得したデータ件数が、１ページの表示件数未満だった場合
            if (data.count < data.page_item_num) {
                // 前月以前のデータを取得する必要がある場合
                if (month_index != undefined) {
                    month_index++;
                    $obj.attr('month-index', month_index);
                    //次のページ番号をセット
                    $obj.attr('next-page-num', 1);

                    // 取得した件数が 1 件以上の場合
                    // 「さらに読み込む」リンクを表示
                    if (data.count > 0) {
                        $obj.removeAttr('disabled');
                        $loader_html.remove();
                        $obj.text(cake.message.info.f);
                    }
                    // 取得したデータ件数が 0 件の場合
                    else {
                        // さらに古い投稿が存在する可能性がある場合は、再度同じ関数を呼び出す
                        if (data.start && data.start > oldest_post_time) {
                            setTimeout(function () {
                                evFeedMoreView.call($obj[0], {recursive: true, loader_id: '__feed_loader'});
                            }, 200);
                            return;
                        }
                        // これ以上古い投稿が存在しない場合
                        else {
                            $loader_html.remove();
                            $("#" + no_data_text_id).show();
                            $obj.remove();
                            return;
                        }
                    }
                }
                // 前月以前のデータを取得する必要がない場合
                else {
                    //ローダーを削除
                    $loader_html.remove();
                    $("#" + no_data_text_id).show();
                    //もっと読む表示をやめる
                    $obj.remove();
                }
            }
            autoload_more = false;
        },
        error: function () {
            alert(cake.message.notice.c);
        }
    });
    return false;
}

// ゴールのフォロワー一覧を取得
function evAjaxGoalFollowerMore() {
    var $obj = $(this);
    $obj.attr('ajax-url', cake.url.goal_followers + '/goal_id:' + $obj.attr('goal-id'));
    return evBasicReadMore.call(this);
}

// ゴールのメンバー一覧を取得
function evAjaxGoalMemberMore() {
    var $obj = $(this);
    $obj.attr('ajax-url', cake.url.goal_members + '/goal_id:' + $obj.attr('goal-id'));
    return evBasicReadMore.call(this);
}

// ゴールのキーリザルト一覧を取得
function evAjaxGoalKeyResultMore() {
    var $obj = $(this);
    var kr_can_edit = $obj.attr('kr-can-edit');
    var goal_id = $obj.attr('goal-id');
    $obj.attr('ajax-url', cake.url.goal_key_results + '/' + kr_can_edit + '/goal_id:' + goal_id + '/view:key_results');
    return evBasicReadMore.call(this);
}

/**
 * オートローダー シンプル版
 *
 * オプション
 *   ajax_url: Ajax呼び出しURL
 *   next-page-num: 次に読み込むページ数
 *   list-container: Ajaxで読み込んだHTMLを挿入するコンテナのセレクタ
 *
 * ajax_url のレスポンスJSON形式
 *   {
 *     html: string,         // 一覧(list-container)の末尾に挿入されるHTML
 *     page_item_num: int,   // １ページ（１度の読み込み）で表示するアイテムの数
 *     count: int,           // 実際に返されたアイテムの数
 *   }
 *
 * 使用例
 *   HTML:
 *     <a href="#"
 *        id="SampleReadMoreButtonID"
 *        ajax-url="{Ajax呼び出しURL}"
 *        next-page-num="2"
 *        list-container="#listContainerID">さらに読み込む</a>
 *
 *   JavaScript:
 *     $(document).on("click", "#SampleReadMoreButtonID", evAjaxSampleReadMore);
 *     function evAjaxSampleReadMore() {
 *         return evBasicReadMore.call(this);
 *     }
 *
 * @returns {boolean}
 */


function evBasicReadMore() {
    var $obj = $(this);
    var ajax_url = $obj.attr('ajax-url');
    var next_page_num = $obj.attr('next-page-num');
    var $list_container = $($obj.attr('list-container'));

    // 次ページのURL
    ajax_url += '/page:' + next_page_num;

    // さらに読み込むリンク無効化
    $obj.attr('disabled', 'disabled');

    // ローダー表示
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    $obj.after($loader_html);

    $.ajax({
        type: 'GET',
        url: ajax_url,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (!$.isEmptyObject(data.html)) {
                var $content = $(data.html);
                $content.hide();
                $list_container.append($content);
                $content.show("slow");

                // ページ番号インクリメント
                next_page_num++;
                $obj.attr('next-page-num', next_page_num);

                // ローダーを削除
                $loader_html.remove();

                // リンクを有効化
                $obj.removeAttr('disabled');
            }

            // 取得したデータ件数が、１ページの表示件数未満だった場合
            if (data.count < data.page_item_num) {
                // ローダーを削除
                $loader_html.remove();

                // 「さらに読みこむ」表示をやめる
                $obj.remove();
            }
            autoload_more = false;
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
        url = cake.url.d + model_id;
    }
    else {
        url = cake.url.e + model_id;
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
        $(obj).find('.showmore-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '900px',
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
        $(obj).find('.showmore-comment-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '920px',
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
        $('.showmore-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '900px',
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
        $('.showmore-comment-circle').showMore({
            speedDown: 300,
            speedUp: 300,
            height: '920px',
            showText: '<i class="fa fa-angle-double-down"></i>' + cake.message.info.e,
            hideText: '<i class="fa fa-angle-double-up"></i>' + cake.message.info.h
        });
    }
}
function getModalFormFromUrl(e) {
    e.preventDefault();
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    modalFormCommonBindEvent($modal_elm);

    $modal_elm.on('shown.bs.modal', function (e) {
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
    else if (type === "my_prev") {
        listBox = $("#PrevGoals");
        limitNumber = cake.data.k;
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
                //一旦非表示
                $posts.fadeOut();
                $($obj).before($posts);
                $posts.fadeIn();
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
    if (e.target.id.startsWith('CommentAjaxGetNewCommentForm_')) {
        addComment(e);
    }
    else if (e.target.id == "ActionCommentForm") {
        addComment(e);
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
    var oldest_score_id = $("ul.notify-page-cards").children("li.notify-card-list:last").attr("data-score");
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
            autoload_more = false;
            if (!$.isEmptyObject(data.html)) {
                //取得したhtmlをオブジェクト化
                var $notify = $(data.html);
                //一旦非表示
                $notify.hide();
                $(".notify-page-cards").append($notify);
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
            updateMessageNotifyCnt();
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

    function updateMessageNotifyCnt() {

        var url = cake.url.af;
        $.ajax({
            type: 'GET',
            url: url,
            async: true,
            success: function (new_notify_count) {
                if (new_notify_count != 0) {
                    setNotifyCntToMessageAndTitle(new_notify_count);
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
        var existingBellCnt = parseInt($bellBox.children('span').html());
        var cntIsTooMuch = '20+';

        if (cnt == 0) {
            return;
        }

        // set notify number
        if (parseInt(cnt) <= 20) {
            $bellBox.children('span').html(cnt);
            $bellBox.children('sup').addClass('none');
            $title.text("(" + cnt + ")" + $originTitle);
        } else {
            $bellBox.children('span').html(20);
            $bellBox.children('sup').removeClass('none');
            $title.text("(" + cntIsTooMuch + ")" + $originTitle);
        }

        if (existingBellCnt == 0) {
            displaySelectorFluffy($bellBox);
        }
        return;
    }

    function setNotifyCntToMessageAndTitle(cnt) {
        var $bellBox = getMessageBoxSelector();
        var $title = $("title");
        var $originTitle = $("title").attr("origin-title");
        var existingBellCnt = parseInt($bellBox.children('span').html());
        var cntIsTooMuch = '20+';

        if (cnt == 0) {
            return;
        }

        // set notify number
        if (parseInt(cnt) <= 20) {
            $bellBox.children('span').html(cnt);
            $bellBox.children('sup').addClass('none');

            $title.text("(" + cnt + ")" + $originTitle);
        } else {
            $bellBox.children('span').html(20);
            $bellBox.children('sup').removeClass('none');
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

$(document).ready(function () {
    var click_cnt = 0;
    $(document).on("click", "#click-header-message", function () {
        initMessageNum();
        initTitle();
        updateMessageListBox();
    });
});

function initBellNum() {
    $bellBox = getBellBoxSelector();
    $bellBox.css("opacity", 0);
    $bellBox.html("0");
}
function initMessageNum() {
    var $box = getMessageBoxSelector();
    $box.css("opacity", 0);
    //$box.html("0");
    $box.children('span').html("0");
}

function initTitle() {
    $title = $("title");
    $title.text($title.attr("origin-title"));
}

function getBellBoxSelector() {
    return $("#bellNum");
}
function getMessageBoxSelector() {
    return $("#messageNum");
}

function getNotifyCnt() {
    var $bellBox = getBellBoxSelector();
    return parseInt($bellBox.children('span').html());
}

function getMessageNotifyCnt() {
    var $box = getMessageBoxSelector();
    return parseInt($box.children('span').html());
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

function updateMessageListBox() {
    var $messageDropdown = $("#message-dropdown");
    $messageDropdown.empty();
    var $loader_html = $('<li class="text-align_c"><i class="fa fa-refresh fa-spin"></i></li>');
    //ローダー表示
    $messageDropdown.append($loader_html);
    var url = cake.url.ag;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            //取得したhtmlをオブジェクト化
            var $notifyItems = data;
            $loader_html.remove();
            $messageDropdown.append($notifyItems);
            //画像をレイジーロード
            imageLazyOn();
        },
        error: function () {
            alert(cake.message.notice.c);
        }
    });
    return false;
}

function copyToClipboard(url) {
    window.prompt("Copy to clipboard: Ctrl+C, Enter", url);
}

$(document).ready(function () {
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 2000) {
            if (!autoload_more) {
                autoload_more = true;
                $('#FeedMoreReadLink').trigger('click');
                $('#GoalPageFollowerMoreLink').trigger('click');
                $('#GoalPageMemberMoreLink').trigger('click');
                $('#GoalPageKeyResultMoreLink').trigger('click');
            }
        }
    });


    /**
     * ファイルのドラッグ & ドロップ 設定
     *
     * 設定例）
     * HTML:
     *   <div id="DragDropArea">
     *      <form id="PostForm">
     *         <div id="PreviewArea></div>
     *         <!-- form の最後に data['file_id'][] の名前で hidden が追加される -->
     *      </form>
     *      <a href="#" id="UploadButton">ファイルを添付</a>
     *   </div>
     *   <?= $this->element('file_upload_form') ?>
     *
     * JavaScript:
     *   var postParams = {
     *     formID: 'PostForm',
     *     previewContainerID: 'PreviewArea'
     *   };
     *   var $uploadFileForm = $(document).data('uploadFileForm');
     *   $uploadFileForm.registerDragDropArea('#DragDropArea', postParams);
     *   $uploadFileForm.registerAttachFileButton('#UploadButton', postParams);
     *
     */
    // ファイルアップロード用フォーム
    var $uploadFileForm = $('#UploadFileForm');
    // ファイル削除用フォーム
    var $removeFileForm = $('#RemoveFileForm');
    // 手動ファイル添付用ボタン
    var $uploadFileAttachButton = $('#UploadFileAttachButton');
    // プレビューエリアのテンプレート
    var previewTemplateDefault =
        '<div class="dz-preview dz-default-preview panel">' +
        '  <div class="dz-details">' +
        '    <a href="#" class="pull-right font_lightgray" data-dz-remove><i class="fa fa-times"></i></a>' +
        '    <div class="dz-thumb-container pull-left">' +
        '      <i class="fa fa-file-o file-other-icon"></i>' +
        '      <img class="dz-thumb none" data-dz-thumbnail /></div>' +
        '    <span class="dz-name font_14px font_bold font_verydark pull-left" data-dz-name></span><br>' +
        '    <span class="dz-size font_11px font_lightgray pull-left" data-dz-size></span>' +
        '  </div>' +
        '  <div class="dz-progress progress">' +
        '    <div class="progress-bar progress-bar-info" role="progressbar"  data-dz-uploadprogress></div>' +
        '  </div>' +
        '</div>';

    // アクションのメイン画像表示部分のテンプレート
    var previewTemplateActionImage =
        '<div class="dz-preview dz-action-photo-preview action-photo-preview upload-file-attach-button">' +
        '  <div class="dz-action-photo-details">' +
        '    <div class="dz-action-photo-thumb-container pull-left"><img class="dz-action-photo-thumb" data-dz-thumbnail /></div>' +
        '  </div>' +
        '  <div class="dz-action-photo-progress progress">' +
        '    <div class="progress-bar progress-bar-info" role="progressbar"  data-dz-uploadprogress></div>' +
        '  </div>' +
        '</div>';

    Dropzone.autoDiscover = false;
    Dropzone.options.UploadFileForm = {
        paramName: "file",
        maxFiles: 10,
        maxFilesize: 25, // MB
        url: cake.url.upload_file,
        addRemoveLink: true,
        dictFileTooBig: cake.message.validate.dropzone_file_too_big,
        dictInvalidFileType: cake.message.validate.dropzone_invalid_file_type,
        dictMaxFilesExceeded: cake.message.validate.dropzone_max_files_exceeded,
        dictResponseError: cake.message.validate.dropzone_response_error,
        dictCancelUpload: cake.message.validate.dropzone_cancel_upload,
        dictCancelUploadConfirmation: cake.message.validate.dropzone_cancel_upload_confirmation,
        clickable: '#' + $uploadFileAttachButton.attr('id'),
        previewTemplate: previewTemplateDefault,
        thumbnailWidth: null,
        thumbnailHeight: 240,
        // ファイルがドロップされた時の処理
        addedfile: function (file) {
            // previewContainer をドロップエリアに応じて入れ替える
            this.previewsContainer = $('#' + $uploadFileForm._params.previewContainerID).get(0);

            // コールバック関数実行 (beforeAddedFile)
            $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].beforeAddedFile.call(this, file);

            // Dropzone デフォルトの処理を実行
            this.defaultOptions.addedfile.call(this, file);
        },
        // ファイルがドロップされた後
        accept: function (file, done) {
            // コールバック関数実行 (beforeAccept)
            $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].beforeAccept.call(this, file);

            $uploadFileForm.hide();
            done();

            // コールバック関数実行 (afterAccept)
            $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].afterAccept.call(this, file);
        },
        // ファイルアップロード完了時
        success: function (file, res) {
            var $preview = $(file.previewTemplate);
            // エラー
            if (res.error) {
                $preview.remove();
                PNotify.removeAll();
                new PNotify({
                    type: 'error',
                    title: cake.message.notice.d,
                    text: res.msg,
                    icon: "fa fa-check-circle",
                    delay: 4000,
                    mouse_reset: false
                });
                return;
            }

            // 処理成功
            // submit するフォームに hidden でファイルID追加
            var $form = $('#' + $uploadFileForm._params.formID);
            $form.append($('<input type=hidden name=data[file_id][]>').val(res.id).attr('id', res.id));

            // プレビューエリアをファイルオブジェクトにファイルIDを紐付ける
            $preview.data('file_id', res.id);
            file.file_id = res.id;

            // プログレスバー消す
            // 一瞬で消えるのを防止するため１秒待つ
            setTimeout(function () {
                $preview.find('.progress').css('visibility', 'hidden');
            }, 1000);

            // コールバック関数（afterSuccess）
            $uploadFileForm._callbacks[$uploadFileForm._params.previewContainerID].afterSuccess.call(this, file);
        },
        // サムネイル
        thumbnail: function (file, dataUrl) {
            var $container = $(file.previewTemplate).find('.dz-thumb-container');
            // 画像の場合はデフォルトの処理でサムネイル作成
            if (file.type.match(/image/)) {
                $container.find('.fa').hide();
                $container.find('.dz-thumb').show();
                this.defaultOptions.thumbnail.call(this, file, dataUrl);
            }
        },
        // ファイル削除ボタン押下時
        removedfile: function (file) {
            var $preview = $(file.previewTemplate);

            // キャンセルされたファイルの場合は処理しない
            if (file.status == Dropzone.CANCELED) {
                return;
            }

            // 既にDBに保存済のデータの場合（投稿編集時）
            if (file.saved_file) {
                // フォームの hidden を削除
                $('#AttachedFile_' + $preview.data('file_id')).remove();

                // 削除済ファイルの hidden を追加
                var $form = $('#' + $uploadFileForm._params.formID);
                $form.append($('<input type=hidden name=data[deleted_file_id][]>').val($preview.data('file_id')));

                // プレビューエリア削除
                $preview.fadeOut();
            }
            // 新しくアップロードするファイルの場合
            else {
                $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val($preview.data('file_id'));
                $.ajax({
                    url: cake.url.remove_file,
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    data: $removeFileForm.serialize()
                })
                    .done(function (res) {
                        PNotify.removeAll();
                        // エラー
                        if (res.error) {
                            new PNotify({
                                type: 'error',
                                title: cake.message.notice.d,
                                text: res.msg,
                                icon: "fa fa-check-circle",
                                delay: 4000,
                                mouse_reset: false
                            });
                            return;
                        }

                        // 成功
                        new PNotify({
                            type: 'success',
                            title: cake.word.success,
                            text: res.msg,
                            icon: "fa fa-check-circle",
                            delay: 2000,
                            mouse_reset: false
                        });
                        // ファイルIDの hidden 削除
                        $('#' + $preview.data('file_id')).remove();

                        $preview.fadeOut('fast', function () {
                            // コールバック関数実行 (afterRemoveFile)
                            var previewContainerID = $preview.parent().attr('id');
                            $uploadFileForm._callbacks[previewContainerID].afterRemoveFile.call(this, file);
                        });
                    })
                    .fail(function (res) {
                        PNotify.removeAll();
                        new PNotify({
                            type: 'error',
                            title: cake.message.notice.d,
                            text: cake.message.notice.d,
                            icon: "fa fa-check-circle",
                            delay: 4000,
                            mouse_reset: false
                        });
                    });
            }
        },
        // アップロードがキャンセルされたとき
        canceled: function (file) {
            var $preview = $(file.previewTemplate);
            // キャンセルを確認出来るようにファイルの名前を強調して少しの間表示しておく
            $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.cancel + ')');
            setTimeout(function () {
                $preview.remove();
            }, 4000);
            $uploadFileForm.hide();
            PNotify.removeAll();
            new PNotify({
                type: 'success',
                title: cake.word.success,
                text: cake.message.validate.dropzone_cancel_upload,
                icon: "fa fa-check-circle",
                delay: 4000,
                mouse_reset: false
            });
        },
        // ファイルアップロード失敗
        error: function (file, errorMessage) {
            var $preview = $(file.previewTemplate);
            // エラーと確認出来るように失敗したファイルの名前を強調して少しの間表示しておく
            $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.error + ')');
            setTimeout(function () {
                $preview.remove();
            }, 4000);
            $uploadFileForm.hide();
            PNotify.removeAll();
            new PNotify({
                type: 'error',
                title: cake.message.notice.d,
                text: errorMessage,
                icon: "fa fa-check-circle",
                delay: 8000,
                mouse_reset: false
            });
        }
    };

    // パラメータ
    $uploadFileForm._params = {};
    // コールバック関数
    $uploadFileForm._callbacks = {};
    // Dropzone のデフォルト設定
    $uploadFileForm._dzDefaultOptions = {};

    // 登録済ドロップエリアとアップロードボタン
    $uploadFileForm._dragDropArea = {};
    $uploadFileForm._attachFileButton = {};

    /**
     * ドラッグ＆ドロップ対象エリアを設定する
     *
     * selector : string  ドロップエリアにする要素のセレクタ
     * params: object {
     *   formID: string|function  *必須 アップロードしたファイルIDを hidden で追加するフォームのID
     *   previewContainerID: string|function  *必須  プレビューを表示するコンテナ要素のID
     *   beforeAddedFile: function  コールバック関数
     *   beforeAccept: function   コールバック関数
     *   afterAccept: function  コールバック関数
     *   afterRemoveFile: function  コールバック関数
     *   afterSuccess: function   コールバック関数
     * }
     * dzOptions: object {
     *    ...   Dropzone のオプション（デフォルトの設定を上書きする場合に指定）
     * }
     */
    $uploadFileForm.registerDragDropArea = function (selector, params, dzOptions) {
        if ($uploadFileForm._dragDropArea[selector]) {
            return true;
        }
        $uploadFileForm._dragDropArea[selector] = {
            selector: selector,
            params: params,
            dzOptions: dzOptions
        };

        $(document).on('dragover', selector, function (e) {
            e.preventDefault();
            $uploadFileForm._setParams(this, params, dzOptions);

            // ファイルアップロード用フォームのサイズと位置を合わせて重ねて表示させる
            var $dropArea = $(this);
            var pos = $dropArea.position();
            $uploadFileForm.appendTo($dropArea).css({
                width: $dropArea.outerWidth(),
                height: $dropArea.outerHeight(),
                paddingTop: $dropArea.outerHeight() / 2 - 18,
                top: pos.top,
                left: pos.left,
                position: 'absolute'
            }).addClass('drag-over').show().find('.upload-file-form-content').show();
        });
    };

    /**
     * ファイル添付用ボタンを登録する
     *
     * 引数は registerDragDropArea と同じ
     */
    $uploadFileForm.registerAttachFileButton = function (selector, params, dzOptions) {
        if ($uploadFileForm._attachFileButton[selector]) {
            return true;
        }
        $uploadFileForm._attachFileButton[selector] = {
            selector: selector,
            params: params,
            dzOptions: dzOptions
        };

        $(document).on('click', selector, function (e) {
            e.preventDefault();
            $uploadFileForm._setParams(this, params, dzOptions);
            $uploadFileAttachButton.trigger('click');
        });
    };

    // 各ドロップエリアの設定パラメータをセットし直す
    // ドロップエリアが切り替わる度に呼び出される
    $uploadFileForm._setParams = function (target, params, dzOptions) {
        var formID = (typeof params.formID == 'function') ? params.formID.call(target) : params.formID;
        var previewContainerID = (typeof params.previewContainerID == 'function') ? params.previewContainerID.call(target) : params.previewContainerID;
        $uploadFileForm._params.formID = formID;
        $uploadFileForm._params.previewContainerID = previewContainerID;

        // Dropzone 設定
        // （Dropzone インスタンスは常に１つ）
        Dropzone.instances[0].options = $.extend({}, $uploadFileForm._dzDefaultOptions, dzOptions || {});

        // コールバック関数登録
        var empty = function () {
        };
        $uploadFileForm._callbacks[previewContainerID] = {
            beforeAddedFile: params.beforeAddedFile ? params.beforeAddedFile : empty,
            beforeAccept: params.beforeAccept ? params.beforeAccept : empty,
            afterAccept: params.afterAccept ? params.afterAccept : empty,
            afterRemoveFile: params.afterRemoveFile ? params.afterRemoveFile : empty,
            afterSuccess: params.afterSuccess ? params.afterSuccess : empty
        };
    };

    // アップロードフォーム内の子要素の dragenter/dragleave イベントのチェック用
    var uploadFileFormContentEnter = false;
    $('.upload-file-form-content').on('dragenter', function (e) {
        uploadFileFormContentEnter = true;
    });

    // ドロップエリアから外れた時
    $uploadFileForm.on('dragleave', function (e) {
        if ($(e.target).hasClass('upload-file-form-content')) {
            uploadFileFormContentEnter = false;
            return;
        }
        if (uploadFileFormContentEnter) {
            return;
        }

        $(this).hide();
    });

    //////////////////////////////////////////////////
    // ドロップエリアとファイル添付ボタンの登録
    //////////////////////////////////////////////////

    ///////////////////////////////
    // 投稿フォーム
    ///////////////////////////////
    var postParams = {
        formID: 'PostDisplayForm',
        previewContainerID: 'PostUploadFilePreview'
    };
    $uploadFileForm.registerDragDropArea('#PostForm', postParams);
    $uploadFileForm.registerAttachFileButton('#PostUploadFileButton', postParams);

    ///////////////////////////////
    // アクションメイン画像（最初の画像選択時)
    ///////////////////////////////
    var actionImageParams = {
        formID: 'CommonActionDisplayForm',
        previewContainerID: 'ActionUploadFilePhotoPreview',
        beforeAccept: function (file) {
            var $oldPreview = $('#' + $uploadFileForm._params.previewContainerID).find('.dz-preview:visible');

            // 画像を２枚同時に選択（ドラッグ）された時の対応
            if ($oldPreview.size()) {
                // Dropzone の管理ファイルから外す
                var old_file = Dropzone.instances[0].files.splice(0, 1)[0];

                // プレビューエリアを非表示にする
                $oldPreview.hide();

                // フォームの hidden を削除
                $('#' + old_file.file_id).remove();

                // サーバ上から削除
                $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val(old_file.file_id);
                $.ajax({
                    url: cake.url.remove_file,
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    data: $removeFileForm.serialize()
                })
                    .done(function (res) {
                        // pass
                    })
                    .fail(function (res) {
                        // pass
                    });
            }
        },
        afterAccept: function (file) {
            var $button = $('.action-image-add-button');
            if ($button.size()) {
                evTargetShowThisDelete.call($button.get(0));
            }
            $(file.previewTemplate).show();
        }
    };
    var actionImageDzOptions = {
        acceptedFiles: "image/*",
        previewTemplate: previewTemplateActionImage
    };
    $uploadFileForm.registerDragDropArea('#ActionImageAddButton', actionImageParams, actionImageDzOptions);
    $uploadFileForm.registerAttachFileButton('#ActionImageAddButton', actionImageParams, actionImageDzOptions);

    ///////////////////////////////
    // アクションメイン画像（入れ替え時）
    ///////////////////////////////
    var actionImage2Params = {
        formID: 'CommonActionDisplayForm',
        previewContainerID: 'ActionUploadFilePhotoPreview',
        beforeAccept: function (file) {
            var $oldPreview = $('#' + $uploadFileForm._params.previewContainerID).find('.dz-preview:visible');

            // Dropzone の管理ファイルから外す
            var old_file = Dropzone.instances[0].files.splice(0, 1)[0];

            // プレビューエリアを非表示にする
            $oldPreview.hide();

            // 既にDBに保存済のデータの場合（アクション編集時）
            if (old_file.saved_file) {
                // フォームの hidden を削除
                $('#AttachedFile_' + old_file.file_id).remove();

                // 削除済ファイルの hidden を追加
                var $form = $('#' + $uploadFileForm._params.formID);
                $form.append($('<input type=hidden name=data[deleted_file_id][]>').val(old_file.file_id));
            }
            // 新しくアップロードするファイルの場合
            else {
                // フォームの hidden を削除
                $('#' + old_file.file_id).remove();

                // サーバ上から削除
                $removeFileForm.find('input[name="data[AttachedFile][file_id]"]').val(old_file.file_id);
                $.ajax({
                    url: cake.url.remove_file,
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    data: $removeFileForm.serialize()
                })
                    .done(function (res) {
                        // pass
                    })
                    .fail(function (res) {
                        // pass
                    });
            }
        },
        afterAccept: function (file) {
            $(file.previewTemplate).show();
        },
        afterSuccess: function (file) {
            // メイン画像の hidden を先頭に持ってくる
            // DB内の index 番号を 0 にするため
            var $form = $('#' + $uploadFileForm._params.formID);
            var file_id = $(file.previewTemplate).data('file_id');
            var $firstHidden = $form.find('input[name="data[file_id][]"]:first');
            if ($firstHidden.val() != file_id) {
                $('#' + file_id).insertBefore($firstHidden);
            }
        }
    };
    var actionImage2DzOptions = {
        acceptedFiles: "image/*",
        previewTemplate: previewTemplateActionImage
    };
    $uploadFileForm.registerDragDropArea('.action-photo-preview', actionImage2Params, actionImage2DzOptions);
    $uploadFileForm.registerAttachFileButton('.action-photo-preview', actionImage2Params, actionImage2DzOptions);

    ///////////////////////////////
    // アクション添付ファイル
    ///////////////////////////////
    var actionParams = {
        formID: 'CommonActionDisplayForm',
        previewContainerID: 'ActionUploadFilePreview',
        afterAccept: actionImageParams.afterAccept
    };
    $uploadFileForm.registerDragDropArea('#ActionUploadFileDropArea', actionParams);
    $uploadFileForm.registerAttachFileButton('#ActionFileAttachButton', actionParams);

    //////////////////////////////////////////////////
    // Dropzone 有効化
    //////////////////////////////////////////////////
    $uploadFileForm.dropzone();
    if (typeof Dropzone.instances[0] !== "undefined") {
        $uploadFileForm._dzDefaultOptions = $.extend({}, Dropzone.instances[0].options);
    }
    $(document).data('uploadFileForm', $uploadFileForm);

    //////////////////////////////////////////////////
    // 投稿、アクション の編集時の処理
    //////////////////////////////////////////////////

    // DB に保存済の添付ファイルデータを Dropzone に手動で登録する
    var dropzonePrepareEdit = function (setting) {
        var $input = $(this);

        var file = {};
        file.saved_file = true;
        file.name = $input.attr('data-name');
        file.size = $input.attr('data-size');

        file.upload = {
            progress: 100,
            total: file.size,
            bytesSent: file.size
        };
        file.status = Dropzone.SUCCESS;

        $uploadFileForm._setParams(setting.selector, setting.params, setting.dzOptions);
        Dropzone.instances[0].files.push(file);
        Dropzone.instances[0].options.addedfile.call(Dropzone.instances[0], file);
        file.previewElement.classList.remove("dz-file-preview");
        file.previewElement.querySelector('.progress').style.visibility = 'hidden';

        switch ($input.attr('data-ext')) {
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
                var thumb = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0, len = thumb.length; i < len; i++) {
                    thumb[i].alt = file.name;
                    thumb[i].src = $input.attr('data-url');
                }
                break;

            default:
                break;
        }
        file.file_id = $input.attr('value');
        $(file.previewElement).data('file_id', file.file_id).show();
    };

    // registerDragDropArea() か registerAttachFileButton() で登録されたフォームをチェックし、
    // <input type=hidden name=data[file_id][]> が存在すれば、Dropzone に初期データとして登録する
    var settings = {};
    var i, setting;
    for (i in $uploadFileForm._dragDropArea) {
        if (!$uploadFileForm._dragDropArea.hasOwnProperty(i)) {
            continue;
        }
        setting = $uploadFileForm._dragDropArea[i];
        settings[setting.params.previewContainerID] = setting;
    }
    for (i in $uploadFileForm._attachFileButton) {
        if (!$uploadFileForm._attachFileButton.hasOwnProperty(i)) {
            continue;
        }
        setting = $uploadFileForm._attachFileButton[i];
        settings[setting.params.previewContainerID] = setting;
    }
    for (i in settings) {
        if (!settings.hasOwnProperty(i)) {
            continue;
        }
        var $hiddens = $('#' + settings[i].params.formID).find('input[type=hidden][name="data[file_id][]"]');
        if (!$hiddens.size()) {
            continue;
        }

        var previewContainerID = settings[i].params.previewContainerID;
        // アクションのメイン画像の場合
        // hidden の最初の１件のみ処理
        if (previewContainerID == 'ActionUploadFilePhotoPreview') {
            dropzonePrepareEdit.call($hiddens.eq(0).get(0), settings[i]);
        }
        // アクションの添付ファイルの場合
        // hidden の最初の１件以外を処理
        else if (previewContainerID == 'ActionUploadFilePreview') {
            $hiddens.not(':first').each(function () {
                dropzonePrepareEdit.call(this, settings[i]);
            });
        }
        else {
            $hiddens.each(function () {
                dropzonePrepareEdit.call(this, settings[i]);
            });
        }
    }

    // アクションの編集画面の場合は、画像選択の画面をスキップし、
    // ajax で動いている select を選択済みにする
    var $button = $('#ActionForm').find('.action-image-add-button.skip');
    if ($button.size()) {
        // 画像選択の画面をスキップ
        evTargetShowThisDelete.call($button.get(0));
        // ゴール選択の ajax 処理を動かす
        $('#GoalSelectOnActionForm').trigger('change');
    }
});

function evAjaxEditCircleAdminStatus(e) {
    e.preventDefault();

    var $this = $(this);
    var user_id = $this.attr('data-user-id');

    $.ajax({
        url: $this.attr('action'),
        type: 'POST',
        dataType: 'json',
        processData: false,
        data: $this.serialize()
    })
        .done(function (data) {
            // 処理失敗時
            if (data.error) {
                new PNotify({
                    type: 'error',
                    title: data.message.title,
                    text: data.message.text,
                    icon: "fa fa-check-circle",
                    delay: 2000,
                    mouse_reset: false
                });
            }
            // 処理成功時
            else {
                new PNotify({
                    type: 'success',
                    title: data.message.title,
                    text: data.message.text,
                    icon: "fa fa-exclamation-triangle",
                    delay: 2000,
                    mouse_reset: false
                });

                // 操作者自身を情報を更新した場合
                if (data.self_update) {
                    window.location.href = '/';
                    return;
                }
                // 操作者以外の情報を更新した場合
                else {
                    var $member_row = $('#edit-circle-member-row-' + user_id);
                    // 非管理者 -> 管理者 の場合
                    if (data.result.admin_flg == "1") {
                        $member_row.find('.item-for-non-admin').hide();
                        $member_row.find('.item-for-admin').show();
                    }
                    // 管理者 -> 非管理者 の場合
                    else {
                        $member_row.find('.item-for-admin').hide();
                        $member_row.find('.item-for-non-admin').show();
                    }
                }
            }
        })
        .fail(function (data) {
            new PNotify({
                type: 'error',
                text: cake.message.notice.d,
                delay: 4000,
                mouse_reset: false
            });
        });
}

function evAjaxLeaveCircle(e) {
    e.preventDefault();

    var $this = $(this);
    var user_id = $this.attr('data-user-id');

    $.ajax({
        url: $this.attr('action'),
        type: 'POST',
        dataType: 'json',
        processData: false,
        data: $this.serialize()
    })
        .done(function (data) {
            // 処理失敗時
            if (data.error) {
                new PNotify({
                    type: 'error',
                    title: data.message.title,
                    text: data.message.text,
                    icon: "fa fa-check-circle",
                    delay: 2000,
                    mouse_reset: false
                });
            }
            // 処理成功時
            else {
                new PNotify({
                    type: 'success',
                    title: data.message.title,
                    text: data.message.text,
                    icon: "fa fa-exclamation-triangle",
                    delay: 2000,
                    mouse_reset: false
                });
                // 操作者自身の情報更新した場合
                if (data.self_update) {
                    window.location.href = '/';
                    return;
                }
                // 操作者以外の情報を更新した場合
                else {
                    var $member_row = $('#edit-circle-member-row-' + user_id);
                    $member_row.fadeOut('fast', function () {
                        $(this).remove();
                    });
                }
            }
        })
        .fail(function (data) {
            new PNotify({
                type: 'error',
                text: cake.message.notice.d,
                delay: 4000,
                mouse_reset: false
            });
        });
}

function setDefaultTab() {
    if (cake.common_form_type == "") {
        return;
    }
    switch (cake.common_form_type) {
        case "action":
            $('#CommonFormTabs li:eq(0) a').tab('show');
            break;
        case "post":
            $('#CommonFormTabs li:eq(1) a').tab('show');
            if (!isMobile()) {
                $('#CommonPostBody').focus();
            }
            break;
        case "message":
            $('#CommonFormTabs li:eq(2) a').tab('show');
            if (!isMobile()) {
                $('#CommonMessageBody').focus();
            }
            break;
    }
}

function isMobile() {
    var agent = navigator.userAgent;
    if (agent.search(/iPhone/) != -1 ||
        agent.search(/iPad/) != -1 ||
        agent.search(/iPod/) != -1 ||
        agent.search(/Android/) != -1
    ) {
        return true;
    }
    return false;
}
