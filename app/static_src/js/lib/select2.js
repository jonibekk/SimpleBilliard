/**
 * Select2 related functions
 */

"use strict";

$(document).ready(function () {
    console.log("select2.js: $(document).ready");

    initMemberSelect2();
    initCircleSelect2();

    $.fn.select2.locales['en'] = {
        formatNoMatches: function () {
            return cake.word.d;
        },
        formatInputTooShort: function () {
            return cake.word.e;
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            return cake.word.g + n + cake.word.h;
        },
        formatSelectionTooBig: function (limit) {
            return cake.word.i + limit + cake.word.j;
        },
        formatLoadMore: function (pageNumber) {
            return cake.message.info.b;
        },
        formatSearching: function () {
            return cake.message.info.c;
        }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['en']);
});

//Select2 Customization
$.fn.select2.defaults = {
    width: "copy",
    loadMorePadding: 0,
    closeOnSelect: true,
    openOnEnter: true,
    containerCss: {},
    dropdownCss: {},
    containerCssClass: "",
    dropdownCssClass: "",
    formatResult: function (result, container, query, escapeMarkup) {
        var markup = [];
        markMatch(result.text, query.term, markup, escapeMarkup);
        return markup.join("");
    },
    formatSelection: function (data, container, escapeMarkup) {
        return data ? escapeMarkup(data.text) : undefined;
    },
    sortResults: function (results, container, query) {
        return results;
    },
    formatResultCssClass: function (data) {
        return data.css;
    },
    formatSelectionCssClass: function (data, container) {
        return undefined;
    },
    minimumResultsForSearch: 0,
    minimumInputLength: 0,
    maximumInputLength: null,
    maximumSelectionSize: 0,
    id: function (e) {
        return e == undefined ? null : e.id;
    },
    matcher: function (term, text) {
        return stripDiacritics('' + text).toUpperCase().indexOf(stripDiacritics('' + term).toUpperCase()) >= 0;
    },
    separator: ",",
    tokenSeparators: [],
    //tokenizer: defaultTokenizer,
    //escapeMarkup: defaultEscapeMarkup,
    blurOnChange: false,
    selectOnBlur: false,
    adaptContainerCssClass: function (c) {
        return c;
    },
    adaptDropdownCssClass: function (c) {
        return null;
    },
    nextSearchTerm: function (selectedObject, currentSearchTerm) {
        return undefined;
    },
    searchInputPlaceholder: '',
    createSearchChoicePosition: 'top',
    shouldFocusInput: function (instance) {
        // Attempt to detect touch devices
        var supportsTouchEvents = (('ontouchstart' in window) ||
        (navigator.msMaxTouchPoints > 0));

        // Only devices which support touch events should be special cased
        if (!supportsTouchEvents) {
            return true;
        }

        // Never focus the input if search is disabled
        if (instance.opts.minimumResultsForSearch < 0) {
            return false;
        }

        return true;
    }
};

$.fn.select2.locales = [];
$.fn.select2.locales['en'] = {
    formatMatches: function (matches) {
        if (matches === 1) {
            return "One result is available, press enter to select it.";
        }
        return matches + " results are available, use up and down arrow keys to navigate.";
    },
    formatNoMatches: function () {
        return "No matches found";
    },
    formatAjaxError: function (jqXHR, textStatus, errorThrown) {
        return "Loading failed";
    },
    formatInputTooShort: function (input, min) {
        var n = min - input.length;
        return "Please enter " + n + " or more character" + (n == 1 ? "" : "s");
    },
    formatInputTooLong: function (input, max) {
        var n = input.length - max;
        return "Please delete " + n + " character" + (n == 1 ? "" : "s");
    },
    formatSelectionTooBig: function (limit) {
        return "You can only select " + limit + " item" + (limit == 1 ? "" : "s");
    },
    formatLoadMore: function (pageNumber) {
        return "Loading more results…";
    },
    formatSearching: function () {
        return "Searching…";
    },
};

