// Sentry:js error tracking
if (cake.sentry_dsn && (cake.env_name !== 'local' && cake.env_name !== 'develop')) {
  Raven.config(
    cake.sentry_dsn,
    {
      environment: cake.env_name
    }
  ).install();
}

$.ajaxSetup({
  cache: false,
  timeout: 10000 // 10 sec
});

if (typeof String.prototype.startsWith != 'function') {
  // see below for better implementation!
  String.prototype.startsWith = function (str) {
    return this.indexOf(str) === 0;
  };
}
;
require.config({
  baseUrl: '/js/modules/'
});


/**
 * 画像の高さを親の要素に割り当てる
 *
 * @param $obj
 */
function changeSizeActionImage($obj) {
  console.log("gl_basic.js: changeSizeActionImage");
  $obj.each(function (i, v) {
    var $elm = $(v);
    var $img = $elm.find('img');
    var imgWidth = $img[0].width;
    var imgHeight = $img[0].height;

    var is_oblong = imgWidth > imgHeight;
    var is_near_square = Math.abs(imgWidth - imgHeight) <= 5;

    // 横長の画像か、ほぼ正方形に近い画像の場合はそのまま表示
    if (is_oblong || is_near_square) {
      $elm.css('height', imgHeight);
      $img.parent().css('height', imgHeight);
    }
    // 縦長の画像は、4:3 の比率にする
    else {
      var expect_parent_height = imgWidth * 0.75;

      $elm.css('height', expect_parent_height);
      $img.parent().css('height', expect_parent_height);
    }
  });
}


// selectorの存在確認用
jQuery.fn.exists = function () {
  console.log("gl_basic.js: jQuery.fn.exists");
  return Boolean(this.length > 0);
}

// scrollbarの存在確認用
jQuery.fn.hasScrollBar = function () {
  console.log("gl_basic.js: jQuery.fn.hasScrollBar");
  return this.get(0) ? this.get(0).scrollHeight > this.innerHeight() : false;
}


