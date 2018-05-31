define(function () {
    // ヘッダーの検索ボックス処理
    var headerSearch = {
        setup: function () {
            var lastHitCount = 1;
            var current;
            var currentIndex = -1;
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
                    link_base: cake.url.user_page,
                    label: cake.word.members
                },
                goal: {
                    link_base: cake.url.goal_page,
                    label: cake.word.goals
                },
                circle: {
                    link_base: cake.url.circle_page,
                    label: cake.word.circles
                },
                general: {
                    url: cake.url.select_search,
                },
            };

            $NavSearchForm
                // Enter 押しても submit させないようにする
                .on('submit', function (e) {
                    if(cake.is_mb_app == "1" || cake.is_mb_browser == "1"){
                        $("#NavSearchInput").blur();
                        $("#NavSearchInput").focusout();
                    } 
                    e.preventDefault();
                    return false;
                });

            $NavSearchInput
                .on('keydown', function (e) {
                    var $selectedItems = $('.search-list-item-link');
                    if ($selectedItems.length) {
                        var code = e.keyCode || e.which;
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
                    var code = e.keyCode || e.which;
                    if(code === 38 || code === 40 || code === 13){
                        return;
                    }   

                    // 検索文字列
                    var inputText = $(this).val();

                    if(inputText.length){
                        $("#NavSearchInputClear").show();
                    } else {
                        $("#NavSearchInputClear").hide();
                    }

                     // When there is no last search result return unless backspace/delete pressed
                    if(lastHitCount < 1 && !(e.keyCode === 8 || e.keyCode === 46)) {
                        return;
                    }

                    // キー連打考慮してすこし遅らせて ajax リクエストする
                    // clearTimeout(keyupTimer);
                    // keyupTimer = setTimeout(function () {
                    // 入力テキストが空
                    if (inputText.length == 0) {
                        $NavSearchResults.hide();
                        return;
                    }

                    current = null;
                    currentIndex = -1;

                    var ajaxResults = $.get(config['general'].url, {
                        term: inputText,
                        page_limit: 10
                    });

                    $.when(ajaxResults).done(function(allResults){
                       // a1, a2 and a3 are arguments resolved 
                       // for the ajax1, ajax2 and ajax3 Ajax requests, respectively.

                       // Each argument is an array with the following structure:
                       // [ data, statusText, jqXHR ]
                        var $notFoundText = $('<div id="notFoundElement">')
                            .text(cake.message.notice.search_result_zero)
                            .addClass('nav-search-result-notfound');
                        $NavSearchResults.empty().append($notFoundText);

                        userResult = [];
                        goalResult = [];
                        circleResult = [];
                        if(allResults){
                            userResult = allResults.results_users.results;
                            goalResult = allResults.results_goals.results;
                            circleResult = allResults.results_circles.results;
                        }

                        lastHitCount = userResult.length + goalResult.length + circleResult.length;

                        if (userResult && userResult.length) {
                            $('#notFoundElement').remove();
                            var $userLabel = $('<div>')
                                .text(config['user'].label)
                                .addClass('nav-search-result-label');
                            $NavSearchResults.append($userLabel);
                            for (var i = 0; i < userResult.length; i++) {
                                var $row = $('<a>')
                                    .addClass('search-list-item-link')
                                    .attr('href', config['user'].link_base + userResult[i].id.split('_').pop());
                                // image
                                var $divImage = $('<div>')
                                    .addClass('search-list-avatar-item');
                                var $img = $('<img>').attr('src', userResult[i].image);
                                $divImage.append($img);
                                $row.append($divImage);
                                // text
                                var $divText = $('<div>')
                                    .addClass('topic-search-list-item-main');
                                var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(userResult[i].text);
                                $divText.append($text);
                                $row.append($divText);

                                $row.appendTo($NavSearchResults);
                            }
                        }
                        if (goalResult && goalResult.length) {
                            $('#notFoundElement').remove();
                            var $goalLabel = $('<div>')
                                .text(config['goal'].label)
                                .addClass('nav-search-result-label');
                            $NavSearchResults.append($goalLabel);
                            for (var i = 0; i < goalResult.length; i++) {
                                var $row = $('<a>')
                                    .addClass('search-list-item-link')
                                    .attr('href', config['goal'].link_base + goalResult[i].id.split('_').pop());
                                // image
                                var $divImage = $('<div>')
                                    .addClass('search-list-avatar-item');
                                var $img = $('<img>').attr('src', goalResult[i].image);
                                $divImage.append($img);
                                $row.append($divImage);
                                // text
                                var $divText = $('<div>')
                                    .addClass('topic-search-list-item-main');
                                var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(goalResult[i].text);
                                $divText.append($text);
                                $row.append($divText);

                                $row.appendTo($NavSearchResults);
                            }
                        }
                       if (circleResult && circleResult.length) {
                            $('#notFoundElement').remove();
                            var $circleLabel = $('<div>')
                                .text(config['circle'].label)
                                .addClass('nav-search-result-label');
                            $NavSearchResults.append($circleLabel);
                            for (var i = 0; i < circleResult.length; i++) {
                                var $row = $('<a>')
                                    .addClass('search-list-item-link')
                                    .attr('href', config['circle'].link_base + circleResult[i].id.split('_').pop());
                                // image
                                var $divImage = $('<div>')
                                    .addClass('search-list-avatar-item');
                                var $img = $('<img>').attr('src', circleResult[i].image);
                                $divImage.append($img);
                                $row.append($divImage);
                                // text
                                var $divText = $('<div>')
                                    .addClass('topic-search-list-item-main');
                                var $text = $('<div>').addClass('topic-search-list-item-main-header-title').text(circleResult[i].text);
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

                        // ポップアップクローズ用
                        $NavSearchResults.one('click', function () {
                            $NavSearchResults.hide();
                        });
                        $(".nav-search-result-label,.nav-search-result-end-label,.nav-search-result-notfound").off("click").on("click", function(e) {
                            e.preventDefault();
                            return false;
                        });
                        $NavSearchResults.show();
                    });
                    // }, 150);
                });
        }
    };

    return {
        headerSearch: headerSearch
    };
});