$.extend($.fn.select2.defaults, $.fn.select2.locales['en']);

$.fn.select2.ajaxDefaults = {
    transport: $.ajax,
    params: {
        type: "GET",
        cache: false,
        dataType: "json"
    }
};

// NO REFERENCE FOUND
function initMessageSelect2(topic_id) {
    console.log("select2.js: initMessageSelect2");
    //noinspection JSUnusedLocalSymbols post_detail.Post.id
    $('#selectOnlyMember').select2({
        multiple: true,
        minimumInputLength: 1,
        placeholder: cake.message.notice.b,
        ajax: {
            url: cake.url.add_member_on_message,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    topic_id: topic_id
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2Member"
    }).on('change', function () {
        var $this = $(this);
        if ($this.val() == '') {
            $('#MessageSubmit').attr('disabled', 'disabled');
        }
        else {
            if ($('#CommonMessageBody').val() != '') {
                $('#MessageSubmit').removeAttr('disabled');
            }
        }
        // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
        $this.select2('data', select2ExpandGroup($this.select2('data')));
    });
}

function initMemberSelect2() {
    console.log("select2.js: initMemberSelect2");
    //noinspection JSUnusedLocalSymbols
    $('#select2Member').select2({
        initSelection: function (element, callback) {
            // user_**の文字列からユーザーIDを抽出
            if ($(element).val().match(/^user_(\d+)$/)) {
                var userId = RegExp.$1;
                // ユーザー情報を取得して初期表示
                $.ajax("/users/ajax_select2_get_user_detail/" + userId,
                    {
                        type: 'GET'
                    }).done(function (data) {
                    callback(data);
                });
            }
        },
        multiple: true,
        minimumInputLength: 1,
        placeholder: cake.message.notice.b,
        ajax: {
            url: cake.url.a,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    with_group: 1
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2Member"
    }).on('change', function () {
        var $this = $(this);
        if ($this.val() == '' || $('#CommonMessageBody').val() == '') {
            $('#MessageSubmit').attr('disabled', 'disabled');
        }
        else {
            $('#MessageSubmit').removeAttr('disabled');
        }
        // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
        $this.select2('data', select2ExpandGroup($this.select2('data')));
    });
}

function initCircleSelect2() {
    console.log("select2.js: initCircleSelect2");
    //noinspection JSUnusedLocalSymbols,JSDuplicatedDeclaration
    $('#select2PostCircleMember').select2({
        multiple: true,
        placeholder: cake.word.select_public_circle,
        minimumInputLength: 1,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: "public"
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.b,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2PostCircleMember"
    })
        .on('change', function () {
            var $this = $(this);
            // グループを選択した場合、グループに所属するユーザーを展開して入力済にする
            $this.select2('data', select2ExpandGroup($this.select2('data')));
        });

    // select2 秘密サークル選択
    $('#select2PostSecretCircle').select2({
        multiple: true,
        placeholder: cake.word.select_secret_circle,
        minimumInputLength: 1,
        maximumSelectionSize: 1,
        ajax: {
            url: cake.url.select2_secret_circle,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.select2_secret_circle,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2PostCircleMember"
    });

    //noinspection JSUnusedLocalSymbols,JSDuplicatedDeclaration
    $('#select2MessageCircleMember').select2({
        multiple: true,
        placeholder: cake.word.select_public_message,
        minimumInputLength: 2,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: "public"
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.b,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2MessageCircleMember"
    });

    // select2 秘密サークル選択
    $('#select2MessageSecretCircle').select2({
        multiple: true,
        placeholder: cake.word.select_secret_circle,
        minimumInputLength: 2,
        maximumSelectionSize: 1,
        ajax: {
            url: cake.url.select2_secret_circle,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.select2_secret_circle,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2MessageCircleMember"
    });

    // サークル追加用モーダルの select2 を設定
    bindSelect2Members($('#modal_add_circle'));

    // 投稿の共有範囲(公開/秘密)切り替えボタン
    var $shareRangeToggleButton = $('#postShareRangeToggleButton');
    var $shareRange = $('#postShareRange');
    var publicButtonLabel = '<i class="fa fa-unlock"></i> ' + cake.word.public;
    var secretButtonLabel = '<i class="fa fa-lock font_verydark"></i> ' + cake.word.secret;

    // ボタン初期状態
    $shareRangeToggleButton.html(($shareRange.val() == 'public') ? publicButtonLabel : secretButtonLabel);

    // 共有範囲切り替えボタンが有効な場合
    $shareRangeToggleButton.on('click', function (e) {
        e.preventDefault();
        if ($shareRangeToggleButton.attr('data-toggle-enabled')) {
            $shareRange.val($shareRange.val() == 'public' ? 'secret' : 'public');
            if ($shareRange.val() == 'public') {
                $shareRangeToggleButton.html(publicButtonLabel);
                $('#PostSecretShareInputWrap').hide();
                $('#PostPublicShareInputWrap').show();
            }
            else {
                $shareRangeToggleButton.html(secretButtonLabel);
                $('#PostPublicShareInputWrap').hide();
                $('#PostSecretShareInputWrap').show();
            }
        }
        else {
            // 共有範囲切り替えボタンが無効な場合（サークルフィードページ）
            $shareRangeToggleButton.popover({
                'data-toggle': "popover",
                'placement': 'top',
                'trigger': "focus",
                'content': cake.word.share_change_disabled,
                'container': 'body'
            });
        }
    });


    $('#select2ActionCircleMember').select2({
        multiple: true,
        placeholder: cake.word.select_notify_range,
        minimumInputLength: 1,
        ajax: {
            url: cake.url.select2_circle_user,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10, // page size
                    circle_type: 'all'
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        data: [],
        initSelection: cake.data.l,
        formatSelection: format,
        formatResult: format,
        dropdownCssClass: 's2-post-dropdown aaaa',
        escapeMarkup: function (m) {
            return m;
        },
        containerCssClass: "select2ActionCircleMember"
    });

}

function bindSelect2Members($this) {
    console.log("select2.js: bindSelect2Members");
    var $select2elem = $this.find(".ajax_add_select2_members");
    var url = $select2elem.attr('data-url');

    //noinspection JSUnusedLocalSymbols
    $select2elem.select2({
        'val': null,
        multiple: true,
        minimumInputLength: 1,
        placeholder: cake.message.notice.b,
        ajax: {
            url: url ? url : cake.url.a,
            dataType: 'json',
            quietMillis: 100,
            cache: true,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        },
        formatSelection: format,
        formatResult: format,
        escapeMarkup: function (m) {
            return m;
        }
    })
        .on('change', function () {
            var $this = $(this);
            // グループを選択した場合
            // グループに所属するユーザーを展開して入力済にする
            $this.select2('data', select2ExpandGroup($this.select2('data')));
        });
}

// select2 で選択されたグループをユーザーとして展開する
function select2ExpandGroup(data) {
    console.log("select2.js: select2ExpandGroup");
    for (var i = 0; i < data.length; i++) {
        if (data[i].id.indexOf('group_') === 0 && data[i].users) {
            var group = data.splice(i, 1)[0];
            for (var j = 0; j < group.users.length; j++) {
                data.push(group.users[j]);
            }
        }
    }
    return data;
};

function format(item) {
    console.log("select2.js: format");
    if ('image' in item) {
        return "<img style='width:14px;height: 14px' class='select2-item-img' src='" + item.image + "' alt='icon' /> " + "<span class='select2-item-txt'>" + item.text + "</span>";
    }
    else if ('icon' in item) {
        return "<span class='select2-item-txt-with-i'><i class='" + item.icon + "'></i> " + item.text + "</span>";
    }
    else {
        return "<span class='select2-item-txt'>" + item.text + "</span>";
    }
}