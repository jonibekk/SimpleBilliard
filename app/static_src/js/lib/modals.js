"use strict";

$(function () {
    // TODO: Remove console log
    console.log("LOADING: modals.js");

    $(document).on('lightbox.open', 'a[rel^=lightbox]', function () {
        // TODO: Remove console log
        console.log("modals.js: lightbox.open");

        var $viewport = $("meta[name='viewport']");
        $viewport.attr('content', $viewport.attr('content')
            .replace('user-scalable=no', 'user-scalable=yes')
            .replace('maximum-scale=1', 'maximum-scale=10'));

    });
    $(document).on('lightbox.close', 'a[rel^=lightbox]', function () {
        // TODO: Remove console log
        console.log("modals.js: lightbox.close");

        var $viewport = $("meta[name='viewport']");
        $viewport.attr('content', $viewport.attr('content')
            .replace('user-scalable=yes', 'user-scalable=no')
            .replace('maximum-scale=10', 'maximum-scale=1'));
    });
});
