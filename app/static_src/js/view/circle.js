// Register circle events.
$(document).on("click", ".js-dashboard-circle-list", evCircleFeed);
$(document).on("click", ".circle-link", evCircleFeed);

// ハンバーガーメニューのサークル未読点描画
$(document).ready(function() {
  updateNotifyOnHamburger();
});

// サークル投稿リアルタイム通知
$(window).load(function () {
  var pusher = new Pusher(cake.pusher.key);
  var socketId = "";
  pusher.connection.bind('connected', function () {
    socketId = pusher.connection.socket_id;
    if (!cake.pusher.socket_id) {
      cake.pusher.socket_id = socketId;
    }
  });

  // サークル投稿リアルタイム通知設定
  if ($('.js-dashboard-circle-list-body')[0] !== undefined) {
    pusher.subscribe('team_' + cake.data.team_id).bind('circle_list_update', function (data) {
      var $circle_list = $('.js-dashboard-circle-list-body');
      var my_joined_circles = data.circle_ids;
      $.each(my_joined_circles, function (i, circle_id) {
        // $circlesはdashboardとhamburgerそれぞれのサークルリストを含むインスタンス。
        var $circles = $circle_list.children('[circle_id=' + circle_id + ']');
        $circles.each(function () {
          var $circle = $(this);
          if ($circle === undefined) {
            return true;
          }

          // サークル未読数のアップデート
          var $unread_box = $circle.find('.js-circle-count-box');
          var unread_count = $unread_box.text().trim();
          if (unread_count == "") {
            $unread_box.text(1);
          } else if (Number(unread_count) == 9) {

            $unread_box.text("9+");
          } else if (unread_count != "9+") {
            $unread_box.html(Number(unread_count) + 1);
          }

          $circle.find('.js-dashboard-circle-list').removeClass('is-read').addClass('is-unread');
          $circle.parent().prepend($circle);
        });
      });
      // サークルの未読件数がUIに反映されたら実行
      updateNotifyOnHamburger();
    });
  }
});

