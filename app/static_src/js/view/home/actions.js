/**
 * This file contains script related to posting actions on feed page
 */


"use strict";

$(function () {
    // TODO: Remove console log
    console.log("LOADING: actions.js");

    $(document).on("click", ".target-show", evTargetShow);
    $(document).on("click", ".click-this-remove", evRemoveThis);

    // TODO: temporary fix for releasing
    //       should change js to less to manage action iamges size
    setTimeout(function(){
        Page.init();
    }, 1000); //fallback incase images render after page load

    $(window).resize(function () {
        window.Page.action_resize(true);
    });
});


/**
 * Show option fields on create Action form
 * @returns {boolean}
 */
function evTargetShow() {
    // TODO: Remove console log
    console.log("actions.js: evTargetShow");
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).show();
    return false;
}

function evRemoveThis() {
    // TODO: Remove console log
    console.log("actions.js: evRemoveThis");
    $(this).remove();
}
