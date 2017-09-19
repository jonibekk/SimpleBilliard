
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

    var companyName = document.getElementById('company_name');
    var companyCountry = document.getElementById('company_country');
    var companyPostCode = document.getElementById('company_post_code');
    var companyRegion = document.getElementById('company_region');
    var companyCity = document.getElementById('company_city');
    var companyStreet = document.getElementById('company_street');
    var companyTel = document.getElementById('company_tel');
    var contactLastName = document.getElementById('contact_person_last_name');
    var contactFirstName = document.getElementById('contact_person_first_name');
    var contactLastnameKana = document.getElementById('contact_person_last_name_kana');
    var contactFirstNameKana = document.getElementById('contact_person_first_name_kana');
    var contactEmail = document.getElementById('contact_person_email');
    var contactTel = document.getElementById('contact_person_tel');

    // Initialize international phone plugin
    // https://github.com/jackocnr/intl-tel-input
    $(companyTel).intlTelInput({
        onlyCountries: ['us', 'jp', 'de', 'th'],
        autoHideDialCode: false,
        nationalMode: false,
    });
    $(contactTel).intlTelInput({
        onlyCountries: ['us', 'jp', 'de', 'th'],
        autoHideDialCode: false,
        nationalMode: false,
    });
    // Accept only numbers and '+'
    $(companyTel).on('keypress', function (e) {
        return isPhoneNumber(e);
    });
    $(contactTel).on('keypress', function (e) {
        return isPhoneNumber(e);
    });
    // Changes on the country address will affect the phone input
    $(companyTel).intlTelInput("setCountry", companyCountry.value);
    $(companyCountry).on('change', function () {
        $(companyTel).intlTelInput("setCountry", companyCountry.value);
    });

    // Accept only numbers for postal code
    $(companyPostCode).on('keypress', function (e) {
        return validatePostalcode() & isNumber(e);
    });

    function validatePostalcode() {
        var country = companyCountry.value;
        var postalCode = companyPostCode.value;

        if ((country === 'JP' && postalCode.length === 7) ||
            (country === 'US' && postalCode.length === 5)) {
            setError('company_post_code', 'Invalid format');
            return false;
        } else {
            removeErrorFromField('company_post_code');
        }
        return true;
    }

    var submitButton = document.getElementById('submitButton');
    var firstNameKana, lastNameKana;
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

        if (!validatePostalcode()) {
            return;
        }

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
        var encodedData = urlEncode(data).split("+").join("%2B");
        xhr.send(encodedData);
    });
}
