define(function () {
    // ヘッダーの検索ボックス処理
    var headerSearch = {
        setup: function () {
            var $NavSearchForm = $('#NavSearchForm');
            var $NavSearchInput = $('#NavSearchInput');
            var $NavSearchResults = $('#NavSearchResults');
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

            $NavSearchForm
                // Enter 押しても submit させないようにする
                .on('submit', function (e) {
                    e.preventDefault();
                    return false;
                });
                // // 検索種類切り替え（ユーザー、ゴール、サークル）
                // .find('.nav-search-category-item').on('click', function () {
                //     var $this = $(this);
                //     //var category = $this.attr('data-category');
                //     //$NavSearchInput.attr('placeholder', config[category].placeholder);
                //     $NavSearchForm.find('.nav-search-category-icon')
                //         .hide()
                //         //.filter('[data-category=' + category + ']')
                //         .show();
                // });

            $NavSearchInput
                .attr('placeholder', config.user.placeholder)
                .on('keydown', function (e) {
                    // down
                    if (e.keyCode == 40) {
                        e.preventDefault();
                        $NavSearchResults.find('.nav-search-result-item:first').focus();
                    }
                })
                .on('keyup', function (e) {
                    // 検索文字列
                    var inputText = $(this).val();
                    if(inputText.length){
                        $("#NavSearchInputToggleClear,#NavSearchInputClear").show();
                    } else {
                        $("#NavSearchInputToggleClear,#NavSearchInputClear").hide();
                    }

                    // キー連打考慮してすこし遅らせて ajax リクエストする
                    clearTimeout(keyupTimer);
                    keyupTimer = setTimeout(function () {
                        // 入力テキストが空
                        if (inputText.length == 0) {
                            $NavSearchResults.hide();
                            return;
                        }

                        var ajaxCallbackUser = function (res) {
                            cache['user'][inputText] = res;
                        };
                        var ajaxCallbackGoal = function (res) {
                            cache['goal'][inputText] = res;
                        };
                        var ajaxCallbackCircle = function (res) {
                            cache['circle'][inputText] = res;
                        };

                        $NavSearchResults.empty();
                        if(!$('#notFoundElement').length){
                            var $notFoundText = $('<div id="notFoundElement">')
                                .text(cake.message.notice.search_result_zero)
                                .addClass('nav-search-result-notfound');
                            $notFoundText.appendTo($NavSearchResults);
                        }

                        if (cache['user'][inputText]) {
                            var ajaxUser = ajaxCallbackUser(cache['user'][inputText]);
                        }
                        else {
                            var ajaxUser = $.get(config['user'].url, {
                                term: inputText,
                                page_limit: 10
                            }, ajaxCallbackUser);
                        }

                        if (cache['goal'][inputText]) {
                            var ajaxGoal = ajaxCallbackCircle(cache['goal'][inputText]);
                        }
                        else {
                            var ajaxGoal = $.get(config['goal'].url, {
                                term: inputText,
                                page_limit: 10
                            }, ajaxCallbackGoal);
                        }

                        if (cache['circle'][inputText]) {
                            var ajaxCircle = ajaxCallbackCircle(cache['circle'][inputText]);
                        }
                        else {
                            var ajaxCircle = $.get(config['circle'].url, {
                                term: inputText,
                                page_limit: 10
                            }, ajaxCallbackCircle);
                        }

                        $.when(ajaxUser, ajaxGoal, ajaxCircle).done(function(userResult, goalResult, circleResult){
                           // a1, a2 and a3 are arguments resolved 
                           // for the ajax1, ajax2 and ajax3 Ajax requests, respectively.

                           // Each argument is an array with the following structure:
                           // [ data, statusText, jqXHR ]
                           if (userResult && userResult[0].results) {
                                $('#notFoundElement').remove();
                                for (var i = 0; i < userResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('nav-search-result-item user-select')
                                        .attr('href', config['user'].link_base + userResult[0].results[i].id.split('_').pop());

                                    // image
                                    var $img = $('<img>').attr('src', userResult[0].results[i].image);
                                    $row.append($img);

                                    // text
                                    var $text = $('<span>').text(userResult[0].results[i].text);
                                    $row.append($text);
                                    $row.appendTo($NavSearchResults);
                                }
                            }
                            if (goalResult && goalResult[0].results) {
                                $('#notFoundElement').remove();
                                for (var i = 0; i < goalResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('nav-search-result-item goal-select')
                                        .attr('href', config['goal'].link_base + goalResult[0].results[i].id.split('_').pop());

                                    // image
                                    var $img = $('<img>').attr('src', goalResult[0].results[i].image);
                                    $row.append($img);

                                    // text
                                    var $text = $('<span>').text(goalResult[0].results[i].text);
                                    $row.append($text);
                                    $row.appendTo($NavSearchResults);
                                }
                            }
                           if (circleResult && circleResult[0].results) {
                                $('#notFoundElement').remove();
                                for (var i = 0; i < circleResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('nav-search-result-item circle-select')
                                        .attr('href', config['circle'].link_base + circleResult[0].results[i].id.split('_').pop());

                                    // image
                                    var $img = $('<img>').attr('src', circleResult[0].results[i].image);
                                    $row.append($img);

                                    // text
                                    var $text = $('<span>').text(circleResult[0].results[i].text);
                                    $row.append($text);
                                    $row.appendTo($NavSearchResults);
                                }
                            }

                            // ポップアップ下の画面をスクロールさせないようにする
                            $("body").addClass('nav-search-results-open');

                            // ポップアップクローズ用
                            $NavSearchResults.one('click', function () {
                                $NavSearchResults.hide();
                                $("body").removeClass('nav-search-results-open');
                            });
                            $NavSearchResults.show();
                        });
                    }, 150);
                });

            // 矢印キーで選択可能にする
            $NavSearchResults
                .on('keydown', '.nav-search-result-item', function (e) {
                    var $selectedItem = $NavSearchResults.find('.nav-search-result-item:focus');
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
        headerSearch: headerSearch
    };
});
