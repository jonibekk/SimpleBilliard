/**
 * Change the page with dropdown menu selection from
 * Payment method page.
 * @param sel
 */
function paymentMenuChanged(sel) {
    var value = sel.value;

    if(window.location.href != '/payments/'+value){
        window.location.href = '/payments/'+value;
    }
}

/**
 * Encode a javascript object as HTML url encoded string.
 * @param object
 * @returns {string}
 */
function urlEncode(object) {
    var encodedString = '';
    for (var prop in object) {
        if (object.hasOwnProperty(prop)) {
            if (encodedString.length > 0) {
                encodedString += '&';
            }
            encodedString += encodeURI(prop + '=' + object[prop]);
        }
    }
    return encodedString;
}
