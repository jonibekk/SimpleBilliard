
// reset bell notify num call from app.
function resetBellNum() {
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

function isOnline() {
    return Boolean(network_reachable);
}
