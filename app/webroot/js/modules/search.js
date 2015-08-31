define(function () {
    // ヘッダーの検索ボックス処理
    var headerSearch = {
        setup: function () {
            var $NavSearchForm = $('#NavSearchForm');
            var $NavSearchInput = $('#NavSearchInput');
            var $NavSearchResults = $('#NavSearchResults');
            var keyupTimer = null;
            var keypressCount = 0;

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

            $NavSearchForm
                // Enter 押しても submit させないようにする
                .on('submit', function (e) {
                    e.preventDefault();
                    return false;
                })
                // 検索種類切り替え（ユーザー、ゴール、サークル）
                .find('.nav-search-category-item').on('click', function () {
                    var $this = $(this);
                    var category = $this.attr('data-category');
                    $NavSearchInput.attr('placeholder', config[category].placeholder);
                    $NavSearchForm.find('.nav-search-category-icon')
                        .hide()
                        .filter('[data-category=' + category + ']')
                        .show();
                });

            $NavSearchInput
                .attr('placeholder', config.user.placeholder)
                .on('blur', function () {
                    // 検索結果ポップアップ消す
                    $(this).delay(200).queue(function () {
                        $NavSearchResults.hide();
                        $("body").removeClass('nav-search-results-open');
                        $(this).dequeue();
                    });
                })
                .on('keypress', function (e) {
                    // 日本語入力の未確定状態を判別
                    if (e.keyCode != 241 && e.keyCode != 242) {
                        keypressCount++;
                    }
                })
                .on('keyup', function (e) {
                    // 日本語入力の未確定状態を判別
                    keypressCount--;
                    if (keypressCount < 0) {
                        keypressCount = 0;
                        if (e.keyCode != 13 && e.keyCode != 8) {
                            // 日本語入力中（未確定）
                            return;
                        }
                    }

                    // 検索文字列
                    var inputText = $(this).val();

                    // キー連打考慮してすこし遅らせて ajax リクエストする
                    clearTimeout(keyupTimer);
                    keyupTimer = setTimeout(function () {
                        // 入力テキストが空
                        if (inputText.length == 0) {
                            $NavSearchResults.hide();
                            return;
                        }

                        var category = $NavSearchForm.find('.nav-search-category-icon:visible').attr('data-category');
                        $.get(config[category].url, {
                                term: inputText,
                                page_limit: 10
                            }, function (res) {
                                var $container = $('<div>');
                                $NavSearchResults.empty();
                                if (res.results) {
                                    if (res.results.length == 0) {
                                        var $notFoundText = $('<div>')
                                            .text('該当なし')
                                            .addClass('nav-search-result-notfound');
                                        $container.append($notFoundText);
                                    }
                                    else {
                                        for (var i = 0; i < res.results.length; i++) {
                                            var $row = $('<a>')
                                                .addClass('nav-search-result-item')
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
                                    $NavSearchResults.html($container).show();
                                    $("body").addClass('nav-search-results-open');
                                }
                            }
                        );
                    }, 150);
                });
        }
    };

    return {
        headerSearch: headerSearch
    };
})
;
