$(document).ready(function () {

  // Save jwt to local storage before move to new Goalous
  if(cake.jwt_token) {
    localStorage.setItem('token', cake.jwt_token);
  }
  // Delete token to prevent access to new Goalous after logout
  if(!cake.data.user_id && localStorage.getItem('token')) {
    localStorage.removeItem('token');
  }

  window.addEventListener('MobileKeyboardStatusChanged', evtMobileKeyboardStatusChanged);

  //アップロード画像選択時にトリムして表示
  $('.fileinput').fileinput().on('change.bs.fileinput', function (e) {
    $(this).children('.nailthumb-container').nailthumb({width: 150, height: 150, fitDirection: 'center center'});
  });
  //アップロード画像選択時にトリムして表示
  $('.fileinput_small').fileinput().on('change.bs.fileinput', function (e) {
    $(this).children('.nailthumb-container').nailthumb({width: 96, height: 96, fitDirection: 'center center'});
  });
  //アップロード画像選択時にトリムして表示
  $('.fileinput_very_small').fileinput().on('change.bs.fileinput', function (e) {
    $(this).children('.nailthumb-container').nailthumb({width: 34, height: 34, fitDirection: 'center center'});
  });
  //アップロード画像選択時にトリムして表示
  $('.fileinput_post_comment').fileinput().on('change.bs.fileinput', function (e) {
    $(this).children('.nailthumb-container').nailthumb({width: 50, height: 50, fitDirection: 'center center'});
  });

  $('.fileinput-exists,.fileinput-new').fileinput().on('change.bs.fileinput', function (e) {
    exifRotate(this);
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


  //noinspection JSUnresolvedVariable
  $(document).on("click", ".check-target-toggle", evToggle);



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
      }).done(function () {
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

});


$(function () {
    var lastWidth,lastHeight;
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

    function updateSearchPosition(){
      if(lastWidth >= 768){
        $("#NavSearchForm").css("right", (($(window).width() - $(".nav-container").width()) / 2) + "px");
      } else if (lastWidth >= 480) {
        $("#NavSearchForm").css("right", "0px");
      }
    }

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
    var isHeaderClick = false;
    var isSearchClick = false;
    $(".header-icon-zoom").off("click").on("click", function(e) {
      $(this).toggleClass("open");
      $(".header-icon-zoom").not($(this)).removeClass("open");
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      $("#NavSearchInputClear").prev().prev().val('');
      $("#NavSearchInputClear").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      $("#NavSearchInputClearToggle").prev().prev().val('');
      $("#NavSearchInputClearToggle").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      $(".header-search-toggle").removeClass("shown");
      $(".header-search").removeClass("shown");
      hideNav();
      isHeaderClick = true;
    });
    var timeoutToggle;
    $(".header-icon-search-toggle").off("click").on("click", function(e) {
      e.preventDefault();
      $(".header-search-toggle").toggleClass("shown");
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      $("#NavSearchInputClear").prev().prev().val('');
      $("#NavSearchInputClear").hide();
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
      $("#NavSearchInputClearToggle").prev().prev().val('');
      $("#NavSearchInputClearToggle").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      timeoutToggle = setTimeout(function(){$("#NavSearchInputToggle").focus();},650);
      hideNav();
      isSearchClick = true;
    });

    $("#NavSearchInputToggle").on("keyup", function(){
      if($.trim($("#NavSearchInputToggle").val()).length){
        clearTimeout(timeoutToggle);
      } else {
        timeoutToggle = setTimeout(function(){$("#NavSearchInputToggle").focus();},650);
      }
    });

    $("#NavSearchForm,#NavSearchFormToggle").on("click", function(){
      isSearchClick = true;
    });

    var timeout;
    $(".header-icon-search").off("click").on("click", function(e) {
      e.preventDefault();
      $(".header-search").toggleClass("shown");
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      $("#NavSearchInputClear").prev().prev().val('');
      $("#NavSearchInputClear").hide();
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
      $("#NavSearchInputClearToggle").prev().prev().val('');
      $("#NavSearchInputClearToggle").hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
      timeout = setTimeout(function(){$("#NavSearchInput").focus();},650);
      hideNav();
      isSearchClick = true;
    });
    $(document).on("click", function(e){
      if(!isHeaderClick){
        $(".open").removeClass("open");
      }
      if(!isSearchClick) {
        $(".header-search").removeClass("shown");
        $(".header-search-toggle").removeClass("shown");
      }
      isSearchClick = false;
      isHeaderClick = false;
    });
    // $(".dropdown-menu").on("click", function(e){
    //   e.stopPropagation();
    // });
    $("#NavSearchInput").on("keyup", function(){
      if($.trim($("#NavSearchInput").val()).length){
        clearTimeout(timeout);
      } else {
        timeout = setTimeout(function(){$("#NavSearchInput").focus();},650);
      }
    });
    $(window).on('resize load pageshow', function(){
      if($(window).width() !== lastWidth){
        lastWidth = $(window).width();
        $("#NavSearchResults").empty();
        $("#NavSearchResults").hide();
        $("#NavSearchResultsToggle").empty();
        $("#NavSearchResultsToggle").hide();
        $("#NavSearchInputClear").trigger("click");
        $("#NavSearchInputClearToggle").trigger("click");
        $(".header-search-toggle").removeClass("shown");
        $(".header-search").removeClass("shown");
        if(lastWidth > 479){
          updateSearchPosition();
        }
      }
      if($(window).height() !== lastHeight){
        var extra = 0;
        if($(".banner-alert").css("display") === "block"){
          extra = 80;
        }
        extra += 380;
        if (cake.is_demo) {
          // Calc in light of demo alert header
          $(".dashboard-circle-list-body").css("height","calc(100vh - " + extra + "px - 54px)")
        } else {
          $(".dashboard-circle-list-body").css("height","calc(100vh - " + extra + "px)")
        }
      }
    });
    $("#NavSearchInputClear").off("click").on("click", function() {
      $("#NavSearchInput").focus();
      $(this).prev().prev().val('');
      $(this).hide();
      $("#NavSearchResults").empty();
      $("#NavSearchResults").hide();
    });
    $("#NavSearchInputClearToggle").off("click").on("click", function() {
      $("#NavSearchInputToggle").focus();
      $(this).prev().prev().val('');
      $(this).hide();
      $("#NavSearchResultsToggle").empty();
      $("#NavSearchResultsToggle").hide();
    });
    $("#NavSearchHide,#NavSearchHideToggle").off("click").on("click", function() {
        $("#NavSearchResults").empty();
        $("#NavSearchResults").hide();
        $("#NavSearchResultsToggle").empty();
        $("#NavSearchResultsToggle").hide();
        $("#NavSearchInputClear").trigger("click");
        $("#NavSearchInputClearToggle").trigger("click");
        $(".header-search-toggle").removeClass("shown");
        $(".header-search").removeClass("shown");
    });
    $("#toggleNavigationButton").on("click", function() {
      $("#NavSearchHide,#NavSearchHideToggle").trigger("click");
    });
    $(window).trigger('resize');
    $(".no-anchor").off("click").on("click", function(e) {
      e.preventDefault();
      return false;
    });
    $("#ActionFileAttachButton").off("click").on("click", function(e) {
      e.preventDefault();
    });
    $(".btn-back-actions,.btn-back-goals").off("click").on("click", function(e) {
      e.preventDefault();
      window.history.back();
    });
    $(".modal").on("hide.bs.modal", function() {
      $(".select2-display-none").css("display","none");
    });

  // For Mobile App Footer (Web)
  // Change menu to active(change color) immediately after click to make it look like native.
  var _eventType = ("ontouchstart" in document) ? 'touchstart':'click';
  $('.mobile-app-footer-list').on(_eventType, ".mobile-app-footer-list-item-link", function(e){
    // Note: why don't write 'e.preventDefault()' is that didn't react these code immediately, it means that it looks change to active after moved to page.
    $(".mobile-app-footer-list-item-link").removeClass('active');
    $menuLink = $(e.target).closest('.mobile-app-footer-list-item-link');
    $menuLink.addClass('active');
  });

});
// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

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
  var $parent = $obj.closest('.form-group');
  var show_target_id = $obj.attr("show-target-id");
  var delete_target_id = $obj.attr("delete-target-id");
  $parent.find("#" + show_target_id).removeClass('hidden');
  $parent.find("#" + delete_target_id).remove();

  return false;
}

function evTargetEnabled() {
  attrUndefinedCheck(this, 'target-id');
  var $obj = $(this);
  var target_id = $obj.attr("target-id");
  $("#" + target_id).removeAttr("disabled");
  return true;
}

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
  autosize($(this));
  //submitボタンを表示
  $("#" + $(this).attr('target_show_id')).show();
  //クリック済みにする
  $(this).addClass('clicked');
}

