/**
 * This file contains script related to page header
 */
"use strict";

$(function () {
  $(document).on("click", ".glHeaderMobile-nav-menu-link", function (e) {
    if (!$(this).data('toggle')) {
      return;
    }
    if ($(this).parent().hasClass('open')) {
      $('#goalousNavigation').addClass('force-display-dropdown');
    } else {
      $('#goalousNavigation').removeClass('force-display-dropdown');
    }
  });

  // Click at Message Icon
  $(document).on("click", ".click-header-message", function (e) {
    // 未読件数が 0 件の場合は、直接メッセージ一覧ページに遷移させる
    if (getMessageNotifyCnt() == 0) {
      evMessageList(null);
      return;
    }

    initTitle();
    updateMessageListBox();
  });

  // Click at notifications icon
  var click_cnt = 0;
  $(document).on("click", ".btn-notify-header", function () {
    click_cnt++;

    var isExistNewNotify = ifNewNotify();
    initBellNum();
    initTitle();

    if (isExistNewNotify || click_cnt == 1 || do_reload_header_bellList) {
      updateListBox();
      do_reload_header_bellList = false;
    }

    function ifNewNotify() {
      var newNotifyCnt = getNotifyCnt();
      if (newNotifyCnt > 0) {
        return true;
      }
      return false;
    }
  });
  $(document).on("click", ".call-notifications", evNotifications);
  $(document).on('click', '.mark_all_read', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'GET',
      url: cake.url.an,
      cache: false,
      success: function () {
        $(".notify-card-list").removeClass('notify-card-unread').addClass('notify-card-read');
      }
    });
    return false;
  });

  // Auto update notify cnt
  if (cake.data.team_id) {
    setIntervalToGetNotifyCnt(cake.notify_auto_update_sec);
  }
  setNotifyCntToBellAndTitle(cake.new_notify_cnt);
  //メッセージ詳細ページの場合は実行しない。(メッセージ取得後に実行される為)
  if (cake.request_params.controller != 'posts' || cake.request_params.action != 'message') {
    setNotifyCntToMessageAndTitle(cake.new_notify_message_cnt);
  }

  // ヘッダーのお知らせ一覧ポップアップのオートローディング
  var prevScrollTop = 0;
  $('#NotiListScroll').scroll(function () {
    var $this = $(this);
    var currentScrollTop = $this.scrollTop();
    if (prevScrollTop < currentScrollTop && ($this.get(0).scrollHeight - currentScrollTop == $this.height())) {
      if (!autoload_more) {
        autoload_more = true;
        $('.NotifyDropDownReadMore').trigger('click');
      }
    }
    prevScrollTop = currentScrollTop;
  });

  //チーム切り換え
  $('#SwitchTeam, .js-switchTeam').change(function () {
    var val = $(this).val();
    var url = "/teams/ajax_switch_team/team_id:" + val;
    window.localStorage.removeItem('token');
    $.get(url, function (data) {
      location.href = data;
    });
  });

  // ヘッダーの検索フォームの処理
  require(['search'], function (search) {
    search.headerSearch.setup();
  });

  require(['searchToggle'], function (searchToggle) {
    searchToggle.headerSearchToggle.setup();
  });

  // close banner
  $('.js-disappear-banner').click(function () {
    $('.banner-alert').remove();
    resetNavBarPadding();
    setCookieCloseAlert(cake.data.team_id);
  });

  var $navbar = $('.navbar');
  // Workaround for buggy header/footer fixed position when virtual keyboard is on/off
  $(document).on('focus','input,textarea',function(){
    $navbar.css('position','fixed');
  });
  $(document).on('blur','input,textarea',function(){
    $navbar.css('position','fixed');
    setTimeout(function(){
      if(typeof($.mobile) != 'undefined'){
        window.scrollTo($.mobile.window.scrollLeft(),$.mobile.window.scrollTop());
      }
    },20)
  });

  if(cake.is_mb_app_ios_high_header){
    insertSpaceTop(20);
  }

});

// If team status is read only, Display read only alert box
if (cake.require_banner_notification && isClosedAlert(cake.data.team_id) === false) {
  // Because alert box is postion fixed,
  // so body padding should be resized after window loaded and resized.
  window.addEventListener("DOMContentLoaded", function (event) {
    adjustHeaderPosition();
  });
  window.addEventListener("resize", function (event) {
    adjustHeaderPosition();
  });
}

