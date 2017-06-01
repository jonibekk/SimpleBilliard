/**
 * This file contains script related to Posts
 */
"use strict";

$(function () {
    console.log("LOADING: posts.js");

    // 投稿フォームが表示されるページのみ
    if ($('#CommonPostBody').length) {
        require(['ogp'], function (ogp) {
            // 投稿編集の場合で、OGPのurlが登録されている場合
            if ($('.post-edit').length) {
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

/**
 * Request OGP info for post
 * @param ogp
 * @param text
 */
function getPostOGPInfo(ogp, text) {
    console.log("posts.js: getPostOGPInfo");
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

/**
 * Append the acquired OGP info to requested post
 * @param data
 */
function appendPostOgpInfo(data) {
    console.log("posts.js: appendPostOgpInfo");
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