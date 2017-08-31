/**
 * Change the page with dropdown menu selection from
 * Payment method page.
 * @param sel
 */
function paymentMenuChanged(sel) {
    let value = sel.value;
    console.log(value)

    if (value === 'method') {
        window.location.href = '/payments/method';
    }
    else if (value === 'history') {
        window.location.href = '/payments/history';
    }
    else if (value === 'subscription') {

    }
    else if (value === 'settings') {

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