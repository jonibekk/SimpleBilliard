/**
 * This file contains script related to Posts
 */
"use strict";

/**
 * Checks valid Url
 */
var url_pattern = {
    protocol: 'https?:\/\/(www\.)?',
    domain: '[a-zA-Z0-9-_\.]+',
    tld: '(\.[a-zA-Z0-9]{2,})',
    params: '([-a-zA-Z0-9:%_\+.~#?&//=]*)'
}

function getValidURL(input){
    var regex = new RegExp(url_pattern.protocol + url_pattern.domain + url_pattern.tld + url_pattern.params, 'g');
    var result = regex.exec(input);
    if(result){
        return result[0];
    } else {
        return null;
    }
}

$(function () {
  // Only on the where the Posts Page is displayed
    require(['ogp'], function (ogp) {
      $('#CommonPostBody').off('keyup').on('keyup', function (e) {
          if ($('#PostSiteInfoUrl').val()) {
              return false;
          }
          var position = $('#CommonPostBody').get(0).selectionStart - 1;
          var key = this.value.charCodeAt(position);
          if(key == 32 || key == 10) {
              var url = getValidURL($('#CommonPostBody').val());
              if(url) {
                  getPostOGPInfo(ogp, url);
              }
          }
      });
      $('#CommonPostBody').off('paste').on('paste', function (e) {
          if (!$('#PostSiteInfoUrl').val()) {
            var url = getValidURL(e.originalEvent.clipboardData.getData('text'));
            if(url) {
                getPostOGPInfo(ogp, url);
            }
          }
      });
      // When editing posts and OGP'S url has been set
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
  // Circle Page's Attachment file type switching
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
    $obj.removeClass("saved");
  } else {
    $obj.addClass("saved");
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
    // Text containting the url
    text: text,

    // Checks if necessary to obtain ogp
    readyLoading: function () {
      return true;
    },

    // On success retreiving the ogp data
    success: function (data) {
      appendPostOgpInfo(data);
    },

    // On failure retreiving the ogp data
    error: function () {
      // remove loading icon
      $('#PostSiteInfoLoadingIcon').remove();
    },

    // Start retreiving the ogp data
    loadingStart: function () {
      // show loading icon
      $('<i class="fa fa-refresh fa-spin"></i>')
        .attr('id', 'PostSiteInfoLoadingIcon')
        .addClass('pull-right lh_20px')
        .insertBefore('#CommonFormTabs');
    },

    // Finish retreiving the ogp data
    loadingEnd: function () {
      // remove loading icon
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
  var $siteInfo = $('#PostOgpSiteInfo').html(data.html);
  if($siteInfo.hasClass('edit-post-ogp-site-info')){
    var addClass = 'edit-feed-post-ogp-close';
  } else {
    var addClass = 'feed-post-ogp-close';
  }
  $siteInfo
    // show delete button
    .prepend($('<a>').attr('href', '#')
      .addClass('font_lightgray ogp-close')
      .addClass(addClass)
      .append('<i class="fa fa-times fa-2x js-ogp-close"></i>')
      .on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $siteInfoUrl.val('');
        $siteInfo.empty();
        $(this).remove();
      }))
    // makes additional room for the delete button
    .find('.site-info').css({
    "padding-right": "30px"
  });

  // add url to hidden
  $siteInfoUrl.val(data.url);
  return false;
}
