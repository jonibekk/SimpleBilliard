
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