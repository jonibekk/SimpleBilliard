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

var network_reachable = true;
var enabled_intercom_icon = (typeof enabled_intercom_icon === "undefined") ? null : enabled_intercom_icon;

function bindPostBalancedGallery($obj) {
  $obj.removeClass('none');
  $obj.BalancedGallery({
    autoResize: false,                   // re-partition and resize the images when the window size changes
    //background: '#DDD',                   // the css properties of the gallery's containing element
    idealHeight: 150,                  // ideal row height, only used for horizontal galleries, defaults to half the containing element's height
    //idealWidth: 100,                   // ideal column width, only used for vertical galleries, defaults to 1/4 of the containing element's width
    //maintainOrder: false,                // keeps images in their original order, setting to 'false' can create a slightly better balance between rows
    orientation: 'horizontal',          // 'horizontal' galleries are made of rows and scroll vertically; 'vertical' galleries are made of columns and scroll horizontally
    padding: 1,                         // pixels between images
    shuffleUnorderedPartitions: true,   // unordered galleries tend to clump larger images at the begining, this solves that issue at a slight performance cost
    //viewportHeight: 400,               // the assumed height of the gallery, defaults to the containing element's height
    //viewportWidth: 482                // the assumed width of the gallery, defaults to the containing element's width
  });

};
function bindCommentBalancedGallery($obj) {
  $obj.removeClass('none');
  $obj.BalancedGallery({
    autoResize: false,                   // re-partition and resize the images when the window size changes
    //background: '#DDD',                   // the css properties of the gallery's containing element
    idealHeight: 130,                  // ideal row height, only used for horizontal galleries, defaults to half the containing element's height
    //idealWidth: 100,                   // ideal column width, only used for vertical galleries, defaults to 1/4 of the containing element's width
    //maintainOrder: false,                // keeps images in their original order, setting to 'false' can create a slightly better balance between rows
    orientation: 'horizontal',          // 'horizontal' galleries are made of rows and scroll vertically; 'vertical' galleries are made of columns and scroll horizontally
    padding: 1,                         // pixels between images
    shuffleUnorderedPartitions: true,   // unordered galleries tend to clump larger images at the begining, this solves that issue at a slight performance cost
    //viewportHeight: 400,               // the assumed height of the gallery, defaults to the containing element's height
    //viewportWidth: 482                // the assumed width of the gallery, defaults to the containing element's width
  });
};

function changeSizeFeedImageOnlyOne($obj) {
  $obj.each(function (i, v) {
    var $elm = $(v);
    var $img = $elm.find('img');
    var is_oblong = $img.width() > $img.height();
    var is_near_square = Math.abs($img.width() - $img.height()) <= 5;

    // 横長の画像か、ほぼ正方形に近い画像の場合はそのまま表示
    if (is_oblong || is_near_square) {
      $elm.css('height', $img.height());
      $img.parent().css('height', $img.height());
    }
    // 縦長の画像は、4:3 の比率にする
    else {
      var expect_parent_height = $img.width() * 0.75;

      $elm.css('height', expect_parent_height);
      $img.parent().css('height', expect_parent_height);
    }
  });
}
/**
 * 画像の高さを親の要素に割り当てる
 *
 * @param $obj
 */
