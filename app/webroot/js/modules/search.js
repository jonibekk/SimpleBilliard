define(function () {
    // ヘッダーの検索ボックス処理
    var headerSearch = {
        setup: function () {
            var current, currentIndex;
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
                    label: cake.word.members
                },
                goal: {
                    url: cake.url.select2_goals,
                    link_base: cake.url.goal_page,
                    label: cake.word.goals
                },
                circle: {
                    url: cake.url.select2_circles,
                    link_base: cake.url.circle_page,
                    label: cake.word.circles
                }
            };

            // $NavSearchForm
            //     // Enter 押しても submit させないようにする
            //     .on('submit', function (e) {
            //         e.preventDefault();
            //         return false;
            //     });

            $NavSearchInput
                .on('keydown', function (e) {
                    var $selectedItems = $('.search-list-item-link');
                    if ($selectedItems.length) {
                        var code = e.keyCode || e.which;
                        if(!currentIndex) {
                            currentIndex = $selectedItems.first().index() - 1;
                        }
                        switch (code) {
                            // up
                            case 38:
                                e.preventDefault();
                                if(currentIndex > 0) {
                                    currentIndex--;
                                    if(currentIndex >= 0 && currentIndex < $selectedItems.length) {
                                        if(currentIndex < $selectedItems.length - 1) {
                                            $selectedItems[currentIndex + 1].style.backgroundColor = "#fff";
                                        }
                                        current = $selectedItems[currentIndex];
                                        current.scrollIntoView();
                                        current.style.backgroundColor = "#fff0f1";
                                    } else {
                                        currentIndex++;
                                    }
                                }
                                break;
                            // down
                            case 40:
                                e.preventDefault();
                                if(currentIndex < $selectedItems.length) {
                                    currentIndex++;
                                    if(currentIndex >= 0 && currentIndex < $selectedItems.length) {
                                        if(currentIndex >= 1) {
                                            $selectedItems[currentIndex - 1].style.backgroundColor = "#fff";
                                        }
                                        current = $selectedItems[currentIndex];
                                        current.style.backgroundColor = "#fff0f1";
                                        current.scrollIntoView();
                                    } else {
                                        currentIndex--;
                                    }
                                }                                
                                break;
                            //Enter
                            case 13:
                                e.preventDefault();
                                if(current){
                                    window.location = current.href;
                                }
                                break;
                        }
                    }
                })
                .on('keyup', function (e) {
                    // 検索文字列
                    var inputText = $(this).val();
                    if(inputText.length){
                        $("#NavSearchInputClear").show();
                    } else {
                        $("#NavSearchInputClear").hide();
                    }

                    // キー連打考慮してすこし遅らせて ajax リクエストする
                    // clearTimeout(keyupTimer);
                    // keyupTimer = setTimeout(function () {
                        // 入力テキストが空
                        if (inputText.length == 0) {
                            $NavSearchResults.hide();
                            return;
                        }

                        var ajaxUser = $.get(config['user'].url, {
                            term: inputText,
                            page_limit: 10
                        });

                        var ajaxGoal = $.get(config['goal'].url, {
                            term: inputText,
                            page_limit: 10
                        });

                        var ajaxCircle = $.get(config['circle'].url, {
                            term: inputText,
                            page_limit: 10
                        });

                        $.when(ajaxUser, ajaxGoal, ajaxCircle).done(function(userResult, goalResult, circleResult){
                           // a1, a2 and a3 are arguments resolved 
                           // for the ajax1, ajax2 and ajax3 Ajax requests, respectively.

                           // Each argument is an array with the following structure:
                           // [ data, statusText, jqXHR ]
                            var $notFoundText = $('<div id="notFoundElement">')
                                .text(cake.message.notice.search_result_zero)
                                .addClass('nav-search-result-notfound');
                            $NavSearchResults.empty().append($notFoundText);

                           if (userResult && userResult[0].results && userResult[0].results && userResult[0].results.length) {
                                $('#notFoundElement').remove();
                                var $userLabel = $('<div>')
                                    .text(config['user'].label)
                                    .addClass('nav-search-result-label');
                                $NavSearchResults.append($userLabel);
                                for (var i = 0; i < userResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('search-list-item-link')
                                        .attr('href', config['user'].link_base + userResult[0].results[i].id.split('_').pop());
                                    // image
                                    var $divImage = $('<div>')
                                        .addClass('search-list-avatar-item');
                                    var $img = $('<img>').attr('src', userResult[0].results[i].image);
                                    $divImage.append($img);
                                    $row.append($divImage);
                                    // text
                                    var $divText = $('<div>')
                                        .addClass('topic-search-list-item-main');
                                    var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(userResult[0].results[i].text);
                                    $divText.append($text);
                                    $row.append($divText);

                                    $row.appendTo($NavSearchResults);
                                }
                            }
                            if (goalResult && goalResult[0].results && goalResult[0].results && goalResult[0].results.length) {
                                $('#notFoundElement').remove();
                                var $goalLabel = $('<div>')
                                    .text(config['goal'].label)
                                    .addClass('nav-search-result-label');
                                $NavSearchResults.append($goalLabel);
                                for (var i = 0; i < goalResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('search-list-item-link')
                                        .attr('href', config['goal'].link_base + goalResult[0].results[i].id.split('_').pop());
                                    // image
                                    var $divImage = $('<div>')
                                        .addClass('search-list-avatar-item');
                                    var $img = $('<img>').attr('src', goalResult[0].results[i].image);
                                    $divImage.append($img);
                                    $row.append($divImage);
                                    // text
                                    var $divText = $('<div>')
                                        .addClass('topic-search-list-item-main');
                                    var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(goalResult[0].results[i].text);
                                    $divText.append($text);
                                    $row.append($divText);

                                    $row.appendTo($NavSearchResults);
                                }
                            }
                           if (circleResult && circleResult[0].results && circleResult[0].results.length && circleResult[0].results.length) {
                                $('#notFoundElement').remove();
                                var $circleLabel = $('<div>')
                                    .text(config['circle'].label)
                                    .addClass('nav-search-result-label');
                                $NavSearchResults.append($circleLabel);
                                for (var i = 0; i < circleResult[0].results.length; i++) {
                                    var $row = $('<a>')
                                        .addClass('search-list-item-link')
                                        .attr('href', config['circle'].link_base + circleResult[0].results[i].id.split('_').pop());
                                    // image
                                    var $divImage = $('<div>')
                                        .addClass('search-list-avatar-item');
                                    var $img = $('<img>').attr('src', circleResult[0].results[i].image);
                                    $divImage.append($img);
                                    $row.append($divImage);
                                    // text
                                    var $divText = $('<div>')
                                        .addClass('topic-search-list-item-main');
                                    var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(circleResult[0].results[i].text);
                                    $divText.append($text);
                                    $row.append($divText);

                                    $row.appendTo($NavSearchResults);
                                }
                            }

                            if(!$('#notFoundElement').length){
                                var $endLabel = $('<div>')
                                    .text(cake.word.end_search)
                                    .addClass('nav-search-result-end-label');
                                $NavSearchResults.append($endLabel);
                            } else {
                                var $noResultsLabel = $('<div>')
                                    .text(cake.word.no_results)
                                    .addClass('nav-search-result-end-label');
                                $NavSearchResults.append($noResultsLabel);
                            }

                            // ポップアップ下の画面をスクロールさせないようにする
                            $("body").addClass('nav-search-results-open');

                            // ポップアップクローズ用
                            $NavSearchResults.one('click', function () {
                                $NavSearchResults.hide();
                                $("body").removeClass('nav-search-results-open');
                            });
                            $(".nav-search-result-label,.nav-search-result-end-label,.nav-search-result-notfound").off("click").on("click", function(e) {
                                e.preventDefault();
                                return false;
                            });
                            $NavSearchResults.show();
                        });
                    // }, 150);
                });

            // // 矢印キーで選択可能にする
            // $NavSearchResults
            //     .on('keydown', '.nav-search-result-item', function (e) {
            //         var $selectedItems = $('.search-list-item-link');
            //         if ($selectedItems.length) {
            //             var code = e.keyCode || e.which;
            //             if(!currentIndex) {
            //                 currentIndex = $selectedItems.first().index();
            //             }
            //             switch (code) {
            //                 // up
            //                 case 38:
            //                     e.preventDefault();
            //                     if(currentIndex > 1) {
            //                         currentIndex--;
            //                         if(currentIndex >= 1){
            //                             current = $selectedItems[currentIndex];
            //                             $(current).css("background-color")
            //                             current.scrollIntoView();
            //                         }
            //                         console.log(currentIndex);
            //                     }
            //                     break;
            //                 // down
            //                 case 40:
            //                     e.preventDefault();
            //                     if(currentIndex < $selectedItems.length) {
            //                         currentIndex++;
            //                         if(currentIndex < $selectedItems.length){
            //                             current = $selectedItems[currentIndex];
            //                             current.scrollIntoView();
            //                         }
            //                         console.log(currentIndex);
            //                     }                                
            //                     break;
            //             }
            //         }
            //     });
        }
    };

    return {
        headerSearch: headerSearch
    };
});
