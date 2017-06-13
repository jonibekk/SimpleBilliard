$(function () {
    // TODO: Remove console log
    console.log("LOADING: evaluations.js");

    $(document).on("click", ".click-show-post-modal", getModalPostList);
});

function getModalPostList(e) {
    // TODO: Remove console log
    console.log("evaluations.js: getModalPostList");
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

/**
 * 画像の高さを親の要素に割り当てる
 *
 * @param $obj
 */
function changeSizeActionImage($obj) {
    // TODO: Remove console log
    console.log("evaluations.js: changeSizeActionImage");
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