function changeSizeActionImage($obj) {
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


/**
 * selector の要素に Control(Command) + Enter 押下時のアクションを設定する
 *
 * @param selector
 * @param callback Control + Enter が押された時に実行されるコールバック関数
 */
var bindCtrlEnterAction = function (selector, callback) {
  $(document).on('keydown', selector, function (e) {
    if ((e.metaKey || e.ctrlKey) && e.keyCode == 13) {
      callback.call(this, e);
    }
  })
};

// selectorの存在確認用
jQuery.fn.exists = function () {
  return Boolean(this.length > 0);
}

// scrollbarの存在確認用
jQuery.fn.hasScrollBar = function () {
  return this.get(0) ? this.get(0).scrollHeight > this.innerHeight() : false;
}

$(window).load(function () {
  bindPostBalancedGallery($('.post_gallery'));
  bindCommentBalancedGallery($('.comment_gallery'));
  changeSizeFeedImageOnlyOne($('.feed_img_only_one'));
  setDefaultTab();

  // for setting the team_id_current in local storage
  clickToSetCurrentTeamId();

  // if team changed from other tab then don't allow user to proceed without reload
  $('body').click(function () {
    if (Number(cake.data.team_id) !== Number(localStorage.team_id_current)) {
      var r = confirm(cake.translation["Team has been changed, press ok to reload!"]);
      if (r == true) {
        document.location.reload(true);
        return false;
      } else {
        return false;
      }
    }
  });

});

function clickToSetCurrentTeamId() {
  if (typeof(Storage) !== "undefined") {
    localStorage.team_id_current = Number(cake.data.team_id);
  } else {
    console.log("Sorry, your browser does not support web storage...");
  }
};


$(document).ready(function () {
  //intercomのリンクを非表示にする
  if (enabled_intercom_icon) {
    $('#IntercomLink').hide();
  }
  // Androidアプリかiosアプリの場合のみfastClickを実行する。
  // 　→iosでsafari/chromeでfastClick使用時、チェックボックス操作に不具合が見つかったため。
  if (cake.is_mb_app === 'true' || cake.is_mb_app_ios === 'true') {
    fastClick();
  }

  //Monitoring of the communication state of App Server | Appサーバーの通信状態の監視
  window.addEventListener("online", function () {
    updateNotifyCnt();
    updateMessageNotifyCnt();
    network_reachable = true;
  }, false);

  window.addEventListener("offline", function () {
    network_reachable = false;
  }, false);


  $(document).on('keyup', '#message_text_input', function () {
    $(this).autosize();
    //$('body').animate({
    //    scrollTop: $(document).height()
    //});
  });

  $(document).on('click', '#mark_all_read,#mark_all_read_txt', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'GET',
      url: cake.url.an,
      async: true,
      success: function () {
        $(".notify-card-list").removeClass('notify-card-unread').addClass('notify-card-read');
      }
    });
    return false;
  });

  $("a.youtube").YouTubeModal({autoplay: 0, width: 640, height: 360});


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
      // HACK:reactの進捗グラフをリサイズするため架空要素(表示はしない)のクリックイベントを使用
      $('.js-flush-chart').trigger('click');
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
  // アップロードしたカバー画像選択時にリサイズして表示
  $('.fileinput_cover').fileinput().on('change.bs.fileinput', function () {
    var $input = $(this).find('input[type=file]');
    if (!$input.prop('files') || $input.prop('files').length == 0) {
      return;
    }
    var file = $input.prop('files')[0];
    var $preview = $(this).find('.fileinput-preview');
    resizeImgBase64(file.result, 672, 378,
      function (img_b64) {
        $preview.removeClass('mod-no-image');
        $preview.css('line-height', '');
        $preview.html('<img class="profile-setting-cover-image" src="' + img_b64 + '">')
      }
    );
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
  $(document).on("keyup", ".blank-disable-and-undisable", evBlankDisableAndUndisable);
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
  $(document).on("change", ".change-select-target-hidden", evSelectOptionTargetHidden);
  //noinspection JSUnresolvedVariable
  $(document).on("click", ".check-target-toggle", evToggle);
  $(document).on("click", ".target-toggle", evTargetToggle);
  //noinspection JSUnresolvedVariable,JSUnresolvedFunction
  $(document).on("click", ".click-show-post-modal", getModalPostList);
  //noinspection JSUnresolvedVariable
  $(document).on("click", ".toggle-follow", evFollowGoal);
  $(document).on("click", ".click-get-ajax-form-replace", getAjaxFormReplaceElm);
  $(document).on("click", ".notify-click-target", evNotifyPost);
  $(document).on("click", ".btn-back-notifications", evNotifications);
  $(document).on("click", ".call-notifications", evNotifications);
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
  $(document).on("click", '.modal-ajax-get-share-circles-users', function (e) {
    e.preventDefault();
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    var url = $(this).data('url');
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
  $(document).on("click", '.modal-ajax-get-exchange-tkr', getModalFormFromUrl);
  $(document).on("click", '.modal-ajax-get-exchange-leader', getModalFormFromUrl);
  //noinspection JSUnresolvedVariable
  $(document).on("click", '.modal-ajax-get-add-key-result', getModalFormFromUrl);
  $('.ModalActionResult_input_field').on('change', function () {
    $('#AddActionResultForm').bootstrapValidator('revalidateField', 'photo');
  });

  $(document).on("click", '.modal-ajax-get-circle-edit', function (e) {
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

          fields: {
            "data[Circle][photo]": {

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
        $this.removeClass('double_click');
        $('body').addClass('modal-open');
      });
    }
  });

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

  //lazy load
  $(document).on("click", '.target-toggle-click', function (e) {
    e.preventDefault();
    imageLazyOn();
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
  //team term setting
  $(document).on("change", '#TeamStartTermMonth , #TeamBorderMonths , #TeamTimezone', function () {
    var startTermMonth = $('#TeamStartTermMonth').val();
    var borderMonths = $('#TeamBorderMonths').val();
    var timezone = $('#TeamTimezone').val();
    if (startTermMonth === "" || borderMonths === "") {
      $('#CurrentTermStr').empty();
      return false;
    }
    var url = cake.url.h + "/" + startTermMonth + "/" + borderMonths + "/" + timezone;
    $.get(url, function (data) {
      $('#CurrentTermStr').text(data.start + "  -  " + data.end);
    });
  });

  //edit team term setting
  $(document).on("change", '#EditTermChangeFrom1 , #EditTermChangeFrom2 ,#EditTermTimezone , #EditTermStartTerm , #EditTermBorderMonths', function () {

    if ($("#EditTermChangeFrom1:checked").val()) {
      var changeFrom = $('#EditTermChangeFrom1:checked').val();
    }
    else {
      var changeFrom = $('#EditTermChangeFrom2:checked').val();
    }
    var timezone = $('#EditTermTimezone').val();
    var startTermMonth = $('#EditTermStartTerm').val();
    var borderMonths = $('#EditTermBorderMonths').val();
    if (startTermMonth === "" || borderMonths === "") {
      $('#NewCurrentTerm').addClass('none');
      $('#NewCurrentTerm > div > p').empty();
      $('#NewNextTerm').addClass('none');
      $('#NewNextTerm > div > p').empty();
      return false;
    }
    var url = cake.url.r + "/" + startTermMonth + "/" + borderMonths + "/" + changeFrom + "/" + timezone;
    $.get(url, function (data) {
      if (data.current.start_date && data.current.end_date) {
        $('#NewCurrentTerm').removeClass('none');
        var current_timezone = parseFloat(data.current.timezone);
        var current_sign = current_timezone < 0 ? "" : "+";
        $('#NewCurrentTerm > div > p').text(data.current.start_date + "  -  " + data.current.end_date + " (GMT " + current_sign + current_timezone + "h)");
      }
      else {
        $('#NewCurrentTerm').addClass('none');
        $('#NewCurrentTerm > div > p').empty();
      }
      if (data.next.start_date && data.next.end_date) {
        $('#NewNextTerm').removeClass('none');
        var next_timezone = parseFloat(data.next.timezone);
        var next_sign = next_timezone < 0 ? "" : "+";

        $('#NewNextTerm > div > p').text(data.next.start_date + "  -  " + data.next.end_date + " (GMT " + next_sign + next_timezone + "h)");
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

  // 投稿フォーム submit 時
  $(document).on('submit', '#PostDisplayForm', function (e) {
    return checkUploadFileExpire('PostDisplayForm');
  });


  // メッセージフォーム submit 時
  $(document).on('submit', '#MessageDisplayForm', function (e) {
    return checkUploadFileExpire('messageDropArea');
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
        PNotify.removeAll();
        if (res.error) {
          new PNotify({
            type: 'error',
            title: cake.word.error,
            text: res.msg,
            icon: "fa fa-check-circle",
            delay: 4000,
            mouse_reset: false
          });
          return;
        }
        else {
          var $list_items = $('#RecoveryCodeList').find('li');
          for (var i = 0; i < 10; i++) {
            $list_items.eq(i).text(res.codes[i].slice(0, 4) + ' ' + res.codes[i].slice(-4));
          }
          new PNotify({
            type: 'success',
            title: cake.word.success,
            text: res.msg,
            icon: "fa fa-check-circle",
            delay: 4000,
            mouse_reset: false
          });
        }


      })
      .fail(function () {
        PNotify.removeAll();
        new PNotify({
          type: 'error',
          title: cake.word.error,
          text: cake.message.notice.d,
          icon: "fa fa-check-circle",
          delay: 4000,
          mouse_reset: false
        });
      });
  });


  //noinspection JSJQueryEfficiency

  // サークル編集画面のタブ切り替え
  // タブによって footer 部分を切り替える
  $(document).on('shown.bs.tab', '.modal-dialog.edit-circle a[data-toggle="tab"]', function (e) {
    var $target = $(e.target);
    var tabId = $target.attr('href').replace('#', '');
    $target.closest('.modal-dialog').find('.modal-footer').hide().filter('.' + tabId + '-footer').show();
  });

  if (cake.data.j == "0") {
    $('#FeedMoreReadLink').trigger('click');
  }

  if (typeof cake.request_params.named.after_click !== 'undefined') {
    $("#" + cake.request_params.named.after_click).trigger('click');
  }
  if (typeof cake.request_params.after_click !== 'undefined') {
    $("#" + cake.request_params.after_click).trigger('click');
  }

  $(document).on('lightbox.open', 'a[rel^=lightbox]', function () {
    var $viewport = $("meta[name='viewport']");
    $viewport.attr('content', $viewport.attr('content')
      .replace('user-scalable=no', 'user-scalable=yes')
      .replace('maximum-scale=1', 'maximum-scale=10'));

  });
  $(document).on('lightbox.close', 'a[rel^=lightbox]', function () {
    var $viewport = $("meta[name='viewport']");
    $viewport.attr('content', $viewport.attr('content')
      .replace('user-scalable=yes', 'user-scalable=no')
      .replace('maximum-scale=10', 'maximum-scale=1'));
  });

  ///////////////////////////////////////////////////////////////////////////
  // Ctrl(Command) + Enter 押下時のコールバック
  ///////////////////////////////////////////////////////////////////////////


  // 投稿フォーム
  bindCtrlEnterAction('#PostDisplayForm', function (e) {
    $('#PostSubmit').trigger('click');
  });

  // メッセージフォーム
  bindCtrlEnterAction('#MessageDisplayForm', function (e) {
    $('#MessageSubmit').trigger('click');
  });

  // メッセージ個別ページ
  bindCtrlEnterAction('#message_text_input', function (e) {
    $('#message_submit_button').trigger('click');
  });

  // コメント
  bindCtrlEnterAction('.comment-form', function (e) {
    $(this).find('.comment-submit-button').trigger('click');
  });
});
function imageLazyOn($elm_obj) {
  var lazy_option = {
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
  };
  if ($elm_obj === undefined) {
    return $("img.lazy").lazy(lazy_option);
  }
  else {
    return $elm_obj.find("img.lazy").lazy(lazy_option);
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
 * base64の画像をリサイズ
 */
function resizeImgBase64(imgBase64, width, height, callback) {
  // Image Type
  var img_type = imgBase64.substring(5, imgBase64.indexOf(";"));
  // Source Image
  var img = new Image();
  img.onload = function () {
    // New Canvas
    var canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    // Draw (Resize)
    var ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);
    // Destination Image
    var imgB64_dst = canvas.toDataURL(img_type);
    callback(imgB64_dst);
  };
  img.src = imgBase64;
}

/**
 *  仮アップロードされたファイルの有効期限（保存期限） が過ぎていないか確認
 *
 * @param formID
 * @returns {boolean}
 */
function checkUploadFileExpire(formID) {
  var $form = $('#' + formID);

  var res = true;
  $form.find('input[type=hidden][name="data[file_id][]"]').each(function () {
    var $hidden = $(this);
    var now = Math.floor(new Date().getTime() / 1000);

    // ファイルの有効期限が切れている場合（30 秒余裕を持たす）
    if (now - parseInt($hidden.attr('data-uploaded'), 10) > cake.pre_file_ttl - 30) {
      var $uploadFileForm = $(document).data('uploadFileForm');

      // Dropzone の管理ファイルから外す
      var removed_file;
      for (var i = 0; i < $uploadFileForm._files[formID].length; i++) {
        if ($hidden.val() == $uploadFileForm._files[formID][i].file_id) {
          removed_file = $uploadFileForm._files[formID].splice(i, 1)[0];
          break;
        }
      }
      // hidden を削除
      $hidden.remove();
      // プレビューエリアを非表示にする
      $(removed_file.previewElement).fadeOut();

      res = false;
    }
  });
  if (!res) {
    new PNotify({
      type: 'error',
      title: cake.word.error,
      text: cake.message.validate.dropzone_uploaded_file_expired,
      icon: "fa fa-check-circle",
      delay: 6000,
      mouse_reset: false
    });
  }
  return res;
}

function getAjaxFormReplaceElm() {
  attrUndefinedCheck(this, 'replace-elm-parent-id');
  attrUndefinedCheck(this, 'click-target-id');
  attrUndefinedCheck(this, 'tmp-target-height');
  attrUndefinedCheck(this, 'ajax-url');
  var $obj = $(this);
  // 非表示状態の時は何もしない
  if (!$obj.is(':visible')) {
    return;
  }
  var replace_elm_parent_id = sanitize($obj.attr("replace-elm-parent-id"));
  var replace_elm = $('#' + replace_elm_parent_id);
  var click_target_id = sanitize($obj.attr("click-target-id"));
  var ajax_url = $obj.attr("ajax-url");
  var tmp_target_height = sanitize($obj.attr("tmp-target-height"));
  var post_id = sanitize($obj.attr("post-id"));
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
          // アップロードファイルの有効期限が切れていなければコメント投稿
          var res = checkUploadFileExpire($(this).attr('id'));
          if (res) {
            validatorCallback(e)
          }
          return res;
        });
        $('#' + click_target_id).trigger('click').focus();

        var $uploadFileForm = $(document).data('uploadFileForm');

        // コメントフォームをドラッグ＆ドロップ対象エリアにする
        var commentParams = {
          formID: function () {
            return $(this).attr('data-form-id');
          },
          previewContainerID: function () {
            return $(this).attr('data-preview-container-id');
          },
          beforeSending: function () {
            if ($uploadFileForm._sending) {
              return;
            }
            $uploadFileForm._sending = true;
            // ファイルの送信中はsubmitできないようにする(クリックはできるがsubmit処理は走らない)
            $('#CommentSubmit_' + post_id).on('click', $uploadFileForm._forbitSubmit);
          },
          afterQueueComplete: function () {
            $uploadFileForm._sending = false;
            // フォームをsubmit可能にする
            $('#CommentSubmit_' + post_id).off('click', $uploadFileForm._forbitSubmit);
          },
          afterError: function (file) {
            var $preview = $(file.previewTemplate);
            // エラーと確認出来るように失敗したファイルの名前を強調して少しの間表示しておく
            $preview.find('.dz-name').addClass('font_darkRed font_bold').append('(' + cake.word.error + ')');
            setTimeout(function () {
              $preview.remove();
            }, 4000);
          }
        };
        $uploadFileForm.registerDragDropArea('#CommentBlock_' + post_id, commentParams);
        $uploadFileForm.registerAttachFileButton('#CommentUploadFileButton_' + post_id, commentParams);

        // OGP 情報を取得してプレビューする処理
        require(['ogp'], function (ogp) {
          var onKeyUp = function () {
            ogp.getOGPSiteInfo({
              // URL が含まれるテキスト
              text: $('#CommentFormBody_' + post_id).val(),

              // ogp 情報を取得する必要があるかチェック
              readyLoading: function () {
                // 既に OGP 情報を取得している場合は終了
                if ($('#CommentSiteInfoUrl_' + post_id).val()) {
                  return false;
                }
                return true;
              },

              // ogp 情報取得成功時
              success: function (data) {
                var $siteInfoUrl = $('#CommentSiteInfoUrl_' + post_id);
                var $siteInfo = $('#CommentOgpSiteInfo_' + post_id);
                $siteInfo
                // プレビュー用 HTML
                  .html(data.html)
                  // プレビュー削除ボタンを重ねて表示
                  .append($('<a>').attr('href', '#')
                    .addClass('font_lightgray')
                    .css({
                      left: '95%',
                      "margin-top": '20px',
                      position: 'absolute',
                      display: "block",
                      "z-index": '1000'
                    })
                    .append('<i class="fa fa-times"></i>')
                    .on('click', function (e) {
                      e.preventDefault();
                      e.stopPropagation();
                      $siteInfoUrl.val('');
                      $siteInfo.empty();
                    }))
                  // プレビュー削除ボタンの表示スペースを作る
                  .find('.site-info').css({
                  "padding-right": "30px"
                });

                // hidden に URL 追加
                $siteInfoUrl.val(data.url);
              },

              // ogp 情報 取得失敗時
              error: function () {
                // loading アイコン削除
                $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
              },

              // ogp 情報 取得開始時
              loadingStart: function () {
                // loading アイコン表示
                $('<i class="fa fa-refresh fa-spin"></i>')
                  .attr('id', 'CommentSiteInfoLoadingIcon_' + post_id)
                  .addClass('mr_8px lh_20px')
                  .insertBefore('#CommentSubmit_' + post_id);
              },

              // ogp 情報 取得完了時
              loadingEnd: function () {
                // loading アイコン削除
                $('#CommentSiteInfoLoadingIcon_' + post_id).remove();
              }
            });
          };
          var timer = null;
          $('#CommentFormBody_' + post_id).on('keyup', function () {
            clearTimeout(timer);
            timer = setTimeout(onKeyUp, 800);
          });
        });
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

  // アップロードファイルの上限数をリセット
  if (typeof Dropzone.instances[0] !== "undefined" && Dropzone.instances[0].files.length > 0) {
    // ajax で submit するので、アップロード完了後に Dropzone のファイルリストを空にする
    // （参照先の配列を空にするため空配列の代入はしない）
    Dropzone.instances[0].files.length = 0;
  }

  var $f = $(e.target);
  var ajaxProcess = $.Deferred();
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
      if (!data.error) {
        // 通信が成功したときの処理
        evCommentLatestView.call($refresh_link.get(0), {
          afterSuccess: function () {
            $first_form.children().toggle();
            $f.remove();
            ajaxProcess.resolve();
          }
        });
      }
      else {
        $error_msg_box.text(data.msg);
        ajaxProcess.reject();
      }
    })
    .fail(function (data) {
      $error_msg_box.text(cake.message.notice.g);
      ajaxProcess.reject();
    });

  ajaxProcess.always(function () {
    // 通信が完了したとき
    $loader_html.remove();
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

/**
 * target_idの属性に対象となるIDがセットするとブランクの場合にdisabledにする。
 * 再度ブランクではない状態になったらdisabledを削除する。
 * target_idは,区切りで複数の要素を指定可能
 */
function evBlankDisableAndUndisable() {
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
        var scroll_offset = 0;
        subNavbar.stop().animate({"top": scroll_offset}, 400);
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
  $(".hoverPic").hover(
    function () {
      $("img", this).stop().attr("src", $("img", this).attr("src").replace("_off", "_on"));
    },
    function () {
      $("img", this).stop().attr("src", $("img", this).attr("src").replace("_on", "_off"));
    });
});

$(function () {
  $(".js-header-link").hover(
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
      $(".js-header-link , .header-profile-icon,.header-logo-img ,.header-function-link").stop().animate({opacity: ".88"}, 300);//ONマウス時のカラーと速度
    }, function () {
      $(".js-header-link , .header-profile-icon,.header-logo-img,.header-function-link").stop().animate({opacity: ".54"}, 600);//OFFマウス時のカラーと速度
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
  var current_slide_id = 1;

  // インジケータークリック時
  $(document).on('click', '.setup-tutorial-indicator', function () {
    resetDisplayStatus();
    changeTutorialContent($(this).attr('data-id'));
  });

  // ネクストボタンクリック時
  $(document).on('click', '.tutorial-next-btn', function () {
    if (current_slide_id == 3) {
      location.href = "/setup/";
      return;
    }
    resetDisplayStatus();

    var next_id = String(Number(current_slide_id) + 1);
    changeTutorialContent(next_id);
  });

  function changeTutorialContent(content_id) {
    // 各要素をカレントステータスに設定
    $('.tutorial-box' + content_id).show();
    $('.tutorial-text' + content_id).show();
    $('.setup-tutorial-indicator' + content_id).addClass('setup-tutorial-navigation-indicator-selected');

    current_slide_id = content_id;
  }

  function resetDisplayStatus() {
    $('.tutorial-body').children('div').hide();
    $('.setup-tutorial-texts').children('div').hide();
    $('.setup-tutorial-navigation-indicator').children('span').removeClass('setup-tutorial-navigation-indicator-selected');
  }
});

//入力途中での警告表示
//静的ページのにはすべて適用
function setChangeWarningForAllStaticPage() {
  //オートコンプリートでchangeしてしまうのを待つ
  setTimeout(function () {
    var flag = false;
    $(":input").each(function () {
      var default_val = "";
      var changed_val = "";
      default_val = $(this).load().val();
      $(this).on("change keyup keydown", function () {
        if ($(this).hasClass('disable-change-warning')) {
          return;
        }
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
    $(window).on("beforeunload", function () {
      if ($(".changed").length != "" && flag == false) {
        return cake.message.notice.a;
      }
    });
  }, 2000);
}

function warningCloseModal() {
  warningAction($('.modal'));
}

function warningAction($obj) {
  var flag = false;
  $obj.on('shown.bs.modal', function (e) {
    setTimeout(function () {
      $obj.find(":input").each(function () {
        var default_val = "";
        var changed_val = "";
        default_val = $(this).load().val();
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
  warningAction($modal_elm);
  $modal_elm.on('shown.bs.modal', function (e) {
    $(this).find('textarea').each(function () {
      $(this).autosize();
    });
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

  $('[rel="tooltip"]').tooltip();

  $('.validate').bootstrapValidator({
    live: 'enabled',
    fields: {
      "data[User][password]": {
        validators: {
          stringLength: {
            min: 8,
            message: cake.message.validate.a
          },
          regexp: {
            regexp: /^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/,
            message: cake.message.validate.e
          }
        }
      },
      "data[User][password_confirm]": {
        validators: {
          stringLength: {
            min: 8,
            message: cake.message.validate.a
          },
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

    fields: {}
  });
  $('#MessageDisplayForm').bootstrapValidator({
    live: 'enabled',

    fields: {}
  });
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

  initMemberSelect2();
  initCircleSelect2();

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
                PNotify.removeAll();
                new PNotify({
                  type: 'success',
                  title: cake.word.success,
                  text: res.msg,
                  icon: "fa fa-check-circle",
                  delay: 4000,
                  mouse_reset: false
                });
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
                PNotify.removeAll();
                new PNotify({
                  type: 'error',
                  title: cake.word.error,
                  text: cake.message.notice.d,
                  icon: "fa fa-check-circle",
                  delay: 4000,
                  mouse_reset: false
                });
              });
          });
      }).success(function () {
        $('body').addClass('modal-open');
        $this.removeClass('double_click');
      });
    }
  });

  $(document).on("click", '#CircleFilterMenuDropDown .modal-circle-setting', function (e) {
    e.preventDefault();
    var $this = $(this);
    if ($this.hasClass('double_click')) {
      return false;
    }
    $this.addClass('double_click');
    var $modal_elm = $('<div class="modal on fade" tabindex="-1"></div>');
    $modal_elm.on('hidden.bs.modal', function () {
      $(this).remove();
    });
    var url = $(this).attr('href');
    $.get(url, function (data) {
      $modal_elm.append(data);
      $modal_elm.modal();
      $modal_elm.find(".bt-switch").bootstrapSwitch({
        size: "small"
      })
      // スイッチ切り替えた時、即時データを更新する
        .on('switchChange.bootstrapSwitch', function () {
          var $form = $('#CircleSettingForm');
          $.ajax({
            url: cake.url.circle_setting,
            type: 'POST',
            dataType: 'json',
            processData: false,
            data: $form.serialize()
          })
            .done(function (res) {
              PNotify.removeAll();
              if (res.error) {
                new PNotify({
                  type: 'error',
                  title: cake.word.error,
                  text: res.msg,
                  icon: "fa fa-check-circle",
                  delay: 4000,
                  mouse_reset: false
                });
              }
              else {
                new PNotify({
                  type: 'success',
                  title: cake.word.success,
                  text: res.msg,
                  icon: "fa fa-check-circle",
                  delay: 4000,
                  mouse_reset: false
                });
              }
            })
            .fail(function () {
              PNotify.removeAll();
              new PNotify({
                type: 'error',
                title: cake.word.error,
                text: cake.message.notice.d,
                icon: "fa fa-check-circle",
                delay: 4000,
                mouse_reset: false
              });
            });
        });
    }).success(function () {
      $('body').addClass('modal-open');
      $this.removeClass('double_click');
    });
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

  // 投稿フォームが表示されるページのみ
  if ($('#CommonPostBody').size()) {
    require(['ogp'], function (ogp) {
      // 投稿編集の場合で、OGPのurlが登録されている場合
      if ($('.post-edit').size()) {
        if ($('.post-edit').attr('data-default-ogp-url')) {
          getPostOGPInfo(ogp, $('.post-edit').attr('data-default-ogp-url'));
        }
      }

      var onKeyUp = function () {
        getPostOGPInfo(ogp, $('#CommonPostBody').val());
      };
      var timer = null;
      $('#CommonPostBody').on('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(onKeyUp, 800);
      });
    });
  }
});

function initMessageSelect2(topic_id) {
  //noinspection JSUnusedLocalSymbols post_detail.Post.id
  $('#selectOnlyMember').select2({
    multiple: true,
    minimumInputLength: 1,
    placeholder: cake.message.notice.b,
    ajax: {
      url: cake.url.add_member_on_message,
      dataType: 'json',
      quietMillis: 100,
      cache: true,
      data: function (term, page) {
        return {
          term: term, //search term
          page_limit: 10, // page size
          topic_id: topic_id
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
  }).on('change', function () {
    var $this = $(this);
    if ($this.val() == '') {
      $('#MessageSubmit').attr('disabled', 'disabled');
    }
    else {
      if ($('#CommonMessageBody').val() != '') {
        $('#MessageSubmit').removeAttr('disabled');
      }
    }
    // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
    $this.select2('data', select2ExpandGroup($this.select2('data')));
  });
}


function initMemberSelect2() {
  //noinspection JSUnusedLocalSymbols
  $('#select2Member').select2({
    initSelection: function (element, callback) {
      // user_**の文字列からユーザーIDを抽出
      if ($(element).val().match(/^user_(\d+)$/)) {
        var userId = RegExp.$1;
        // ユーザー情報を取得して初期表示
        $.ajax("/users/ajax_select2_get_user_detail/" + userId,
          {
            type: 'GET'
          }).done(function (data) {
          callback(data);
        });
      }
    },
    multiple: true,
    minimumInputLength: 1,
    placeholder: cake.message.notice.b,
    ajax: {
      url: cake.url.a,
      dataType: 'json',
      quietMillis: 100,
      cache: true,
      data: function (term, page) {
        return {
          term: term, //search term
          page_limit: 10, // page size
          with_group: 1
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
  }).on('change', function () {
    var $this = $(this);
    if ($this.val() == '' || $('#CommonMessageBody').val() == '') {
      $('#MessageSubmit').attr('disabled', 'disabled');
    }
    else {
      $('#MessageSubmit').removeAttr('disabled');
    }
    // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
    $this.select2('data', select2ExpandGroup($this.select2('data')));
  });
}

function initCircleSelect2() {
  //noinspection JSUnusedLocalSymbols,JSDuplicatedDeclaration
  $('#select2PostCircleMember').select2({
    multiple: true,
    placeholder: cake.word.select_public_circle,
    minimumInputLength: 1,
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
  })
    .on('change', function () {
      var $this = $(this);
      // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
      $this.select2('data', select2ExpandGroup($this.select2('data')));
    });

  // select2 秘密サークル選択
  $('#select2PostSecretCircle').select2({
    multiple: true,
    placeholder: cake.word.select_secret_circle,
    minimumInputLength: 1,
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

  // サークル追加用モーダルの select2 を設定
  bindSelect2Members($('#modal_add_circle'));

  // 投稿の共有範囲(公開/秘密)切り替えボタン
  var $shareRangeToggleButton = $('#postShareRangeToggleButton');
  var $shareRange = $('#postShareRange');
  var publicButtonLabel = '<i class="fa fa-unlock"></i> ' + cake.word.public;
  var secretButtonLabel = '<i class="fa fa-lock font_verydark"></i> ' + cake.word.secret;

  // ボタン初期状態
  $shareRangeToggleButton.html(($shareRange.val() == 'public') ? publicButtonLabel : secretButtonLabel);

  // 共有範囲切り替えボタンが有効な場合
  $shareRangeToggleButton.on('click', function (e) {
    e.preventDefault();
    if ($shareRangeToggleButton.attr('data-toggle-enabled')) {
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
    }
    else {
      // 共有範囲切り替えボタンが無効な場合（サークルフィードページ）
      $shareRangeToggleButton.popover({
        'data-toggle': "popover",
        'placement': 'top',
        'trigger': "focus",
        'content': cake.word.share_change_disabled,
        'container': 'body'
      });
    }
  });


  $('#select2ActionCircleMember').select2({
    multiple: true,
    placeholder: cake.word.select_notify_range,
    minimumInputLength: 1,
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

}

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
    minimumInputLength: 1,
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
    formatSelection: format,
    formatResult: format,
    escapeMarkup: function (m) {
      return m;
    }
  })
    .on('change', function () {
      var $this = $(this);
      // グループを選択した場合
      // グループに所属するユーザーを展開して入力済にする
      $this.select2('data', select2ExpandGroup($this.select2('data')));
    });
}

// select2 で選択されたグループをユーザーとして展開する
function select2ExpandGroup(data) {
  for (var i = 0; i < data.length; i++) {
    if (data[i].id.indexOf('group_') === 0 && data[i].users) {
      var group = data.splice(i, 1)[0];
      for (var j = 0; j < group.users.length; j++) {
        data.push(group.users[j]);
      }
    }
  }
  return data;
};

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
    formatInputTooShort: function () {
      return cake.word.e;
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


var action_autoload_more = false;
var autoload_more = false;
var feed_loading_now = false;
var do_reload_header_bellList = false;
function evFeedMoreView(options) {
  var opt = $.extend({
    recursive: false,
    loader_id: null
  }, options);

  //フィード読み込み中はキャンセル
  if (feed_loading_now) {
    return false;
  }
  feed_loading_now = true;

  attrUndefinedCheck(this, 'parent-id');
  attrUndefinedCheck(this, 'next-page-num');
  attrUndefinedCheck(this, 'get-url');

  var $obj = $(this);
  var parent_id = sanitize($obj.attr('parent-id'));
  var next_page_num = sanitize($obj.attr('next-page-num'));
  var get_url = $obj.attr('get-url');
  var month_index = sanitize($obj.attr('month-index'));
  var no_data_text_id = sanitize($obj.attr('no-data-text-id'));
  var oldest_post_time = sanitize($obj.attr('oldest-post-time')) || 0;
  var append_target_id = sanitize($obj.attr('append-target-id'));
  // この時間より前の投稿のみ読み込む
  var post_time_before = sanitize($obj.attr('post-time-before')) || 0;

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
        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();
        if (append_target_id != undefined) {
          $("#" + append_target_id).append($posts);
        }
        else {
          $("#" + parent_id).before($posts);
        }
        showMore($posts);
        $posts.fadeIn();

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
        $posts.imagesLoaded(function () {
          $posts.find('.post_gallery').each(function (index, element) {
            bindPostBalancedGallery($(element));
          });
          $posts.find('.comment_gallery').each(function (index, element) {
            bindCommentBalancedGallery($(element));
          });
          changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
        });
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
                feed_loading_now = false;
                evFeedMoreView.call($obj[0], {recursive: true, loader_id: '__feed_loader'});
              }, 200);
              return;
            }
            // これ以上古い投稿が存在しない場合
            else {
              $loader_html.remove();
              $("#" + no_data_text_id).show();
              $('#' + parent_id).find('.panel-read-more-body').removeClass('panel-read-more-body').addClass('panel-read-more-body-no-data');
              $obj.css("display", "none");
              feed_loading_now = false;
              return;
            }
          }
        }
        // 前月以前のデータを取得する必要がない場合
        else {
          //ローダーを削除
          $loader_html.remove();
          $("#" + no_data_text_id).show();
          $('#' + parent_id).find('.panel-read-more-body').removeClass('panel-read-more-body').addClass('panel-read-more-body-no-data');
          //もっと読む表示をやめる
          $obj.css("display", "none");
        }
      }
      action_autoload_more = false;
      autoload_more = false;
      feed_loading_now = false;
    },
    error: function () {
      feed_loading_now = false;
    },
  });
  return false;
}

//アドレスバー書き換え
function updateAddressBar(url) {
  if (typeof history.pushState == 'function') {
    try {
      history.pushState(null, null, url);
      return true;
    } catch (e) {
      window.location.href = url;
      return false;
    }
  }
}

function evMessageList(options) {
  //とりあえずドロップダウンは隠す
  $(".has-notify-dropdown").removeClass("open");
  $('body').removeClass('notify-dropdown-open');

  var url = cake.url.message_list;
  location.href = url;
  return false;
}


function evNotifications(options) {

  //とりあえずドロップダウンは隠す
  $(".has-notify-dropdown").removeClass("open");
  $('body').removeClass('notify-dropdown-open');

  var opt = $.extend({
    recursive: false,
    loader_id: null
  }, options);

  //フィード読み込み中はキャンセル
  if (feed_loading_now) {
    return false;
  }
  feed_loading_now = true;

  attrUndefinedCheck(this, 'get-url');

  var $obj = $(this);
  var get_url = $obj.attr('get-url');

  //layout-mainが存在しないところではajaxでコンテンツ更新しようにもロードしていない
  //要素が多すぎるので、おとなしくページリロードする
  if (!$(".layout-main").exists()) {
    window.location.href = get_url;
    return false;
  }

  //アドレスバー書き換え
  if (!updateAddressBar(get_url)) {
    return false;
  }

  $('#jsGoTop').click();

  //ローダー表示
  var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>');
  if (!opt.recursive) {
    $(".layout-main").html($loader_html);
  }

  // URL生成
  var url = cake.url.notifications;

  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    dataType: 'json',
    success: function (data) {
      if (!$.isEmptyObject(data.html)) {
        //取得したhtmlをオブジェクト化
        var $posts = $(data.html);
        //notify一覧に戻るhtmlを追加
        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();

        $(".layout-main").html($posts);
      }

      //ローダーを削除
      $loader_html.remove();

      action_autoload_more = false;
      autoload_more = false;
      feed_loading_now = false;
      do_reload_header_bellList = true;
    },
    error: function () {
      feed_loading_now = false;
      $loader_html.remove();
    },
  });
  return false;
}

// 通知から投稿、メッセージに移動
// TODO: メッセージ通知リンクと投稿通知リンクのイベントを分けるか、このメソッドを汎用的に使えるようにする。
//       そうしないとメッセージ詳細へのリンクをajax化する際に、ここのロジックが相当複雑になってしまう予感がする。
function evNotifyPost(options) {

  //とりあえずドロップダウンは隠す
  $(".has-notify-dropdown").removeClass("open");
  $('body').removeClass('notify-dropdown-open');

  var opt = $.extend({
    recursive: false,
    loader_id: null
  }, options);

  //フィード読み込み中はキャンセル
  if (feed_loading_now) {
    return false;
  }
  feed_loading_now = true;

  attrUndefinedCheck(this, 'get-url');

  var $obj = $(this);
  var get_url = $obj.attr('get-url');

  //layout-mainが存在しないところではajaxでコンテンツ更新しようにもロードしていない
  //要素が多すぎるので、おとなしくページリロードする
  //urlにpost_permanentを含まない場合も対象外
  if (!$(".layout-main").exists() || !get_url.match(/post_permanent/)) {
    // 現状、メッセージページに遷移する場合はこのブロックを通る
    feed_loading_now = false;
    window.location.href = get_url;
    return false;
  }

  //アドレスバー書き換え
  if (!updateAddressBar(get_url)) {
    return false;
  }

  $('#jsGoTop').click();

  //ローダー表示
  var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>');
  if (!opt.recursive) {
    $(".layout-main").html($loader_html);
  }

  // URL生成
  var url = get_url.replace(/post_permanent/, "ajax_post_permanent");

  var button_notifylist = '<a href="#" get-url="/notifications" class="btn-back btn-back-notifications"> <i class="fa fa-chevron-left font_18px font_lightgray lh_20px"></i> </a> ';

  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    dataType: 'json',
    success: function (data) {
      if (!$.isEmptyObject(data.html)) {
        //取得したhtmlをオブジェクト化
        var $posts = $(data.html);
        //notify一覧に戻るhtmlを追加
        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();

        $(".layout-main").html(button_notifylist);
        $(".layout-main").append($posts);
        $(".layout-main").append(button_notifylist);

        showMore($posts);
        $posts.fadeIn();

        //リンクを有効化
        $obj.removeAttr('disabled');
        $("#ShowMoreNoData").hide();
        $posts.imagesLoaded(function () {
          $posts.find('.post_gallery').each(function (index, element) {
            bindPostBalancedGallery($(element));
          });
          $posts.find('.comment_gallery').each(function (index, element) {
            bindCommentBalancedGallery($(element));
          });
          changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
        });
      }

      //ローダーを削除
      $loader_html.remove();

      // Google tag manager トラッキング
      if (cake.data.google_tag_manager_id !== "") {
        sendToGoogleTagManager('app');
      }

      action_autoload_more = false;
      autoload_more = false;
      feed_loading_now = false;
      do_reload_header_bellList = true;
    },
    error: function () {
      feed_loading_now = false;
      $loader_html.remove();
    },
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
        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();
        $("#" + parent_id).before($posts);
        showMore($posts);
        $posts.fadeIn();
        //ローダーを削除
        $loader_html.remove();
        //リンクを削除
        $obj.css("display", "none").css("opacity", 0);
        $posts.imagesLoaded(function () {
          $posts.find('.comment_gallery').each(function (index, element) {
            bindCommentBalancedGallery($(element));
          });
          changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
        });

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
  var $like_count_text = $("#" + like_count_id);

  var like_type = $obj.attr('like_type');
  var url = null;
  var model_id = $obj.attr('model_id');
  $obj.toggleClass("liked");

  // ajax の結果を待たずに表示されているいいね数を変更する
  // ajax の結果が返ってきたら正しい数字で上書きされる
  var currentCount = parseInt($like_count_text.text(), 10);
  if ($obj.hasClass("liked")) {
    $like_count_text.text(currentCount + 1);
  }
  else {
    $like_count_text.text(currentCount - 1);
  }

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
        $like_count_text.text(data.count);
      }
    },
    error: function () {
      return false;
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
  var showText = '<i class="fa fa-angle-double-down mr_5px"></i>' + cake.message.info.e;
  var hideText = '<i class="fa fa-angle-double-up mr_5px"></i>' + cake.message.info.h;
  if (obj) {
    $(obj).find('.showmore').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '128px',
      showText: showText,
      hideText: hideText
    });
    $(obj).find('.showmore-comment').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '105px',
      showText: showText,
      hideText: hideText
    });
    $(obj).find('.showmore-circle').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '900px',
      showText: showText,
      hideText: hideText
    });
    $(obj).find('.showmore-comment-circle').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '920px',
      showText: showText,
      hideText: hideText
    });
    $(obj).find('.showmore-init-none').showMore({
      speedDown: 100,
      speedUp: 100,
      height: '0px',
      showText: showText,
      hideText: hideText
    });
    $(obj).find('.showmore-action').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '42px',
      showText: showText,
      hideText: hideText
    });

  }
  else {
    $('.showmore').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '128px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-circle').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '900px',
      showText: showText,
      hideText: hideText
    });

    $('.showmore-comment').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '105px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-comment-circle').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '920px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-mini').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '60px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-xtra-mini').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '40px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-profile-content').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '80px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-init-none').showMore({
      speedDown: 100,
      speedUp: 100,
      height: '0px',
      showText: showText,
      hideText: hideText
    });
    $('.showmore-action').showMore({
      speedDown: 300,
      speedUp: 300,
      height: '42px',
      showText: showText,
      hideText: hideText
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
    $(this).prop("disabled", true);
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
    pusher.subscribe(cake.data.c[i]).bind('bell_count', function (data) {
      //通知設定がoffもしくは自分自身が送信者の場合はなにもしない。
      if (!cake.notify_setting[data.flag_name]) {
        return;
      }
      if (cake.data.user_id == data.from_user_id) {
        return;
      }
      setNotifyCntToBellAndTitle(getCurrentUnreadNotifyCnt() + 1);
    });
  }
  pusher.subscribe('user_' + cake.data.user_id + '_team_' + cake.data.team_id).bind('msg_count', function (data) {

    //通知設定がoffもしくは自分自身が送信者の場合はなにもしない。
    if (!cake.notify_setting[data.flag_name]) {
      return;
    }

    // if display the topic page, nothing to do
    const topic_page_url = "/topics/" + data.topic_id + "/detail";
    if (location.pathname.indexOf(topic_page_url) !== -1) {
      return;
    }

    if (cake.data.user_id == data.from_user_id) {
      return;
    }
    if (cake.unread_msg_topic_ids.indexOf(data.topic_id) >= 0) {
      return;
    }
    cake.unread_msg_topic_ids.push(data.topic_id);
    setNotifyCntToMessageAndTitle(getMessageNotifyCnt() + 1);
  });

});