$(document).ready(function () {
    console.log("gl_basic.js: $(document).ready");

  // Androidアプリかiosアプリの場合のみfastClickを実行する。
  // 　→iosでsafari/chromeでfastClick使用時、チェックボックス操作に不具合が見つかったため。
  if (cake.is_mb_app === 'true' || cake.is_mb_app_ios === 'true') {
    fastClick();
  }










  $("a.youtube").YouTubeModal({autoplay: 0, width: 640, height: 360});



  //ヘッダーサブメニューでのフィード、ゴール切り換え処理
  //noinspection JSJQueryEfficiency

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



  $('.js-close-dropdown').on('click', function (e) {
    e.preventDefault();
    $(this).closest('dropdown').removeClass('open');
  });

  //tab open
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var $target = $(e.target);
    if ($target.hasClass('click-target-focus') && $target.attr('target-id') != undefined) {
      $('#' + $target.attr('target-id')).click();
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






  //autosize
  //noinspection JSJQueryEfficiency
  autosize($('textarea:not(.not-autosize)'));
  //noinspection JSJQueryEfficiency
  $('textarea:not(.not-autosize)').show().trigger('autosize.resize');


  //carousel
  $('.carousel').carousel({interval: false});



  $(document).on("click", ".click-show", evShow);
  $(document).on("click", ".trigger-click", evTriggerClick);
  //noinspection SpellCheckingInspection
  $(document).on("keyup", ".blank-disable-and-undisable", evBlankDisableAndUndisable);
  //noinspection SpellCheckingInspection
  $(document).on("keyup", ".blank-disable", evBlankDisable);

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

  //noinspection JSUnresolvedVariable,JSUnresolvedFunction
  $(document).on("click", ".click-show-post-modal", getModalPostList);
  //noinspection JSUnresolvedVariable
  $(document).on("click", ".toggle-follow", evFollowGoal);


  // TODO:delete.進捗グラフリリース時に不要になるので必ず削除
  $(document).on("click", '.js-show-modal-edit-kr', function (e) {
      e.preventDefault();
      var url = $(this).attr('href');
      if (url.indexOf('#') == 0) {
        $(url).modal('open');
      } else {
        var kr_id = $(this).data('kr_id');
        $(this).modalKrEdit({kr_id: kr_id});
      }
    }
  );

  $(document).on("touchend", "#layer-black", function () {
    $('.navbar-offcanvas').offcanvas('hide');
  });
  $(document).on("touchstart", ".nav-back-btn", function () {
    $('.nav-back-btn').addClass('mod-touchstart');
  });
  $(document).on("touchend", ".nav-back-btn", function () {
    $('.nav-back-btn').removeClass('mod-touchstart');
  });
  //evToggleAjaxGet
  $(document).on("click", ".toggle-ajax-get", evToggleAjaxGet);
  $(document).on("click", ".ajax-get", evAjaxGetElmWithIndex);
  $(document).on("click", ".click-target-remove", evTargetRemove);
  //dynamic modal
  $(document).on("click", '.modal-ajax-get', function (e) {
    e.preventDefault();
    var $this = $(this);
    if ($this.hasClass('double_click')) {
      return false;
    }
    $this.addClass('double_click');
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    if ($this.hasClass('remove-on-hide')) {
      $modal_elm.on('hidden.bs.modal', function (e) {
        $modal_elm.remove();
      });
    }
    //noinspection CoffeeScriptUnusedLocalSymbols,JSUnusedLocalSymbols
    modalFormCommonBindEvent($modal_elm);
    var url = $this.data('url');
    if (url.indexOf('#') === 0) {
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
        $this.removeClass('double_click');
        $('body').addClass('modal-open');
      });
    }
  });


  //noinspection JSUnresolvedVariable
  $(document).on("click", '.modal-ajax-get-collab', getModalFormFromUrl);
  $(document).on("click", '.modal-ajax-get-exchange-tkr', getModalFormFromUrl);
  $(document).on("click", '.modal-ajax-get-exchange-leader', getModalFormFromUrl);
  //noinspection JSUnresolvedVariable
  $(document).on("click", '.modal-ajax-get-add-key-result', getModalFormFromUrl);
  $('.ModalActionResult_input_field').on('change', function () {
    $('#AddActionResultForm').bootstrapValidator('revalidateField', 'photo');
  });






  // KR進捗の詳細値を表示
  $(document).on("click", '.js-show-detail-progress-value', function (e) {
    var current_value = $(this).data('current_value');
    var start_value = $(this).data('start_value');
    var target_value = $(this).data('target_value');
    $(this).find('.krProgress-text').text(current_value);
    $(this).find('.krProgress-valuesLeft').text(start_value);
    $(this).find('.krProgress-valuesRight').text(target_value);
  });



  $(document).on("click", ".click-goal-follower-more", evAjaxGoalFollowerMore);
  $(document).on("click", ".click-goal-member-more", evAjaxGoalMemberMore);
  $(document).on("click", ".click-goal-key-result-more", evAjaxGoalKeyResultMore);

  // 投稿フォーム submit 時
  $(document).on('submit', '#PostDisplayForm', function (e) {
    return checkUploadFileExpire('PostDisplayForm');
  });



  // HACK:To occur to_user_ids change event in react app
  $(document).on('change', '.js-changeSelect2Member', function (e) {
    $('.js-triggerUpdateToUserIds').trigger('click');
  });

  // リカバリコード再生成
  $(document).on('click', '#RecoveryCodeModal .regenerate-recovery-code', function (e) {
    e.preventDefault();
    var $form = $('#RegenerateRecoveryCodeForm');
    $.ajax({
      url: cake.url.regenerate_recovery_code,
      type: 'POST',
      dataType: 'json',
      processData: false,
      data: $form.serialize()
    })
      .done(function (res) {
        if (res.error) {
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>'+res.msg,
            }).show();
          return;
        }
        else {
          var $list_items = $('#RecoveryCodeList').find('li');
          for (var i = 0; i < 10; i++) {
            $list_items.eq(i).text(res.codes[i].slice(0, 4) + ' ' + res.codes[i].slice(-4));
          }
          new Noty({
              type: 'success',
              text: '<h4>'+cake.word.success+'</h4>'+res.msg,
          }).show();
        }


      })
      .fail(function () {
          new Noty({
              type: 'error',
              text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.d,
          }).show();
      });
  });


  if (typeof cake.request_params.named.after_click !== 'undefined') {
    $("#" + cake.request_params.named.after_click).trigger('click');
  }
  if (typeof cake.request_params.after_click !== 'undefined') {
    $("#" + cake.request_params.after_click).trigger('click');
  }


    // TEAM
    $(document).on("submit", "form.ajax-csv-upload", uploadCsvFileByForm);
});


