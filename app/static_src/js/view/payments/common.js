/**
 * Change the page with dropdown menu selection from
 * Payment method page.
 * @param sel
 */
function paymentMenuChanged(sel) {
    var value = sel.value;

    if (window.location.href !== '/payments/' + value) {
        window.location.href = '/payments/' + value;
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

/**
 * Show validation error on field
 * @param fieldName
 * @param message
 */
function setError(fieldName, message) {
    var field = document.querySelector('input[name=' + fieldName + ']');
    if (field && field.parentNode.className.indexOf('has-error') === -1) {
        field.parentNode.className += ' has-error';
        var error = document.createElement('small');
        error.className = 'help-block';
        error.innerHTML = message;
        field.parentNode.appendChild(error);
    }
}

/**
 * Remove validation error from field
 * @param e
 */
function removeError(e) {
    var field = e.target;
    field.parentNode.className = field.parentNode.className.replace('has-error', '');
    var error = field.parentNode.querySelector('small');
    if (error) {
        error.remove();
    }
}

/**
 * Remove all error from a element and its child
 * @param element
 */
function removeAllErrors(element) {
    $(element).find('.help-block').remove();
    $(element).find('.has-error').removeClass('has-error');
}

/**
 * Check if a string is Zenkaku Katakana
 * @param str
 * @returns {boolean}
 */
function isZenKatakana(str) {
    str = (str == null) ? "" : str;
    // Mach katakana characters and zenkaku white space.
    if (str.match(/^[ァ-ヶー　]*$/)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Return the key code from a javascript key event.
 * @param e
 * @returns {*}
 */
function getKeyCode(e) {
    e = (e) ? e : window.event;
    return (e.which) ? e.which : e.keyCode;
}

/**
 * Accept numbers and + symbol
 * @param e
 * @returns {boolean}
 */
function isPhoneNumber(e) {
    var charCode = getKeyCode(e);
    // Char code in ASCII http://ascii.cl
    // 43 = '+', 48 = '0', 57 = '9'
    if (charCode === 43 || (charCode > 47 && charCode < 58)) {
        return true;
    }
    e.returnValue = false;
    return false;
}

/**
 * Accept only numbers
 * @param e
 * @returns {boolean}
 */
function isNumber(e) {
    var charCode = getKeyCode(e);
    // Char code in ASCII http://ascii.cl
    // 43 = '+', 48 = '0', 57 = '9'
    // Less then 32 are special characters
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        e.returnValue = false;
        return false;
    }
    return true;
}