function getCurrentUnreadNotifyCnt() {
  var $bellNum = $("#bellNum");
  var $numArea = $bellNum.find("span");
  return parseInt($numArea.html());
}

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
  $(document).on("click", ".click-comment-delete", evCommentDelete);
  $(document).on("click", ".click-comment-confirm-delete", evCommentDeleteConfirm);
  $(document).on("click", '[id*="CommentEditSubmit_"]', evCommendEditSubmit);
});

function evCommendEditSubmit(e) {
  e.preventDefault();
  var $form = $(this).parents('form');
  var formUrl = $form.attr('action');
  var commentId = formUrl.split(':')[1];

  var token = $form.find('[name="data[_Token][key]"]').val();
  var body = $form.find('[name="data[Comment][body]"]').val();

  var data = {
    "data[_Token][key]": token,
    Comment: {
      body: body
    }
  };

  $.ajax({
    type: 'PUT',
    url: "/api/v1/comments/" + commentId,
    cache: false,
    dataType: 'json',
    data: data,
    success: function (data) {
      if (!$.isEmptyObject(data.html)) {
        var $updatedComment = $(data.html);
        // update comment box
        imageLazyOn($updatedComment);
        var $box = $('.comment-box[comment-id="' + commentId + '"]');
        $updatedComment.insertBefore($box);
        $updatedComment.imagesLoaded(function () {
            $updatedComment.find('.comment_gallery').each(function (index, element) {
                bindCommentBalancedGallery($(element));
            });
            changeSizeFeedImageOnlyOne($updatedComment.find('.feed_img_only_one'));
        });
        $box.remove();
      }
      else {
        // Cancel editing
        $('[target-id="CommentEditForm_' + commentId + '"]').click();
      }
    },
    error: function (ev) {
      // Display error message
      new PNotify({
        title: cake.word.error,
        text: cake.message.notice.i,
        type: 'error'
      });
      // Cancel editing
      $('[target-id="CommentEditForm_' + commentId + '"]').click();
    }
  });
  return false;
}

