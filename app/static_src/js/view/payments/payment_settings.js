
// Edit Invoice info
if(document.editPaySettingsForm) {

    var fields = [
        'company_name',
        'company_post_code',
        'company_region',
        'company_city',
        'company_street',
        'contact_person_last_name',
        'contact_person_first_name',
        'contact_person_email',
        'contact_person_tel'
    ];

    // If payment type is invoice, user can update contact person name kana.
    if (document.getElementById('editPaySettingsType').value == 0) {
      fields.push(
        'contact_person_last_name_kana',
        'contact_person_first_name_kana'
      );
    }

  var allFields = document.editPaySettingsForm.querySelectorAll('input[type=text], input[type=email], input[type=tel]');
    for(var i = 0; i < allFields.length; i++) {
        allFields[i].addEventListener('change', removeError);
    }

    document.editPaySettingsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset validation errors
        $(this).find('.help-block').remove();

        var $submitBtn = $(this).find('#editPaySettingsSubmitBtn');
        $submitBtn.prop('disabled', true);


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
                    timeout: 3000
                }).on('onClose', function() {
                    window.location.reload();
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
                    $submitBtn.prop('disabled', false);
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
