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
    $('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 50, height: 50, fitDirection: 'center center'});
    });
    //チーム切り換え
    $('#SwitchTeam').change(function () {
        var val = $(this).val();
        var url = "/teams/ajax_switch_team/" + val;
        $.get(url, function (data) {
            location.href = data;
        });
    });
    //投稿の共有範囲切り替え
    $('#ChangeShareSelect2').click(function () {
        attrUndefinedCheck(this, 'show-target-id');
        attrUndefinedCheck(this, 'target-id');
        var show_target_id = $(this).attr('show-target-id');
        var target_id = $(this).attr('target-id');
        $("#" + show_target_id).show();
        $("#" + target_id).find('ul.select2-choices').click();
        return false;
    });
    //autosize
    //noinspection JSJQueryEfficiency
    $('textarea').autosize();
    //noinspection JSJQueryEfficiency
    $('textarea').show().trigger('autosize.resize');

    //noinspection JSJQueryEfficiency,JSUnresolvedFunction
    imageLazyOn();
    //showmore
    //noinspection JSUnresolvedFunction
    showMore();
    //carousel
    $('.carousel').carousel({interval: false});

    $('.gl-custom-radio-check').customRadioCheck();

    //bootstrap switch
    $(".bt-switch").bootstrapSwitch();
    //bootstrap tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
    //form二重送信防止
    $('form').on('submit', function () {
        $(this).find('input:submit').attr('disabled', 'disabled');
    });
    /**
     * ajaxで取得するコンテンツにバインドする必要のあるイベントは以下記述で追加
     */
    $(document).on("blur", ".blur-height-reset", evThisHeightReset);
    $(document).on("focus", ".click-height-up", evThisHeightUp);
    $(document).on("click", ".tiny-form-text", evShowAndThisWide);
    $(document).on("click", ".click-show", evShow);
    $(document).on("click", ".trigger-click", evTriggerClick);
    //noinspection SpellCheckingInspection
    $(document).on("keyup", ".blank-disable", evBlankDisable);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-feed-read-more", evFeedMoreView);
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".click-comment-all", evCommentAllView);
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
    //noinspection JSUnresolvedVariable
    $(document).on("click", ".toggle-follow", evFollowGoal);
    $(document).on("touchend", "#layer-black", function () {
        $('.navbar-offcanvas').offcanvas('hide');
    });
    //evToggleAjaxGet
    $(document).on("click", ".toggle-ajax-get", evToggleAjaxGet);
    //dynamic modal
    $(document).on("click", '.modal-ajax-get', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
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
    $(document).on("click", '.modal-ajax-get-share-circles-users', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
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
    $(document).on("click", '.modal-ajax-get-collabo', getModalFormFromUrl);
    $(document).on("click", '.modal-ajax-get-add-key-result', getModalFormFromUrl);
    $(document).on("click", '.modal-ajax-get-circle-edit', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('shown.bs.modal', function (e) {
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
                            enabled: false
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
        $('.container').css('position', 'fixed');
        $('#layer-black').css('display', 'block');
    });
    //noinspection JSJQueryEfficiency
    $('.navbar-offcanvas').on('hide.bs.offcanvas', function () {
        $('.container').css('position', '');
        $('#layer-black').css('display', 'none');
    });

});
$(function () {
    $('textarea').bind('load', function () {
        var h = $('textarea').css('height');
    });
});

function getModalFormFromUrl(e) {
    e.preventDefault();
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
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
                feedbackIcons: {}
            });
            $modal_elm.modal();
            $('body').addClass('modal-open');
        });
    }
}

function imageLazyOn() {
    $("img.lazy").lazy({
        bind: "event",
        attribute: "data-original",
        combined: true,
        delay: 100,
        visibleOnly: false,
        effect: "fadeIn",
        removeAttribute: false
    });
}
function evToggleAjaxGet() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'ajax-url');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var ajax_url = $obj.attr("ajax-url");

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
    $('#' + target_id).addClass('data-exists');
    $('#' + target_id).toggle();
    return false;
}
function evTargetToggleClick() {
    attrUndefinedCheck(this, 'target-id');
    attrUndefinedCheck(this, 'click-target-id');

    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    var click_target_id = $obj.attr("click-target-id");
    if ($obj.attr("hidden-target-id")) {
        $('#' + $obj.attr("hidden-target-id")).hide();
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
    console.log(target_id);
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

$(function () {
    $(".develop--search").click(
        function () {
            $(this).attr('placeholder', '準備中です。');
        }
    );
});


// Workaround for buggy header/footer fixed position when virtual keyboard is on/off
$('input, textarea')
    .on('focus', function () {
        $('.navbar').css('position', 'absolute');
    })
    .on('blur', function () {
        $('.navbar').css('position', 'fixed');
        //force page redraw to fix incorrectly positioned fixed elements
        setTimeout(function () {
            window.scrollTo($.mobile.window.scrollLeft(), $.mobile.window.scrollTop());
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
        $("body,html").animate({
            scrollTop: 0
        }, 500, 'swing');
        return false;
    });
});

$(function () {
    $("#gotop").hover(
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