// Ajax的なサークルフィード読み込み
function evCircleFeed(options) {
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
  var circle_id = sanitize($obj.attr('circle-id'));
  // DOMから取得し再度DOMに投入するデータなのでサニタイズを行う
  var title = sanitize($obj.attr('title'));
  var public_flg = sanitize($obj.attr('public-flg'));
  var team_all_flg = sanitize($obj.attr('team-all-flg'));
  var oldest_post_time = sanitize($obj.attr('oldest-post-time'));
  // URL生成
  var url = get_url.replace(/circle_feed/, "ajax_circle_feed");
  var more_read_url = get_url.replace(/\/circle_feed\//, "\/posts\/ajax_get_feed\/circle_id:");

  if ($obj.hasClass('is-hamburger')) {
    //ハンバーガーから来た場合は隠す
    $("#header-slide-menu").click();
  }
  //app-view-elements-feed-postsが存在しないところではajaxでコンテンツ更新しようにもロードしていない
  //要素が多すぎるので、おとなしくページリロードする
  //urlにcircle_feedを含まない場合も対象外
  if (!$("#app-view-elements-feed-posts").exists() || !$("#GlobalForms").exists() || !get_url.match(/circle_feed/)) {
    window.location.href = get_url;
    return false;
  }
  //サークルリストのわきに表示されている未読数リセット
  $obj.children(".js-circle-count-box").html("");
  $obj.children(".circle-count_box").children(".count-value").html("");
  $obj.removeClass('is-unread').addClass('is-read');
  updateNotifyOnHamburger();
  //アドレスバー書き換え
  if (!updateAddressBar(get_url)) {
    return false;
  }
  // メインカラム内の要素をリセット
  // FIXME:本来は「$("#app-view-elements-feed-posts").empty();」のようにメインカラム.フィード親要素をemptyにすれば良いだけだがHTMLの作り上そうなっていないので、上記のような処理をせざるをえない。
  $(".panel.panel-default").not(".feed-read-more, .global-form, .dashboard-krs, .js_progress_graph").remove();
  //ローダー表示
  var $loader_html = opt.loader_id ? $('#' + opt.loader_id) : $('<center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>');
  if (!opt.recursive) {
    $("#app-view-elements-feed-posts").html($loader_html);
  }

  // TODO: サブヘッダを削除するタイミングでこの処理も必要無くなるので一緒に削除する。
  if ($("#SubHeaderMenuFeed").exists()) {
    $("#SubHeaderMenuFeed").click();
  }

  $("#FeedMoreRead").removeClass("hidden");
  // read more 非表示
  $("#FeedMoreReadLink").css("display", "none");


  //サークル名が長すぎる場合は切る
  var panel_title = title;
  if (title.length > 30) {
    panel_title = title.substr(0, 29) + "…";
  }

  $("#circle-filter-menu-circle-name").html(panel_title);
  $("#circle-filter-menu-member-url").data("url", "/circles/ajax_get_circle_members/circle_id:" + circle_id);
  $(".feed-share-range-file-url").attr("href", "/posts/attached_file_list/circle_id:" + circle_id);
  $('#postShareRangeToggleButton').removeAttr('data-toggle-enabled');
  if (public_flg == 1) {
    $("#feed-share-range-public-flg").children("i").removeClass("fa-lock").addClass("fa-unlock");
    $('#postShareRange').val("public");
    $('#PostSecretShareInputWrap').hide();
    $('#PostPublicShareInputWrap').show();

    $('#select2PostCircleMember').val("circle_" + circle_id);
    $('#select2PostSecretCircle').val("");
  } else {
    $("#feed-share-range-public-flg").children("i").removeClass("fa-unlock").addClass("fa-lock");
    $('#postShareRange').val("secret");
    $('#PostPublicShareInputWrap').hide();
    $('#PostSecretShareInputWrap').show();

    $('#select2PostCircleMember').val("");
    $('#select2PostSecretCircle').val("circle_" + circle_id);
  }
  $("#postShareRangeToggleButton").popover({
    'data-toggle': "popover",
    'placement': 'top',
    'trigger': "focus",
    'content': cake.word.share_change_disabled,
    'container': 'body'
  });
  // circle情報パネル表示
  $(".feed-share-range").css("display", "block");

  //Post後のリダイレクトURLを設定
  $("#PostRedirectUrl").val(get_url);

  $.ajax({
    type: 'GET',
    url: url,
    async: true,
    dataType: 'json',
    success: function (data) {
      var post_time_before = "";
      var image_url = data.circle_img_url;

      updateCakeValue(circle_id, title, image_url);

      setDefaultTab();
      initCircleSelect2();

      $('#OpenCircleSettingMenu').empty();

      if (!$.isEmptyObject(data.html)) {
        //取得したhtmlをオブジェクト化
        var $posts = $(data.html);
        //notify一覧に戻るhtmlを追加
        //画像をレイジーロード
        imageLazyOn($posts);
        //一旦非表示
        $posts.fadeOut();

        $("#app-view-elements-feed-posts").html($posts);
        //read moreの情報を差し替え

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

      if (data.post_time_before != null) {
        post_time_before = data.post_time_before;
      }

      $("#FeedMoreReadLink").attr("get-url", more_read_url);
      $("#FeedMoreReadLink").attr("month-index", 0);
      $("#FeedMoreReadLink").attr("next-page-num", 2);
      $("#FeedMoreReadLink").attr("oldest-post-time", oldest_post_time);
      $("#FeedMoreReadLink").attr("post-time-before", post_time_before);
      $("#FeedMoreReadLink").css("display", "inline");

      $("#circle-filter-menu-circle-member-count").html(data.circle_member_count);
      $(".js-circle-filter-menu-image").attr('src', image_url);

      //サークル設定メニュー生成
      if (!team_all_flg && data.user_status == "joined") {
        $('#OpenCircleSettingMenu')
          .append('<li><a href="/posts/unjoin_circle/circle_id:' + circle_id + '">' + cake.word.leave_circle + '</a></li>');
      }
      if (data.user_status == "joined" || data.user_status == "admin") {
        $('#OpenCircleSettingMenu')
          .append('<li><a href="/circles/ajax_setting/circle_id:' + circle_id + '" class="modal-circle-setting">' + cake.word.config + '</a></li></ul>');
      }

      $loader_html.remove();
      action_autoload_more = false;
      autoload_more = false;
      feed_loading_now = false;
      do_reload_header_bellList = true;
    },
    error: function () {
      feed_loading_now = false;
    }
  });
  return false;
}

function updateNotifyOnHamburger() {
  var $list_elem = $('.js-dashboard-circle-list.is-hamburger');
  var existUnreadCircle = $list_elem.hasClass('is-unread');
  if (existUnreadCircle) {
    $('.js-unread-point-on-hamburger').removeClass('is-read');
  } else {
    $('.js-unread-point-on-hamburger').addClass('is-read');
  }
}

// サークルフィード用のcake value 更新
function updateCakeValue(circle_id, title, image_url) {
  //サークルフィードでは必ずデフォルト投稿タイプはポスト
  cake.common_form_type = "post";

  cake.data.b = function (element, callback) {
    var data = [];
    var current_circle_item = {
      id: "circle_" + circle_id,
      text: title,
      image: image_url
    };

    data.push(current_circle_item);
    callback(data);
  }

  cake.data.select2_secret_circle = function (element, callback) {
    var data = [];
    var current_circle_item = {
      id: "circle_" + circle_id,
      text: title,
      image: image_url,
      locked: true
    };
    data.push(current_circle_item);
    callback(data);
  }
}
