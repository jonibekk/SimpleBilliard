define(function () {
    // 集計画面の処理
    var $formInputs = $('#InsightInputDateRange, #InsightInputGroup, #InsightInputType, #InsightInputTimezone, #InsightInputTeam');

    var createCallback = function (ajax_url, result_container_id, options) {

        options = $.extend({
            afterSuccess: function() {
            }
        }, options);

        var onInsightFormChange = function () {
            var $form = $('#InsightForm');
            var $result = $('#' + result_container_id);

            // ローダー表示
            $result.html('<div class="text-align_c"><i class="fa fa-refresh fa-spin"></i><div id="after-long" style="display: none"></div></div>');

            // 10000 means after 10 sec the text will display
            setTimeout(function(){
                $('#after-long').html(cake.word.waiting_message);
                $('#after-long').removeAttr("style");
            }, 10000 );

            // setting the browser url without loading the page
            window.history.pushState({state:1,rand:Math.random()}, "State 1", "?"+$form.serialize());

            // イベント外す
            $formInputs.off('change', onInsightFormChange);
            $.ajax({
                type: 'GET',
                url: ajax_url,
                data: $form.serialize(),
                dataType: 'json',
                timeout: 60000, //60 sec
                success: function (data) {
                    if (data.html) {
                        $result.html(data.html);
                        options.afterSuccess();
                    }
                    $formInputs.on('change', onInsightFormChange);

                    // this code block is related to sorting by column on circle insight page
                    $('.table .insight-circle-table-header th').on('click', function(){
                        var $th = $( this );
                        var name_arr = $th.attr( "id" ).split('_header');
                        // setting sortby value
                        var sort_by_old = $('#TeamInsightSortBy').val();
                        $('#TeamInsightSortBy').val(name_arr[0]);

                        // setting the sort type by default starts from desc
                        if (sort_by_old !== name_arr[0]) {
                            $('#TeamInsightSortType').val('desc');
                        } else {
                            if ($('#TeamInsightSortType').val() == 'desc') {
                                $('#TeamInsightSortType').val('asc');
                            } else {
                                $('#TeamInsightSortType').val('desc');
                            }
                        }

                        createCallback(cake.url.insight_circle, 'InsightCircleResult')();
                    });
                }
            });
        };
        return onInsightFormChange;
    };


    /////////////////////////////////////////////////////
    // インサイト
    /////////////////////////////////////////////////////
    // グラフデータ
    var insightGraphData = [];
    var insightGraphDataCache = {};
    // グラフ描画オプション
    var insightGraphOptions = {
        autoscale: true,
        xaxis: {
            ticks: []
        },
        yaxis: {
            min: 0,
            tickDecimals: 0
        },
        series: {
            lines: {show: true, fill: true},
            points: {show: true}
        },
        grid: {
            borderWidth: 1,
            borderColor: '#a1a1a1',
            hoverable: true
        }
    };

    // グラフ用データをサーバから取得
    var loadGraphData = function (options) {
        options = $.extend({
            afterSuccess: function() {
            }
        }, options);

        var $form = $('#InsightForm');
        var serialized = $form.serialize();

        // キャッシュにデータがあればそれを使う
        if (insightGraphDataCache[serialized]) {
            insightGraphData = insightGraphDataCache[serialized];
            options.afterSuccess();
            return true;
        }

        $.ajax({
            type: 'GET',
            url: cake.url.insight_graph,
            data: serialized,
            dataType: 'json',
            timeout: 60000, //60 sec
            success: function (data) {
                if (data.insights) {
                    insightGraphDataCache[serialized] = data.insights;
                    insightGraphData = insightGraphDataCache[serialized];
                    options.afterSuccess();
                }
            }
        });
    };

    // グラフ再描画
    var redrawGraph = function () {
        // グラフ描画オプションに横軸ラベルを追加
        // 先頭とラストの項目は空、それ以外は日付
        insightGraphOptions.xaxis.ticks = [];
        var monthNameShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for (var i = 0; i < insightGraphData.length; i++) {
            var label = '';
            if (!(i == 0 || i == insightGraphData.length - 1)) {
                var endDate = new Date(insightGraphData[i].end_date);
                label = monthNameShort[endDate.getMonth()] + '. ' + endDate.getDate();
            }
            insightGraphOptions.xaxis.ticks.push([i, label]);
        }

        var items = [
            'user_count',
            'access_user_count',
            'action_count',
            'action_user_count',
            'post_count',
            'post_user_count',
            'like_count',
            'like_user_count',
            'comment_count',
            'comment_user_count',
            'message_count',
            'message_user_count',
            'access_user_percent',
            'action_user_percent',
            'post_user_percent',
            'like_user_percent',
            'comment_user_percent',
            'message_user_percent'
        ];
        for (i = 0; i < items.length; i++) {
            var data = [];
            var d1 = [];
            for (var j = 0; j < insightGraphData.length; j++) {
                d1.push([j, insightGraphData[j][items[i]]]);
            }
            data.push({
                data: d1,
                color: '#aaaaff'
            });

            var $graphContainer = $('#' + items[i] + '_item').closest('.insight-row').find('.insight-graph-container');
            if ($graphContainer.is(':visible')) {
                $.plot($graphContainer, data, insightGraphOptions);
                $graphContainer.off('plothover').on('plothover', _onPlotHover);
            }
        }
    };

    // グラフのポイントにマウスオーバーしたときのコールバック
    var _onPlotHover = function (event, pos, item) {
        var $tooltip = $("#InsightGraphTooltip");
        if (item) {
            var y = item.datapoint[1];
            $tooltip.html(y)
                .css({top: item.pageY-40, left: item.pageX-27})
                .fadeIn(200);
        } else {
            $tooltip.hide();
        }
    };

    // グラフ種類のボタンの有効/無効を調整
    var toggleGraphTypeButton = function () {
        var $buttonGroup = $('#InsightGraphTypeButtonGroup');
        var $buttons = $buttonGroup.find('.insight-graph-type-button');

        // 全てのボタンを一度 disabled
        $buttons.attr('disabled', 'disabled').button('reset');
        // 選択されているグラフ種類
        var graphType = $buttons.filter('.active').attr('data-value');
        // 選択されている期間
        var dateRange = $('#InsightInputDateRange').val();

        // 今週、先週の場合
        // 週、日 グラフを有効にする
        if (dateRange.indexOf('week') != -1) {
            $buttonGroup.find('label[data-value=week], label[data-value=day]').removeAttr('disabled');
            graphType = 'week';
        }
        // 今月、先月の場合
        // 月、週 グラフを有効にする
        else if (dateRange.indexOf('month') != -1) {
            $buttonGroup.find('label[data-value=month], label[data-value=week]').removeAttr('disabled');
            graphType = 'month';
        }
        // 今期、前期の場合
        // 期、月 グラフを有効にする
        else {
            $buttonGroup.find('label[data-value=term], label[data-value=month]').removeAttr('disabled');
            graphType = 'term';
        }
        $buttonGroup.find('label[data-value=' + graphType + ']').button('toggle');
    };

    // セットアップ
    var setupInsight = function () {

        // ウィンドウサイズを変更したとき
        $(window).on('resize', function (event) {
            redrawGraph();
        });

        // グラフの種類（期、月、週、日）を変更した時
        $(document).on('change', '#InsightGraphTypeButtonGroup input[type=radio]', function () {
            loadGraphData({
                afterSuccess: function () {
                    redrawGraph();
                }
            });
        });

        // トータル件数/ユニーク件数 切り替え
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
            redrawGraph();
        });

        $formInputs.on('change', createCallback(cake.url.insight, 'InsightResult', {
            afterSuccess: function () {
                toggleGraphTypeButton();
            }
        }));

        // グラフのポイントの値を表示する tooltip
        $('<div id="InsightGraphTooltip" class="insight-graph-tooltip"></div>').appendTo("body");
    };



    /////////////////////////////////////////////////////
    // サークル利用状況 セットアップ
    /////////////////////////////////////////////////////
    var setupCircle = function () {
        $formInputs.on('change', createCallback(cake.url.insight_circle, 'InsightCircleResult'));
    };

    /////////////////////////////////////////////////////
    // ランキング セットアップ
    /////////////////////////////////////////////////////
    var setupRanking = function () {
        $formInputs.on('change', createCallback(cake.url.insight_ranking, 'InsightRankingResult'));
    };

    var reload = function () {
        $formInputs.trigger('change');
    };


    return {
        reload: reload,
        insight: {
            setup: setupInsight,
            redrawGraph: redrawGraph
        },
        circle: {
            setup: setupCircle
        },
        ranking: {
            setup: setupRanking
        }
    };
});
