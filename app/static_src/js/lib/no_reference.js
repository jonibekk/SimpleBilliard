

function ajaxAppendCount(id, url) {
    console.log("no_reference.js: ajaxAppendCount");
    var $loader_html = $('<i class="fa fa-refresh fa-spin"></i>');
    $('#' + id).append($loader_html);
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        dataType: 'json',
        success: function (data) {
            //ローダーを削除
            $loader_html.remove();
            //カウント数を表示
            $('#' + id).text(data.count);
        },
        error: function () {
        }
    });
    return false;
}

function enabledAllInput(selector) {
    console.log("no_reference.js: enabledAllInput");
    $(selector).find('input,select,textarea').removeAttr('disabled');
}

function disabledAllInput(selector) {
    console.log("no_reference.js: disabledAllInput");
    $(selector).find("input,select,textarea").attr('disabled', 'disabled');
}

// reset bell notify num call from app.
function resetBellNum() {
    console.log("no_reference.js: resetBellNum");
    initBellNum();
    var url = cake.url.g;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            updateNotifyCnt();
        },
        error: function () {
            // do nothing.
        }
    });
}

// reset bell message num call from app.
function resetMessageNum() {
    console.log("no_reference.js: resetMessageNum");
    initMessageNum();
    var url = cake.url.ag;
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        success: function (data) {
            // do nothing.
        },
        error: function () {
            // do nothing.
        }
    });
}

function isOnline() {
    console.log("no_reference.js: isOnline");
    return Boolean(network_reachable);
}