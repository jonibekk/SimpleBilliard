
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
        switch (allFields[i].name) {
            case 'contact_person_first_name_kana':
                firstNameKana = allFields[i];
                allFields[i].addEventListener('input', validateKanaFields);
                break

            case 'contact_person_last_name_kana':
                lastNameKana = allFields[i];
                allFields[i].addEventListener('input', validateKanaFields);
                break
        }
    }

    function validateKanaFields(e) {
        if ((firstNameKana.value !== '' && !isZenKatakana(firstNameKana.value)) ||
            (lastNameKana.value !== '') && !isZenKatakana(lastNameKana.value)) {

            setError(e.target.name, __("Only Kana characters are allowed."));
            submitButton.disabled = true;
            return;
        }
        removeError(e);
        submitButton.disabled = false;
        submitButton.enabled = true;
    }
    
    function validateForm() {
        
    }

    document.editPaySettingsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset validation errors
        $(this).find('.help-block').remove();

        var $submitBtn = $(this).find('#editPaySettingsSubmitBtn');
        $submitBtn.prop('disabled', true);

        var data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            company_name: companyName.value,
            company_country: companyCountry.value,
            company_post_code: companyPostCode.value,
            company_region: companyRegion.value,
            company_city: companyCity.value,
            company_street: companyStreet.value,
            company_tel: companyTel.value,
            contact_person_last_name: contactLastName.value,
            contact_person_first_name: contactFirstName.value,
            contact_person_last_name_kana: contactLastnameKana.value,
            contact_person_first_name_kana: contactFirstNameKana.value,
            contact_person_email: contactEmail.value,
            contact_person_tel: contactTel.value
        };


        var xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/company_info');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Updated, redirect to payment pages
                document.editPaySettingsForm.disabled = true;
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
        var encodedData = urlEncode(data).split("+").join("%2B");
        xhr.send(encodedData);
    });
}