$(function () {
    console.log("gl_basic.js: $(function ()");
    $(".click-show").on("click", function () {
            console.log("gl_basic.js: click");
            $("#PostFormPicture").css("display", "block")
        }
    )
});


$(function () {
    console.log("gl_basic.js: $(function ()");
    var current_slide_id = 1;

    // インジケータークリック時
    $(document).on('click', '.setup-tutorial-indicator', function () {
        console.log("gl_basic.js: click");
        resetDisplayStatus();
        changeTutorialContent($(this).attr('data-id'));
    });

    // ネクストボタンクリック時
    $(document).on('click', '.tutorial-next-btn', function () {
        console.log("gl_basic.js: click");
        if (current_slide_id == 3) {
            location.href = "/setup/";
            return;
        }
        resetDisplayStatus();

        var next_id = String(Number(current_slide_id) + 1);
        changeTutorialContent(next_id);
    });

    function changeTutorialContent(content_id) {
        console.log("gl_basic.js: changeTutorialContent");
        // 各要素をカレントステータスに設定
        $('.tutorial-box' + content_id).show();
        $('.tutorial-text' + content_id).show();
        $('.setup-tutorial-indicator' + content_id).addClass('setup-tutorial-navigation-indicator-selected');

        current_slide_id = content_id;
    }

    function resetDisplayStatus() {
        console.log("gl_basic.js: resetDisplayStatus");
        $('.tutorial-body').children('div').hide();
        $('.setup-tutorial-texts').children('div').hide();
        $('.setup-tutorial-navigation-indicator').children('span').removeClass('setup-tutorial-navigation-indicator-selected');
    }
});



$(document).ready(function () {
    console.log("gl_basic.js: $(document).ready");
    var click_cnt = 0;

    $('#HeaderDropdownNotify')
        .on('shown.bs.dropdown', function () {
            $("body").addClass('notify-dropdown-open');
        })
        .on('hidden.bs.dropdown', function () {
            $('body').removeClass('notify-dropdown-open');
        });
});


$(document).ready(function () {
    console.log("gl_basic.js: $(document).ready");

    $(document).on("click", '.modal-ajax-get-public-circles', function (e) {
        e.preventDefault();
        var $this = $(this);
        if ($this.hasClass('double_click')) {
            return false;
        }
        $this.addClass('double_click');
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $(this).remove();
        });
        var url = $(this).data('url');
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
                })
                // 参加/不参加 のスイッチ切り替えた時
                // 即時データを更新する
                    .on('switchChange.bootstrapSwitch', function (e, state) {
                        var $checkbox = $(this);
                        var $form = $('#CircleJoinForm');
                        $form.find('input[name="data[Circle][0][join]"]').val(state ? '1' : '0');
                        $form.find('input[name="data[Circle][0][circle_id]"]').val(sanitize($checkbox.attr('data-id')));

                        // 秘密サークルの場合は確認ダイアログ表示
                        if ($checkbox.attr('data-secret') == '1') {
                            if (!confirm(cake.message.notice.leave_secret_circle)) {
                                $checkbox.bootstrapSwitch('toggleState', true);
                                return false;
                            }
                            $checkbox.bootstrapSwitch('toggleDisabled', true);
                        }

                        $.ajax({
                            url: cake.url.join_circle,
                            type: 'POST',
                            dataType: 'json',
                            processData: false,
                            data: $form.serialize()
                        })
                            .done(function (res) {
                                new Noty({
                                    type: 'success',
                                    text: '<h4>'+cake.word.success+'</h4>'+res.msg,
                                }).show();
                                // 秘密サークルの場合は一覧から消す
                                if ($checkbox.attr('data-secret') == '1') {
                                    setTimeout(function () {
                                        $checkbox.closest('.circle-item-row').slideUp('slow');
                                    }, 1000);
                                }
                                // データを更新した場合はモーダルを閉じた時に画面リロード
                                $modal_elm.on('hidden.bs.modal', function () {
                                    location.reload();
                                });
                            })
                            .fail(function () {
                                new Noty({
                                    type: 'error',
                                    text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.d,
                                }).show();
                            });
                    });
            }).done(function () {
                $('body').addClass('modal-open');
                $this.removeClass('double_click');
            });
        }
    });


    $('#PostDisplayForm, #CommonActionDisplayForm, #MessageDisplayForm').change(function (e) {
        var $target = $(e.target);
        switch ($target.attr('id')) {
            case "CommonPostBody":
                $('#CommonActionName').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonActionName'));
                $('#CommonMessageBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonMessageBody'));
                break;
            case "CommonActionName":
                $('#CommonPostBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonPostBody'));
                $('#CommonMessageBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonMessageBody'));
                break;
            case "CommonMessageBody":
                $('#CommonPostBody').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonPostBody'));
                $('#CommonActionName').val($target.val()).trigger('autosize.resize');
                autosize($('#CommonActionName'));
                break;
        }
    });
});


