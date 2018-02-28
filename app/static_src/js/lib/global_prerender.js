"use strict";

function isBrowserIE() {
    // decide if IE or not, if contains 'trident' it is IE
    // FYI: http://www.useragentstring.com/pages/useragentstring.php?name=Internet+Explorer
    return window.navigator.userAgent.toLowerCase().indexOf('trident') != -1;
}