function instertSpaceTop(height){
  var $header = $('#header'),
      $jsLeftSideContainer = $('#jsLeftSideContainer'),
      $jsRightSideContainer = $('#jsRightSideContainer'),
      $body = $('body'),
      $spFeedAltSub = $('#SpFeedAltSub'),
      $sidebarSetting = $('#SidebarSetting'),
      $scrollSpyContents = $('#ScrollSpyContents > div');

  $header.css('max-height', parseInt($header.css('max-height')) + height + 'px');
  $header.css('padding-top', parseInt($header.css('padding-top')) + height + 'px');
  $jsLeftSideContainer.css('top', parseInt($jsLeftSideContainer.css('top')) + height + 'px');
  $jsRightSideContainer.css('top', parseInt($jsRightSideContainer.css('top')) + height + 'px');
  $body.css('padding-top', parseInt($body.css('padding-top')) + height + 'px');
  $spFeedAltSub.css('top', parseInt($spFeedAltSub.css('top')) + height + 'px');
  $sidebarSetting.css('top', parseInt($sidebarSetting.css('top')) + height + 'px');
  $scrollSpyContents.each(function(i,elem){
    $(elem).css('padding-top',parseInt($(elem).css('padding-top')) + height + 'px');
    $(elem).css('margin-top',parseInt($(elem).css('margin-top')) - height + 'px');
  });
}
/**
 * Initialize page title
 */
function initTitle() {
  var $title = $("title");
  $title.text(sanitize($title.attr("origin-title")));
}

/**
 * Return count of notifications displayed on notification icon
 * @returns {Number}
 */
function getNotifyCnt() {
  var $bellBox = $(".bellNum").first();
  return parseInt($bellBox.children('span').html(), 10);
}

/**
 * Initialize notification icon count to 0
 */
function initBellNum() {
  var $bellBoxs = $(".bellNum");
  $bellBoxs.css("opacity", 0);
  for (var i = 0; i < $bellBoxs.length; i++) {
    $($bellBoxs[i]).children("span").html("0");
  }
}

/**
 * Return Count of messages displayed on messages icons
 * @returns {Number}
 */
function getMessageNotifyCnt() {
  var $box = $(".messageNum").first();
  return parseInt($box.children('span').html(), 10);
}

/**
 * Initialize Message icon count to 0
 */
function initMessageNum() {
  var $box = $(".messageNum");
  $box.css("opacity", 0);
  $box.children('span').html("0");
}

function updateMessageListBox() {
  var $messageDropdown = $(".message-dropdown");
  $messageDropdown.empty();
  var $loader_html = $('<li class="notification-refresh text-align_c"><i class="fa fa-refresh fa-spin"></i></li>');
  //ローダー表示
  $messageDropdown.append($loader_html);
  var url = cake.url.ag;
  $.ajax({
    type: 'GET',
    url: url,
    cache: false,
    success: function (data) {
      //取得したhtmlをオブジェクト化
      var $notifyItems = data;
      $(".notification-refresh").remove();
      $messageDropdown.append($notifyItems);
      //画像をレイジーロード
      imageLazyOn();
    },
    error: function () {
    }
  });
  return false;
}

function evMessageList(options) {
  //とりあえずドロップダウンは隠す
  $(".has-notify-dropdown").removeClass("open");
  $('body').removeClass('notify-dropdown-open');

  var url = cake.url.message_list;
  location.href = url;
  return false;
}

function updateListBox() {
  var $bellDropdown = $(".header-nav-notify-contents");
  $bellDropdown.empty();
  //ローダー表示
  $(".noti-loading").show();
  var url = cake.url.g;
  $.ajax({
    type: 'GET',
    url: url,
    cache: false,
    success: function (data) {
      //取得したhtmlをオブジェクト化
      var $notifyItems = data;
      $(".noti-loading").hide();
      if ($notifyItems.has_noti) {
        $(".js-notiListFlyout-footer").show();
      }
      $bellDropdown.append($notifyItems.html);
      //画像をレイジーロード
      imageLazyOn();
    },
    error: function () {
    }
  });
  return false;
}

function setIntervalToGetNotifyCnt(sec) {
  setInterval(function () {
    updateNotifyCnt();
    updateMessageNotifyCnt();
  }, sec * 1000);
}

