define(function () {
    var loadingLock = ['lock'];
    var getOGPSiteInfo = function (params) {
        $.extend({
            // URL が含まれるテキスト
            text: '',
            // OGP 情報取得の ajax 処理を行うかどうかのチェック
            // false を返すと情報取得処理を行わない
            readyLoading: function () {
                return true;
            },
            // OGP 情報の取得開始時
            loadingStart: function () {
            },
            // OGP 情報の取得完了時（エラー時も含む）
            loadingEnd: function () {
            },
            // OGP 情報の取得が成功した時
            success: function () {
            },
            // OGP 情報の取得に失敗した時
            error: function () {
            }
        }, params);

        // URL が含まれているか簡易チェック
        if (params.text.indexOf('http') == -1) {
            return;
        }

        // 同時リクエスト防止用ロック
        var lock = loadingLock.pop();
        if (!lock) {
            return;
        }

        if (!params.readyLoading()) {
            loadingLock.push('lock');
            return;
        }

        params.loadingStart();

        $.ajax({
            type: 'GET',
            url: cake.url.ogp_info,
            data: {
                text: params.text
            },
            dataType: 'json',
        })
            .done(function (data) {
                if (data.title) {
                    params.success(data);
                }
            })
            .fail(function () {
                params.error();
            })
            .always(function () {
                params.loadingEnd();
                loadingLock.push('lock');
            });
    };

    return {
        getOGPSiteInfo: getOGPSiteInfo
    };
});