// Display a modal to confirm the deletion of comment
function evCommentDelete(e) {
  e.preventDefault();
  var $delBtn = $(this);
  attrUndefinedCheck($delBtn, 'comment-id');
  var commentId = $delBtn.attr("comment-id");

  // Modal popup
  var modalTemplate =
    '<div class="modal on fade" tabindex="-1">' +
    '  <div class="modal-dialog">' +
    '    <div class="modal-content">' +
    '      <div class="modal-header none-border">' +
    '        <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span class="close-icon">×</span></button>' +
    '        <h5 class="modal-title text-danger">' + __("Delete comment") + '</h5>' +
    '     </div>' +
    '     <div class="modal-body">' +
    '         <h4>' + __("Do you really want to delete this comment?") +'</h4>' +
    '     </div>' +
    '     <div class="modal-footer">' +
    '        <button type="button" class="btn-sm btn-default" data-dismiss="modal" aria-hidden="true">' + cake.word.cancel + '</button>' +
    '        <button type="button" class="btn-sm btn-primary click-comment-confirm-delete" comment-id="' + commentId + '" aria-hidden="true"><img id="loader" src="img/lightbox/loading.gif" style="height: 17px; width:17px; margin: 0 10px; display: none;"  /><span id="message">' + cake.word.delete + '</span></button>' +
    '     </div>' +
    '   </div>' +
    ' </div>' +
    '</div>';

  var $modal_elm = $(modalTemplate);
  $modal_elm.modal();
  return false;
}

