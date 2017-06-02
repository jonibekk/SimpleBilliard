"use strict";

function initCommentNotify(notifyBox) {
    console.log("gl_basic.js: initCommentNotify");
    var numInBox = notifyBox.find(".num");
    numInBox.html("0");
    notifyBox.css("display", "none").css("opacity", 0);
}