define(function () {
    // ヘッダーの検索ボックス処理
    var headerSearchToggle = {
        setup: function () {
            var $NavSearchFormToggle = $('#NavSearchFormToggle');
            var $NavSearchInputToggle = $('#NavSearchInputToggle');
            var $NavSearchResultsToggle = $('#NavSearchResultsToggle');
            var keyupTimer = null;
            var cache = {
                user: {},
                goal: {},
                circle: {}
            };

            var config = {
                user: {
                    url: cake.url.a,
                    link_base: cake.url.user_page,
                    placeholder: cake.word.search_placeholder_user
                },
                goal: {
                    url: cake.url.select2_goals,
                    link_base: cake.url.goal_page,
                    placeholder: cake.word.search_placeholder_goal
                },
                circle: {
                    url: cake.url.select2_circles,
                    link_base: cake.url.circle_page,
                    placeholder: cake.word.search_placeholder_circle
                }
            };

            $NavSearchFormToggle
                // Enter 押しても submit させないようにする
                .on('submit', function (e) {
                    e.preventDefault();
                    return false;
                });
                // // 検索種類切り替え（ユーザー、ゴール、サークル）
                // .find('.nav-search-category-item').on('click', function () {
                //     var $this = $(this);
                //     //var category = $this.attr('data-category');
                //     //$NavSearchInputToggle.attr('placeholder', config[category].placeholder);
                //     $NavSearchFormToggle.find('.nav-search-category-icon')
                //         .hide()
                //         //.filter('[data-category=' + category + ']')
                //         .show();
                // });

            $NavSearchInputToggle
                .attr('placeholder', config.user.placeholder)
                .on('keydown', function (e) {
                    // down
                    if (e.keyCode == 40) {
                        e.preventDefault();
                        $NavSearchResultsToggle.find('.nav-search-result-item:first').focus();
                    }
                })
                .on('keyup', function (e) {
                    // 検索文字列
                    var inputText = $(this).val();

                    // キー連打考慮してすこし遅らせて ajax リクエストする
                    clearTimeout(keyupTimer);
                    keyupTimer = setTimeout(function () {
                        // 入力テキストが空
                        if (inputText.length == 0) {
                            $NavSearchResultsToggle.hide();
                            return;
                        }

                        var category = 'user';
                        var ajaxCallback = function (res) {
                            cache[category][inputText] = res;

                            var $container = $('<div>');
                            $NavSearchResultsToggle.empty();
                            if (res.results) {
                                if (res.results.length == 0) {
                                    var $notFoundText = $('<div>')
                                        .text(cake.message.notice.search_result_zero)
                                        .addClass('nav-search-result-notfound');
                                    $container.append($notFoundText);
                                }
                                else {
                                    for (var i = 0; i < res.results.length; i++) {
                                        var $row = $('<a>')
                                            .addClass('nav-search-toggle-result-item')
                                            .attr('href', config[category].link_base + res.results[i].id.split('_').pop());

                                        // image
                                        var $img = $('<img>').attr('src', res.results[i].image);
                                        $row.append($img);

                                        // text
                                        var $text = $('<span>').text(res.results[i].text);
                                        $row.append($text);

                                        $container.append($row);
                                    }
                                }
                                $NavSearchResultsToggle.html($container).show();

                                // ポップアップ下の画面をスクロールさせないようにする
                                $("body").addClass('nav-search-results-open');

                                // ポップアップクローズ用
                                $(document).one('click', function () {
                                    $NavSearchResultsToggle.hide();
                                    $("body").removeClass('nav-search-results-open');
                                });
                            }
                        };

                        if (cache[category][inputText]) {
                            ajaxCallback(cache[category][inputText]);
                        }
                        else {
                            $.get(config[category].url, {
                                term: inputText,
                                page_limit: 10
                            }, ajaxCallback);
                        }
                    }, 150);
                });

            // 矢印キーで選択可能にする
            $NavSearchResultsToggle
                .on('keydown', '.nav-search-result-item', function (e) {
                    var $selectedItem = $NavSearchResultsToggle.find('.nav-search-result-item:focus');
                    if ($selectedItem.size()) {
                        switch (e.keyCode) {
                            // up
                            case 38:
                                e.preventDefault();
                                $selectedItem.prev().focus();
                                break;

                            // down
                            case 40:
                                e.preventDefault();
                                $selectedItem.next().focus();
                                break;
                        }
                    }
                });
        }
    };

    return {
        headerSearchToggle: headerSearchToggle
    };
});
