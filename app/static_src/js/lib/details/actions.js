"use strict";

$(function () {
    console.log("LOADING: actions.js");

    $(document).on("click", ".target-show", evTargetShow);
    $(document).on("click", ".click-this-remove", evRemoveThis);
});


/**
 * Show option fields on create Action form
 * @returns {boolean}
 */
function evTargetShow() {
    console.log("actions.js: evTargetShow");
    attrUndefinedCheck(this, 'target-id');
    var $obj = $(this);
    var target_id = $obj.attr("target-id");
    $("#" + target_id).show();
    return false;
}

function evRemoveThis() {
    console.log("actions.js: evRemoveThis");
    $(this).remove();
}