// Send the delete request
function evCommentDeleteConfirm() {
  var $delBtn = $(this);
  attrUndefinedCheck($delBtn, 'comment-id');
  var commentId = $delBtn.attr("comment-id");
  var url = "/api/v1/comments/" + commentId;
  var $modal = $delBtn.closest('.modal');
  var $commentBox = $("div.comment-box[comment-id='" + commentId + "']");

  // Show loading spinner and hide button text
  $delBtn.children('#loader').toggle();
  $delBtn.children('#message').toggle();
  $delBtn.attr('disabled', 'disabled');

  $.ajax({
    url: url,
    type: 'DELETE',
    success: function () {
      // Remove modal and comment box
      $modal.modal('hide');
      $commentBox.fadeOut('slow', function(){
          $(this).remove();
      });
    },
    error: function (res) {
      // Display error message
      new PNotify({
          title: cake.word.error,
          text: cake.message.notice.i,
          type: 'error'
      });
      $modal.modal('hide');
    }
  });
  return false;
}

function getCommentBlockLatestId($commentBlock) {
  var commentNum = $commentBlock.children("div.comment-box").length;
  var $lastCommentBox = $commentBlock.children("div.comment-box:last");
  var lastCommentId = "";
  if (commentNum > 0) {
      // コメントが存在する場合
      attrUndefinedCheck($lastCommentBox, 'comment-id');
      lastCommentId = $lastCommentBox.attr("comment-id");
  } else {
      // コメントがまだ0件の場合
      lastCommentId = "";
  }
  return lastCommentId;
}

