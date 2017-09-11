
// Edit Invoice info
if(document.editPaySettingsForm) {

    var fields = [
        'company_name',
        'company_country',
        'company_post_code',
        'company_region',
        'company_city',
        'company_street',
        'company_tel',
        'contact_person_last_name',
        'contact_person_first_name',
        'contact_person_last_name_kana',
        'contact_person_first_name_kana',
        'contact_person_email',
        'contact_person_tel'
    ];

    var allFields = document.editPaySettingsForm.querySelectorAll('input[type=text], input[type=email], input[type=tel]');
    for(var i = 0; i < allFields.length; i++) {
        allFields[i].addEventListener('change', removeError);
    }

    document.editPaySettingsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        var data = {
            'data[_Token][key]': cake.data.csrf_token.key
        };
        for(var i = 0; i < fields.length; i++) {
            data[fields[i]] = document.getElementsByName(fields[i])[0].value;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/company_info');
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
                var response  = JSON.parse(xhr.response);
                // Validation errors
                if (response.validation_errors) {
                    var fields = Object.keys(response.validation_errors.payment_setting);
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