function updateNotifyCnt() {
  var url = cake.url.f + '/team_id:' + cake.data.team_id;
  $.ajax({
    type: 'GET',
    url: url,
    cache: false,
    success: function (res) {
      if (res.error) {
        //location.reload();
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
  var url = cake.url.af + '/team_id:' + cake.data.team_id;
  $.ajax({
    type: 'GET',
    url: url,
    cache: false,
    success: function (res) {
      if (res.error) {
        //location.reload();
        return;
      }

      if (res != 0) {
        setNotifyCntToMessageAndTitle(res);
      }
    },
    error: function () {
    }
  });
  return false;
}

function setNotifyCntToBellAndTitle(cnt) {
  var $bellBoxs = $(".bellNum");
  var existingBellCnt = parseInt($bellBoxs.first().children('span').html());

  if (cnt == 0) {
    return;
  }

  for(var i = 0; i < $bellBoxs.length; i++){
    var $bellBox = $($bellBoxs[i]);
    // set notify number
    var $badge = $bellBox.children('span');
    if (parseInt(cnt) > 99) {
      $badge.addClass('oval');
    } else {
      $badge.removeClass('oval');
    }
    $badge.html(cnt);
    updateTitleCount();

    if (existingBellCnt == 0) {
      displaySelectorFluffy($bellBox);
    }
  }

  return;
}

function setNotifyCntToBellForMobileApp(cnt, appendFlg) {
  var $badgeCntParent = $('.js-mbAppFooter-setBadgeCnt-notifications');
  _setNotifyCntForMobileApp(cnt, appendFlg, $badgeCntParent);
}
function setNotifyCntToMessageForMobileApp(cnt, appendFlg) {
  var $badgeCntParent = $('.js-mbAppFooter-setBadgeCnt-messages');
  _setNotifyCntForMobileApp(cnt, appendFlg, $badgeCntParent);
}
function _setNotifyCntForMobileApp(cnt, appendFlg, $badgeCntParent) {
  if (!$badgeCntParent) return;

  var $badgeCnt = $badgeCntParent.find('span');
  if (appendFlg) {
    cnt = cnt + parseInt($badgeCnt.text());
  }

  if (cnt == 0) {
    $badgeCntParent.addClass('hidden');
  } else {
    $badgeCntParent.removeClass('hidden');
    $badgeCntParent.find('span').text(cnt);
  }
}

function setNotifyCntToMessageAndTitle(cnt) {
  var cnt = parseInt(cnt);
  var $bellBoxs = $(".messageNum");
  for(var i = 0; i < $bellBoxs.length; i++) {
    var $bellBox = $($bellBoxs[i]);
    var existingBellCnt = parseInt($bellBox.children('span').html());
    if (cnt != 0) {
      // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
      $('.click-header-message').next().addClass('dropdown-menu');
      $('.click-header-message').next().removeClass('none');
    }
    else {
      // メッセージが存在するときだけ、ボタンの次の要素をドロップダウン対象にする
      $('.click-header-message').next().removeClass('dropdown-menu');
      $('.click-header-message').next().addClass('none');
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

// A global function that shows / hides the mobile navigation when the hamburger icon is tapped.
function toggleNav() {
  var header = document.getElementsByClassName("header")[0],
    layerBlack = document.getElementById('layerBlack'),
    menuNotify = document.getElementsByClassName("js-unread-point-on-hamburger")[0],
    navIcon = header.getElementsByClassName('toggle-icon')[0];
  if (header) {
    if (header.classList.contains('mod-openNav')) {
      document.body.classList.remove('modal-open');
      header.classList.remove('mod-openNav');
      layerBlack.classList.remove('mod-openNav');
      menuNotify.classList.remove('is-open');
      navIcon.classList.remove('fa-arrow-right');
      navIcon.classList.add('fa-navicon');
      $("#NavbarOffcanvas").css("background-color","#fff");
    } else {
      document.body.classList.add('modal-open');
      header.classList.add('mod-openNav');
      layerBlack.classList.add('mod-openNav');
      menuNotify.classList.add('is-open');
      navIcon.classList.add('fa-arrow-right');
      navIcon.classList.remove('fa-navicon');
      $("#NavbarOffcanvas").css("background-color","#f5f5f5");
    }
  }
}

function hideNav() {
  if(cake.is_mb_app !== "1" && cake.is_mb_browser !== "1") {
    var header = document.getElementsByClassName("header")[0],
    layerBlack = document.getElementById('layerBlack'),
    menuNotify = document.getElementsByClassName("js-unread-point-on-hamburger")[0],
    navIcon = header.getElementsByClassName('toggle-icon')[0];
    document.body.classList.remove('modal-open');
    header.classList.remove('mod-openNav');
    layerBlack.classList.remove('mod-openNav');
    menuNotify.classList.remove('is-open');
    navIcon.classList.remove('fa-arrow-right');
    navIcon.classList.add('fa-navicon');
  }
}

/**
 * adjust header body padding
 */
function adjustHeaderPosition() {
  var body_top_padding = 0;
  var $navbar = $('.navbar-fixed-top');
  body_top_padding += parseInt($navbar.outerHeight(true));
  var $banner_alert = $('.banner-alert');
  if ($banner_alert) {
    body_top_padding += parseInt($banner_alert.outerHeight(true));
    $banner_alert.show();
  }
  $('body').css('padding-top', body_top_padding);
}

function resetNavBarPadding() {
  var $navbar = $('.navbar-fixed-top');
  var body_top_padding = parseInt($navbar.outerHeight(true));
  $('body').css('padding-top', body_top_padding);
}

/**
 * set cookie for disappearing alert
 */
function setCookieCloseAlert(teamId) {
  setCookie('alertClosedTeam_' + teamId, 1, 3);
}

function isClosedAlert(teamId) {
  return getCookie('alertClosedTeam_' + teamId) !== "";
}