function evCommentLatestView(options) {
  attrUndefinedCheck(this, 'post-id');
  attrUndefinedCheck(this, 'get-url');

  options = $.extend({
    afterSuccess: function () {
    }
  }, options);

  var $obj = $(this);
  var $commentBlock = $obj.closest(".comment-block");
  var lastCommentId = getCommentBlockLatestId($commentBlock);

  var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
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
    success: function (data) {
      if (!$.isEmptyObject(data.html)) {
        //取得したhtmlをオブジェクト化
        var $posts = $(data.html);

        // Get the comment id for the new post
        var $comment = $posts.closest('[comment-id]').last();
        var newCommentId = $comment.attr("comment-id");

        // Get the last comment id displayed on the page
        $commentBlock = $obj.closest(".comment-block");
        lastCommentId = getCommentBlockLatestId($commentBlock);

        // Do nothing if the new comment is already rendered on the page
        if (newCommentId == lastCommentId) {
          return;
        }

        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();
        $($obj).before($posts);
        showMore($posts);
        $posts.fadeIn();
        //ローダーを削除
        $loader_html.remove();
        //リンクを削除
        $obj.css("display", "none").css("opacity", 0);
        $posts.imagesLoaded(function () {
          $posts.find('.comment_gallery').each(function (index, element) {
            bindCommentBalancedGallery($(element));
          });
          changeSizeFeedImageOnlyOne($posts.find('.feed_img_only_one'));
        });
        $obj.removeAttr("disabled");

        initCommentNotify($obj);

        options.afterSuccess();
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

/**
 * お知らせ一覧のページング処理
 *
 * @param e
 * @param params
 *          locationType: string  (*必須) 呼び出し元を表す文字列 'page' | 'dropdown'
 *          showLoader: function($loading_html)  ローディング画像の表示処理を行うコールバック関数
 *          hideLoader: function($loading_html)  ローディング画像の削除処理を行うコールバック関数
 * @returns {boolean}
 */
function evNotifyMoreView(e, params) {
  attrUndefinedCheck(this, 'get-url');

  var $obj = $(this);
  var oldest_score_id = $("ul.notify-" + params.locationType + "-cards").children("li.notify-card-list:last").attr("data-score");
  var get_url = $obj.attr('get-url');
  //リンクを無効化
  $obj.attr('disabled', 'disabled');
  var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
  //ローダー表示
  if (params.showLoader) {
    params.showLoader.call(this, $loader_html);
  }
  else {
    $obj.after($loader_html);
  }

  //url生成
  var url = get_url + '/' + String(oldest_score_id) + '/' + params.locationType;
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
        $(".notify-" + params.locationType + "-cards").append($notify);
        //html表示
        $notify.show("slow", function () {
          //もっと見る
          showMore(this);
        });
        //ローダーを削除
        if (params.hideLoader) {
          params.hideLoader.call($obj.get(0), $loader_html);
        }
        else {
          $loader_html.remove();
        }
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
  // お知らせ一覧ページの次のページ読込みボタン
  $(document).on("click", ".click-notify-read-more-page", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this);
    evNotifyMoreView.call(this, e, {
      locationType: "page"
    });
  });

  // ヘッダーのお知らせ一覧ポップアップの次のページ読込みボタン
  $(document).on("click", ".click-notify-read-more-dropdown", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this);
    evNotifyMoreView.call(this, e, {
      locationType: "dropdown",
      showLoader: function ($loader_html) {
        $('#bell-dropdown').append($('<div>').append($loader_html).css({
          textAlign: 'center',
        }));

      },
      hideLoader: function ($loader_html) {
        $loader_html.remove();
      }
    });
  });
});