function warningAction($obj) {
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
  warningAction($modal_elm);
  $modal_elm.on('shown.bs.modal', function (e) {
    $(this).find('textarea').each(function () {
      autosize($(this));
    });
  });
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
          $content.hide();
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

      // Disable bootstrapValidator because of freeze bug
      // https://jira.goalous.com/browse/GL-7070
      var $form = $modal_elm.find('form');
      if ($form.attr('id') != "AddGoalFormKeyResult") {
        $form.bootstrapValidator({
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
      }

      $modal_elm.modal();
      $('body').addClass('modal-open');
    });
  }
}

// youtubeビデオ読み込み
window.addEventListener('load', function () {
  $("a.youtube").YouTubeModal({autoplay: 0, width: 640, height: 360});
});

function evtMobileKeyboardStatusChanged(e) {
  var mbFooter = document.getElementById('MobileAppFooter');
  if (!mbFooter) {
    return;
  }

  var mbKeyboardStatus = e.detail.status;
  if (!mbKeyboardStatus) {
    return;
  }
  if (mbFooter.dataset.isAlwaysHidden === 'true') {
    return;
  }
  switch (mbKeyboardStatus) {
    case 'started_displaying':
      mbFooter.classList.add('hidden');
      break;
    case 'started_closing':
      mbFooter.classList.remove('hidden');
      break;
  }
}
function evtMobileKeyboardStatusChangedForTopicDetail(e) {
  var mbKeyboardStatus = e.detail.status;
  if (!mbKeyboardStatus) {
    return;
  }
  switch (mbKeyboardStatus) {
    case 'changed_height':
    case 'closed':
      const scrollHeight = document.body.scrollHeight;
      window.scrollTo(0, scrollHeight);
      document.body.scrollTop = scrollHeight;
      break;
  }
}
function triggerMobileKeyboardStatusChanged(status, height) {
  // Native side
  var event = new CustomEvent('MobileKeyboardStatusChanged', {detail: {
      status: status, // keyboard status 'started_displaying', 'displayed', 'changed_height', 'started_closing', 'closed'
      height: height // keyboard height
    }})
  window.dispatchEvent(event);
}
