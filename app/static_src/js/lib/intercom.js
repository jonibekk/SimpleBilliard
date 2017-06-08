/**
 * Intercom initialization
 */
"use strict";

var enabled_intercom_icon =
    (typeof enabled_intercom_icon === "undefined") ? null : enabled_intercom_icon;

$(document).ready(function () {
    // TODO: Remove console log
    console.log("LOADING: intercom.js");

    //intercomのリンクを非表示にする
    if (enabled_intercom_icon) {
        $('#IntercomLink').hide();
    }
});