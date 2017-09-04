
// Edit Invoice info
if(document.editInvoiceForm) {

    let fields = [
        'company_name',
        'company_post_code',
        'company_region',
        'company_city',
        'company_street',
        'contact_person_last_name',
        'contact_person_first_name',
        'contact_person_last_name_kana',
        'contact_person_first_name_kana',
        'contact_person_email',
        'contact_person_tel'
    ];

    let allFields = document.editInvoiceForm.querySelectorAll('input[type=text], input[type=email], input[type=tel]');
    for(var i = 0; i < allFields.length; i++) {
        allFields[i].addEventListener('change', removeError);
    }

    document.editInvoiceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var data = {
            'data[_Token][key]': cake.data.csrf_token.key
        };
        for(var i = 0; i < fields.length; i++) {
            data[fields[i]] = document.getElementsByName(fields[i])[0].value;
        }

        let xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/invoice');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Updated, redirect to payment pages
                new Noty({
                    type: 'success',
                    text: __("Update completed"),
                }).on('onClose', function() {
                    window.location.href = '/payments';
                }).show();
            }
            else {
                let response  = JSON.parse(xhr.response);
                // Validation errors
                if (response.validation_errors) {
                    let fields = Object.keys(response.validation_errors.payment_setting);
                    fields.forEach(function (item) {
                        setError(item, response.validation_errors.payment_setting[item]);
                    });
                } else {
                    // Any other error
                    new Noty({
                        type: 'error',
                        text: response.message ? response.message : xhr.statusText
                    }).show();
                }

            }
        };
        xhr.send(urlEncode(data));
    });
}