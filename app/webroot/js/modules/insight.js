define(function () {
    // 集計画面の処理
    var $formInputs = $('#InsightInputDateRange, #InsightInputGroup, #InsightInputType, #InsightInputTimezone, #InsightInputTeam');

    var createInsightCallBack = function (ajax_url, result_container_id) {
        var onInsightFormChange = function () {
            var $form = $('#InsightForm');
            var $result = $('#' + result_container_id);

            // ローダー表示
            $result.html('<div class="text-align_c"><i class="fa fa-refresh fa-spin"></i></div>');

            // イベント外す
            $formInputs.off('change', onInsightFormChange);

            $.ajax({
                type: 'GET',
                url: ajax_url,
                data: $form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.html) {
                        $result.html(data.html);
                    }
                    $formInputs.on('change', onInsightFormChange);
                }
            });
        };
        return onInsightFormChange;
    };

    // チーム集計セットアップ
    var setupInsight = function () {
        $(document).on('change', '#InsightUniqueToggle', function () {
            var $this = $(this);
            if ($this.val() == 'unique') {
                $('#InsightTotalRow').hide();
                $('#InsightUniqueRow').show();
            }
            else {
                $('#InsightUniqueRow').hide();
                $('#InsightTotalRow').show();
            }
        });
        $formInputs.on('change', createInsightCallBack(cake.url.insight, 'InsightResult'));



    };

    // サークル集計セットアップ
    var setupInsightCircle = function () {
        $formInputs.on('change', createInsightCallBack(cake.url.insight_circle, 'InsightCircleResult'));
    };

    // ランキング集計セットアップ
    var setupInsightRanking = function () {
        $formInputs.on('change', createInsightCallBack(cake.url.insight_ranking, 'InsightRankingResult'));
    };

    var reload = function () {
        $formInputs.trigger('change');
    };


    return {
        reload: reload,
        insight: {
            setup: setupInsight
        },
        insightCircle: {
            setup: setupInsightCircle
        },
        insightRanking: {
            setup: setupInsightRanking
        }
    };
});