// Auto update notify cnt
$(function () {
  if (cake.data.team_id) {
    setIntervalToGetNotifyCnt(cake.notify_auto_update_sec);
  }

  setNotifyCntToBellAndTitle(cake.new_notify_cnt);
  //メッセージ詳細ページの場合は実行しない。(メッセージ取得後に実行される為)
  if (cake.request_params.controller != 'posts' || cake.request_params.action != 'message') {
    setNotifyCntToMessageAndTitle(cake.new_notify_message_cnt);
  }
});

function setIntervalToGetNotifyCnt(sec) {
  setInterval(function () {
    updateNotifyCnt();
    updateMessageNotifyCnt();
  }, sec * 1000);
}

function updateNotifyCnt() {

  var url = cake.url.f + '/team_id:' + $('#SwitchTeam').val();
  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    success: function (res) {
      if (res.error) {
        location.reload();
        return;
      }
      if (res != 0) {
        setNotifyCntToBellAndTitle(res);
      }
    },
    error: function () {
    }
  });
  return false;
}

function updateMessageNotifyCnt() {

  var url = cake.url.af + '/team_id:' + $('#SwitchTeam').val();
  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    success: function (res) {
      if (res.error) {
        location.reload();
        return;
      }
      setNotifyCntToMessageAndTitle(res);
      if (res != 0) {
      }
    },
    error: function () {
    }
  });
  return false;
}

function setNotifyCntToBellAndTitle(cnt) {
  var $bellBox = getBellBoxSelector();
  var existingBellCnt = parseInt($bellBox.children('span').html());

  if (cnt == 0) {
    return;
  }

  // set notify number
  if (parseInt(cnt) <= 20) {
    $bellBox.children('span').html(cnt);
    $bellBox.children('sup').addClass('none');
  } else {
    $bellBox.children('span').html(20);
    $bellBox.children('sup').removeClass('none');
  }
  updateTitleCount();

  if (existingBellCnt == 0) {
    displaySelectorFluffy($bellBox);
  }
  return;
}

function setNotifyCntToMessageAndTitle(cnt) {
  var cnt = parseInt(cnt);
  var $bellBox = getMessageBoxSelector();
  var existingBellCnt = parseInt($bellBox.children('span').html());

  if (cnt != 0) {
    // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
    $('#click-header-message').next().addClass('dropdown-menu');
  }
  else {
    // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
    $('#click-header-message').next().removeClass('dropdown-menu');
  }

  // set notify number
  if (cnt == 0) {
    $bellBox.children('span').html(cnt);
    $bellBox.children('sup').addClass('none');
  } else if (cnt <= 20) {
    $bellBox.children('span').html(cnt);
    $bellBox.children('sup').addClass('none');
  } else {
    $bellBox.children('span').html(20);
    $bellBox.children('sup').removeClass('none');
  }
  updateTitleCount();

  if (existingBellCnt == 0 && cnt >= 1) {
    displaySelectorFluffy($bellBox);
  } else if (cnt == 0) {
    $bellBox.css("opacity", 0);
  }
  return;
}

