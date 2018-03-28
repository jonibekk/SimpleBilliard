/**
 * This file contains script related to Posts
 */
"use strict";

$(function () {
  // 投稿フォームが表示されるページのみ
  if ($('#CommonPostBody').length) {
    require(['ogp'], function (ogp) {
      // OGP
      var patterns = {
        protocol: '^(http(s)?(:\/\/))?(www\.)?',
        domain: '[a-zA-Z0-9-_\.]+',
        tld: '(\.[a-zA-Z0-9]{2,})',
        params: '([-a-zA-Z0-9:%_\+.~#?&//=]*)'
      }

      var p = patterns;
      var pattern = new RegExp(p.protocol + p.domain + p.tld + p.params, 'gi');

      $('#CommonPostBody').on('keyup', function (e) {
        if(e.keyCode == 32 || e.keyCode == 13) {
          var input = $('#CommonPostBody').val();
          var res = pattern.exec(input);
          if(res) {
            getPostOGPInfo(ogp, res[0]);
          }
        }
      });
      // 投稿編集の場合で、OGPのurlが登録されている場合
      if ($('.post-edit').length) {
        if ($('.post-edit').attr('data-default-ogp-url')) {
          getPostOGPInfo(ogp, $('.post-edit').attr('data-default-ogp-url'));
        }
      }
    });
    // register event of deleting post draft
    $('.delete-post-draft').on('click', function() {
        var postDraftId = $(this).data('post-draft-id')
        if (window.confirm(cake.message.notice.confirm_cancel_post)) {
            $.ajax({
                url: '/api/v1/post_drafts/' + postDraftId,
                type: 'DELETE'
            }).always(function (data) {
                location.reload()
            })
        }
        return false
    })
  }
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

  $(document).on({
    'click': function (e) {
      var $target = $(this);

      // jQuery .data() shouldn't be used.
      // Instead we use native dataset
      // Ref: https://stackoverflow.com/questions/8707226/jquery-data-does-not-work-but-attr-does
      var is_saved_item = this.dataset.isSavedItem;
      var post_id = this.dataset.id;
      changeSavedPostStyle($target, is_saved_item);
      this.dataset.isSavedItem = is_saved_item ? "" : "1";

      if (is_saved_item) {
        deleteItem(post_id, is_saved_item, $target);
      } else {
        saveItem(post_id, is_saved_item, $target);
      }
    }
  }, '.js-save-item');
});

function saveItem(post_id, is_saved_item, $target) {
  $.ajax({
    url: "/api/v1/posts/" + post_id + "/saved_items",
    type: 'POST',
    success: function (data) {
      new Noty({
        type: 'success',
        text: __("Item saved"),
      }).show();
    },
    error: function (res, textStatus, errorThrown) {
      changeSavedPostStyle($target, !is_saved_item);
      var body = res.responseJSON;
      new Noty({
        type: 'error',
        text: body.message,
      }).show();
      return false;
    }
  });
}

function deleteItem(post_id, is_saved_item, $target) {
  $.ajax({
    url: "/api/v1/posts/" + post_id + "/saved_items",
    type: 'DELETE',
    success: function (data) {
      new Noty({
        type: 'success',
        text: __("Item removed"),
      }).show();
    },
    error: function (res, textStatus, errorThrown) {
      changeSavedPostStyle($target, !is_saved_item);
      var body = res.responseJSON;
      new Noty({
        type: 'error',
        text: body.message,
      }).show();
      return false;
    }
  });
}

/**
 *
 * @param is_saved_item
 */
function changeSavedPostStyle($obj, current_status) {
  if (current_status) {
    $obj.removeClass("mod-on").addClass("mod-off");
  } else {
    $obj.removeClass("mod-off").addClass("mod-on");
  }
}

// for resizing certainly, exec after window loaded
window.addEventListener("load", function () {
  // Adjust size for single image post
  changeSizeFeedImageOnlyOne($('.feed_img_only_one'));
  // Adjust size for multiple images post
  bindPostBalancedGallery($('.post_gallery'));
});

/**
 * Display multiple images post as gallery grid
 * @param $obj
 */
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

/**
 * Request OGP info for post
 * @param ogp
 * @param text
 */
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
  return false;
}

/**
 * Append the acquired OGP info to requested post
 * @param data
 */
function appendPostOgpInfo(data) {
  var $siteInfoUrl = $('#PostSiteInfoUrl');
  var $siteInfo = $('#PostOgpSiteInfo');
  $siteInfo
  // プレビュー用 HTML
    .html(data.html)
    // プレビュー削除ボタンを重ねて表示
    .prepend($('<a>').attr('href', '#')
      .addClass('font_lightgray')
      .css({
        right: '35px',
        "margin-top": '25px',
        position: 'absolute',
        display: "block",
        "z-index": '1000'
      })
      .append('<i class="fa fa-times fa-2x"></i>')
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
  return false;
}