function evTargetRemove() {
    console.log("gl_basic.js: evTargetRemove");
  attrUndefinedCheck(this, 'target-selector');
  var $obj = $(this);
  var target_selector = $obj.attr("target-selector");
  $(target_selector).remove();
  return false;
}
function evAjaxGetElmWithIndex(e) {
    console.log("gl_basic.js: evAjaxGetElmWithIndex");
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
    console.log("gl_basic.js: evToggleAjaxGet");
  attrUndefinedCheck(this, 'target-id');
  attrUndefinedCheck(this, 'ajax-url');
  var $obj = $(this);
  var target_id = sanitize($obj.attr("target-id"));
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
    console.log("gl_basic.js: evTargetShowThisDelete");
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
    console.log("gl_basic.js: evTargetShowTargetDelete");
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
    console.log("gl_basic.js: evTargetEnabled");
  attrUndefinedCheck(this, 'target-id');
  var $obj = $(this);
  var target_id = $obj.attr("target-id");
  $("#" + target_id).removeAttr("disabled");
  return true;
}
function evSelectOptionTargetHidden() {
    console.log("gl_basic.js: evSelectOptionTargetHidden");
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

function evToggle() {
    console.log("gl_basic.js: evToggle");
  attrUndefinedCheck(this, 'target-id');
  var target_id = $(this).attr('target-id');
  if ($(this).attr('disabled')) {
    return;
  }
  $("#" + target_id).toggle();
  return true;
}

/**
 * target_idの属性に対象となるIDがセットするとブランクの場合にdisabledにする。
 * 再度ブランクではない状態になったらdisabledを削除する。
 * target_idは,区切りで複数の要素を指定可能
 */
function evBlankDisableAndUndisable() {
    console.log("gl_basic.js: evBlankDisableAndUndisable");
  attrUndefinedCheck(this, 'target-id');
  var $obj = $(this);
  var target_ids = $obj.attr("target-id");
  target_ids = target_ids.split(',');
  if ($obj.val().length == 0) {
    for (var i = 0; i < target_ids.length; i++) {
      $("#" + target_ids[i]).attr("disabled", "disabled");
    }
  }
  else {
    for (var i = 0; i < target_ids.length; i++) {
      $("#" + target_ids[i]).removeAttr("disabled");
    }
  }
}
/**
 * target_idの属性に対象となるIDがセットするとブランクの場合にdisabledにする。
 * target_idは,区切りで複数の要素を指定可能
 */
function evBlankDisable() {
    console.log("gl_basic.js: evBlankDisable");
  attrUndefinedCheck(this, 'target-id');
  var $obj = $(this);
  var target_ids = $obj.attr("target-id");
  target_ids = target_ids.split(',');
  if ($obj.val().length == 0) {
    for (var i = 0; i < target_ids.length; i++) {
      $("#" + target_ids[i]).attr("disabled", "disabled");
    }
  }
}

function evTriggerClick() {
    console.log("gl_basic.js: evTriggerClick");
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
    console.log("gl_basic.js: evShow");
  //クリック済みの場合は処理しない
  if ($(this).hasClass('clicked'))return;

  //autosizeを一旦、切る。
  $(this).trigger('autosize.destroy');
  //再度autosizeを有効化
  autosize($(this));
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
    console.log("gl_basic.js: evShowAndThisWide");
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
  autosize($(this));

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
    console.log("gl_basic.js: setSelectOptions");
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

function evShowAndThisWideClose() {
    console.log("gl_basic.js: evShowAndThisWideClose");
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



function warningAction($obj) {
    console.log("gl_basic.js: warningAction");
  var flag = false;
  $obj.on('shown.bs.modal', function (e) {
    setTimeout(function () {
      $obj.find(":input").each(function () {
        var default_val = "";
        var changed_val = "";
        default_val = $(this).val();
        $(this).on("change keyup keydown", function () {
          changed_val = $(this).val();
          if (default_val != changed_val) {
            $(this).addClass("changed");
          } else {
            $(this).removeClass("changed");
          }
        });
      });
      $(document).on('submit', 'form', function () {
        flag = true;
      });
    }, 2000);
  });

  $obj.on('hide.bs.modal', function (e) {
    //datepickerが閉じた時のイベントをなぜかここで掴んでしまう為、datepickerだった場合は何もしない。
    if ('date' in e) {
      return;
    }
    if ($obj.find(".changed").length != "" && flag == false) {
      if (!confirm(cake.message.notice.a)) {
        e.preventDefault();
      } else {
        $.clearInput($(this));
      }
    }
  });
}

function modalFormCommonBindEvent($modal_elm) {
    console.log("gl_basic.js: modalFormCommonBindEvent");
  warningAction($modal_elm);
  $modal_elm.on('shown.bs.modal', function (e) {
    $(this).find('textarea').each(function () {
      autosize($(this));
    });
  });
}






function evFollowGoal() {
    console.log("gl_basic.js: evFollowGoal");
  attrUndefinedCheck(this, 'goal-id');
  attrUndefinedCheck(this, 'data-class');
  var $obj = $(this);
  var goal_id = sanitize($obj.attr('goal-id'));
  var data_class = sanitize($obj.attr('data-class'));
  var url = cake.url.c;
  $.ajax({
    type: 'GET',
    url: url + goal_id,
    async: true,
    dataType: 'json',
    success: function (data) {
      if (data.error) {
          new Noty({
              type: 'error',
              text: '<h4>'+cake.word.error+'</h4>'+data.msg,
          }).show();
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
        new Noty({
            type: 'error',
            text: '<h4>'+cake.word.error+'</h4>'+cake.message.notice.c,
        }).show();
    }
  });
  return false;
}
function getModalPostList(e) {
    console.log("gl_basic.js: getModalPostList");
  e.preventDefault();

  var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
  $modal_elm.on('hidden.bs.modal', function (e) {
    $(this).remove();
    action_autoload_more = false;
  });
  //noinspection CoffeeScriptUnusedLocalSymbols,JSUnusedLocalSymbols
  modalFormCommonBindEvent($modal_elm);

  var url = $(this).attr('href');
  if (url.indexOf('#') == 0) {
    $(url).modal('open');
  } else {
    $.get(url, function (data) {
      $modal_elm.modal();
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

      $modal_elm.find('.custom-radio-check').customRadioCheck();
      $modal_elm.find('form').bootstrapValidator().on('success.form.bv', function (e) {
        validatorCallback(e)
      });
      // アクションリストのオートローディング
      //
      var prevScrollTopAction = 0;
      $modal_elm.find('.modal-body').scroll(function () {
        var $this = $(this);
        var currentScrollTopAction = $this.scrollTop();
        if (prevScrollTopAction < currentScrollTopAction && ($this.get(0).scrollHeight - currentScrollTopAction <= $this.height() + 1500)) {
          if (!action_autoload_more) {
            action_autoload_more = true;
            $modal_elm.find('.click-feed-read-more').trigger('click');
          }
        }
        prevScrollTopAction = currentScrollTopAction;
      });
      // 画像読み込み完了後に画像サイズから要素の高さを割り当てる
      $modal_elm.imagesLoaded(function () {
        changeSizeActionImage($modal_elm.find('.feed_img_only_one'));
      });

    }).success(function () {
      $('body').addClass('modal-open');
    });
  }
}






// ゴールのフォロワー一覧を取得
function evAjaxGoalFollowerMore() {
    console.log("gl_basic.js: evAjaxGoalFollowerMore");
  var $obj = $(this);
  $obj.attr('ajax-url', cake.url.goal_followers + '/goal_id:' + $obj.attr('goal-id'));
  return evBasicReadMore.call(this);
}

// ゴールのメンバー一覧を取得
function evAjaxGoalMemberMore() {
    console.log("gl_basic.js: evAjaxGoalMemberMore");
  var $obj = $(this);
  $obj.attr('ajax-url', cake.url.goal_members + '/goal_id:' + $obj.attr('goal-id'));
  return evBasicReadMore.call(this);
}

// ゴールのキーリザルト一覧を取得
function evAjaxGoalKeyResultMore() {
    console.log("gl_basic.js: evAjaxGoalKeyResultMore");
  var $obj = $(this);
  var kr_can_edit = $obj.attr('kr-can-edit');
  var goal_id = $obj.attr('goal-id');
  $obj.attr('ajax-url', cake.url.goal_key_results + '/' + kr_can_edit + '/goal_id:' + goal_id + '/view:key_results');
  return evBasicReadMore.call(this, {
    afterSuccess: function ($content) {
      imageLazyOn($content);
    }
  });
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


function evBasicReadMore(options) {
    console.log("gl_basic.js: evBasicReadMore");
  $.extend({
    afterSuccess: function ($content) {
    }
  }, options);

  var $obj = $(this);
  var ajax_url = $obj.attr('ajax-url');
  var next_page_num = sanitize($obj.attr('next-page-num'));
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
        $content.fadeOut();
        $list_container.append($content);

        showMore($content);
        $content.fadeIn();

        // ページ番号インクリメント
        next_page_num++;
        $obj.attr('next-page-num', next_page_num);

        // ローダーを削除
        $loader_html.remove();

        // リンクを有効化
        $obj.removeAttr('disabled');

        options.afterSuccess($content);
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
    }
  });
  return false;
}




function getModalFormFromUrl(e) {
    console.log("gl_basic.js: getModalFormFromUrl");
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
  $modal_elm.on('hidden.bs.modal', function (e) {
    $(this).empty();
  });

  var url = $(this).data('url');
  if (url.indexOf('#') == 0) {
    $(url).modal('open');
  } else {
    $.get(url, function (data) {
      $modal_elm.append(data);
      $modal_elm.find('form').bootstrapValidator({
        live: 'enabled',

        fields: {
          "data[KeyResult][start_date]": {
            validators: {
              callback: {
                message: cake.message.notice.e,
                callback: function (value, validator) {
                  var m = new moment(value, 'YYYY/MM/DD', true);
                  return m.isBefore($('[name="data[KeyResult][end_date]"]').val());
                }
              },
              date: {
                format: 'YYYY/MM/DD',
                message: cake.message.validate.date_format
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
              },
              date: {
                format: 'YYYY/MM/DD',
                message: cake.message.validate.date_format
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









// TEAM
$(function () {
    console.log("LOADING: gl_basic.js TEAM");

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
    console.log("gl_basic.js: uploadCsvFileByForm");
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


// USER - ACCOUNT
$(function () {
    console.log("LOADING: gl_basic.js USER");
    $(document).on("click", '#ShowRecoveryCodeButton', function (e) {
        e.preventDefault();
        var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
        $modal_elm.on('hidden.bs.modal', function (e) {
            $modal_elm.remove();
        });
        var url = $(this).attr('href');
        $.get(url, function (data) {
            $modal_elm.append(data);
            // ２段階認証設定後、自動で modal を開いた場合は背景クリックで閉じれないようにする
            $modal_elm.modal({
                backdrop: e.isTrigger ? 'static' : true
            });
        }).success(function () {
            $('body').addClass('modal-open');
        });
    });

    //Load term goal
    $('#LoadTermGoal').change(function () {
        var term_id = $(this).val();
        if (term_id == "") {
            var url = $(this).attr('redirect-url');
        }
        else {
            var url = $(this).attr('redirect-url') + "/term_id:" + term_id;
        }
        location.href = url;
    });
});