// <title> に表示される通知数を更新する
function updateTitleCount() {
  var $title = $("title");
  var current_cnt = getNotifyCnt() + getMessageNotifyCnt();
  var current_str = '';

  if (current_cnt > 20) {
    current_str = '(20+)';
  }
  else if (current_cnt > 0) {
    current_str = '(' + current_cnt + ')';
  }
  $title.text(current_str + $title.attr("origin-title"));
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

$(document).ready(function () {
  var click_cnt = 0;
  $(document).on("click", "#click-header-bell", function () {
    click_cnt++;
    var isExistNewNotify = isExistNewNotify();
    initBellNum();
    initTitle();

    if (isExistNewNotify || click_cnt == 1 || do_reload_header_bellList) {
      updateListBox();
      do_reload_header_bellList = false;
    }

    function isExistNewNotify() {
      var newNotifyCnt = getNotifyCnt();
      if (newNotifyCnt > 0) {
        return true;
      }
      return false;
    }
  });
  $('#HeaderDropdownNotify')
    .on('shown.bs.dropdown', function () {
      $("body").addClass('notify-dropdown-open');
    })
    .on('hidden.bs.dropdown', function () {
      $('body').removeClass('notify-dropdown-open');
    });
});

$(document).ready(function () {
  var click_cnt = 0;
  $(document).on("click", "#click-header-message", function (e) {
    // 未読件数が 0 件の場合は、直接メッセージ一覧ページに遷移させる
    if (getMessageNotifyCnt() == 0) {
      evMessageList(null);
      return;
    }

    initTitle();
    updateMessageListBox();
  });
});

function initBellNum() {
  var $bellBox = getBellBoxSelector();
  $bellBox.css("opacity", 0);
  $bellBox.children('span').html("0");
}
function initMessageNum() {
  var $box = getMessageBoxSelector();
  $box.css("opacity", 0);
  $box.children('span').html("0");
}

function initTitle() {
  var $title = $("title");
  $title.text(sanitize($title.attr("origin-title")));
}

function getBellBoxSelector() {
  return $("#bellNum");
}
function getMessageBoxSelector() {
  return $("#messageNum");
}

function getNotifyCnt() {
  var $bellBox = getBellBoxSelector();
  return parseInt($bellBox.children('span').html(), 10);
}

function getMessageNotifyCnt() {
  var $box = getMessageBoxSelector();
  return parseInt($box.children('span').html(), 10);
}

function updateListBox() {
  var $bellDropdown = $("#bell-dropdown");
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
    }
  });
  return false;
}

// reset bell notify num call from app.
function resetBellNum() {
  initBellNum();
  var url = cake.url.g;
  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    success: function (data) {
      updateNotifyCnt();
    },
    error: function () {
      // do nothing.
    }
  });
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
    }
  });
  return false;
}

// reset bell message num call from app.
function resetMessageNum() {
  initMessageNum();
  var url = cake.url.ag;
  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    success: function (data) {
      // do nothing.
    },
    error: function () {
      // do nothing.
    }
  });
}

function copyToClipboard(url) {
  window.prompt(cake.message.info.copy_url, url);
}

$(document).ready(function () {
  $(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 2000) {
      if (!autoload_more) {
        autoload_more = true;

        if (network_reachable) {
          var $FeedMoreReadLink = $('#FeedMoreReadLink');
          var $GoalPageFollowerMoreLink = $('#GoalPageFollowerMoreLink');
          var $GoalPageMemberMoreLink = $('#GoalPageMemberMoreLink');
          var $GoalPageKeyResultMoreLink = $('#GoalPageKeyResultMoreLink');

          if ($FeedMoreReadLink.is(':visible')) {
            $FeedMoreReadLink.trigger('click');
          }
          if ($GoalPageFollowerMoreLink.is(':visible')) {
            $GoalPageFollowerMoreLink.trigger('click');
          }
          if ($GoalPageMemberMoreLink.is(':visible')) {
            $GoalPageMemberMoreLink.trigger('click');
          }
          if ($GoalPageKeyResultMoreLink.is(':visible')) {
            $GoalPageKeyResultMoreLink.trigger('click');
          }
        } else {
          autoload_more = false;
          return false;
        }
      }
    }
  }).ajaxError(function (event, request, setting) {
    if (request.status == 0) {
      return false;
    }
  });

  // ヘッダーのお知らせ一覧ポップアップのオートローディング
  var prevScrollTop = 0;
  $('#bell-dropdown').scroll(function () {
    var $this = $(this);
    var currentScrollTop = $this.scrollTop();

    if (prevScrollTop < currentScrollTop && ($this.get(0).scrollHeight - currentScrollTop == $this.height())) {
      if (!autoload_more) {
        autoload_more = true;
        $('#NotifyDropDownReadMore').trigger('click');
      }
    }
    prevScrollTop = currentScrollTop;
  });

  // アクションの編集画面の場合は、画像選択の画面をスキップし、
  // ajax で動いている select を選択済みにする
  var $button = $('#ActionForm').find('.post-action-image-add-button.skip');
  if ($button.size()) {
    // 画像選択の画面をスキップ
    evTargetShowThisDelete.call($button.get(0));
    // ゴール選択の ajax 処理を動かす
    $('#GoalSelectOnActionForm').trigger('change');
  }

  // ヘッダーの検索フォームの処理
  require(['search'], function (search) {
    search.headerSearch.setup();
  });

  // Insight 画面の処理
  if ($('#InsightForm').size()) {
    require(['insight'], function (insight) {
      if ($('#InsightResult').size()) {
        insight.insight.setup();
      }
      else if ($('#InsightCircleResult').size()) {
        insight.circle.setup();
      }
      else if ($('#InsightRankingResult').size()) {
        insight.ranking.setup();
      }
      insight.reload();
    });
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
      } else {
        $('#CommonPostBody').blur();
      }
      break;
    case "message":
      $('#CommonFormTabs li:eq(2) a').tab('show');
      if (!isMobile()) {
        $('#s2id_autogen1').focus();
      }
      break;
  }
}

function getPostOGPInfo(ogp, text) {
  var options = {
    // URL が含まれるテキスト
    text: text,

    // ogp 情報を取得する必要があるかチェック
    readyLoading: function () {
      // 既に OGP 情報を取得している場合は終了
      if ($('#PostSiteInfoUrl').val()) {
        return false;
      }
      return true;
    },

    // ogp 情報取得成功時
    success: function (data) {
      appendPostOgpInfo(data);
    },

    // ogp 情報 取得失敗時
    error: function () {
      // loading アイコン削除
      $('#PostSiteInfoLoadingIcon').remove();
    },

    // ogp 情報 取得開始時
    loadingStart: function () {
      // loading アイコン表示
      $('<i class="fa fa-refresh fa-spin"></i>')
        .attr('id', 'PostSiteInfoLoadingIcon')
        .addClass('pull-right lh_20px')
        .insertBefore('#CommonFormTabs');
    },

    // ogp 情報 取得完了時
    loadingEnd: function () {
      // loading アイコン削除
      $('#PostSiteInfoLoadingIcon').remove();
    }
  };

  ogp.getOGPSiteInfo(options);

}

function appendPostOgpInfo(data) {
  var $siteInfoUrl = $('#PostSiteInfoUrl');
  var $siteInfo = $('#PostOgpSiteInfo');
  $siteInfo
  // プレビュー用 HTML
    .html(data.html)
    // プレビュー削除ボタンを重ねて表示
    .append($('<a>').attr('href', '#')
      .addClass('font_lightgray')
      .css({
        left: '91%',
        "margin-top": '15px',
        position: 'absolute',
        display: "block",
        "z-index": '1000'
      })
      .append('<i class="fa fa-times"></i>')
      .on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $siteInfoUrl.val('');
        $siteInfo.empty();
      }))
    // プレビュー削除ボタンの表示スペースを作る
    .find('.site-info').css({
    "padding-right": "30px"
  });

  // hidden に URL 追加
  $siteInfoUrl.val(data.url);
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

/**
 * サニタイズ処理
 * DOMから取得するデータはサーバサイドのサニタイズがリセットされてしまうため、
 * 改めてこのメソッドでサニタイズする必要がある。
 *
 * @param string
 * @returns string
 */
function sanitize(string) {
  if (typeof string !== 'string') {
    return string;
  }
  return string.replace(/[&'`"<>]/g, function (match) {
    return {
      '&': '&amp;',
      "'": '&#x27;',
      '`': '&#x60;',
      '"': '&quot;',
      '<': '&lt;',
      '>': '&gt;',
    }[match]
  });
}

function isOnline() {
  return Boolean(network_reachable);
}
