
// reset bell notify num call from app.
function resetBellNum() {
    // TODO: Remove console log
    console.log("mobile_app.js: resetBellNum");
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
    // TODO: Remove console log
    console.log("mobile_app.js: resetMessageNum");
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
    // TODO: Remove console log
    console.log("mobile_app.js: isOnline");
    return Boolean(network_reachable);
}
