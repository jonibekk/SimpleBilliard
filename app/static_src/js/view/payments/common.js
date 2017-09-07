/**
 * Change the page with dropdown menu selection from
 * Payment method page.
 * @param sel
 */
function paymentMenuChanged(sel) {
    let value = sel.value;

    if(window.location.href !== '/payments/'+value){
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

/**
 * Show validation error on field
 * @param fieldName
 * @param message
 */
function setError(fieldName, message) {
    var field = document.querySelector('input[name=' + fieldName + ']');
    if (field) {
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