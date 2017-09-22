// Edit Invoice info
if (document.editPaySettingsForm) {

    var contactLastnameKana, contactFirstNameKana;
    var companyName = document.getElementById('company_name');
    var companyPostCode = document.getElementById('company_post_code');
    var companyRegion = document.getElementById('company_region');
    var companyCity = document.getElementById('company_city');
    var companyStreet = document.getElementById('company_street');
    var contactLastName = document.getElementById('contact_person_last_name');
    var contactFirstName = document.getElementById('contact_person_first_name');
    var contactEmail = document.getElementById('contact_person_email');
    var contactTel = document.getElementById('contact_person_tel');

    // Initialize international phone plugin
    // https://github.com/jackocnr/intl-tel-input
    $(contactTel).intlTelInput({
        onlyCountries: cake.data.countryCodes,
        autoHideDialCode: false,
        nationalMode: false,
    });

    // Accept only numbers and '+'
    $(contactTel).on('keypress', function (e) {
        return isPhoneNumber(e);
    });
    // Set tel country
    var companyCountry = document.getElementById('countryCode').value;
    $(contactTel).intlTelInput("setCountry", companyCountry);

    // Accept only numbers for postal code
    $(companyPostCode).on('keypress', function (e) {
        return isNumber(e);
    });

    // Validate form
    function validateForm() {
        var isValid = true;

        // Validate kana fields in case of invoice
        if (document.getElementById('editPaySettingsType').value === "0") {
            contactLastnameKana = document.getElementById('contact_person_last_name_kana');
            contactFirstNameKana = document.getElementById('contact_person_first_name_kana');

            if (!isZenKatakana(contactLastnameKana.value)) {
                setError(contactLastnameKana.name, __("Only Kana characters are allowed."));
                isValid = false;
            }

            if (!isZenKatakana(contactFirstNameKana.value)) {
                setError(contactFirstNameKana.name, __("Only Kana characters are allowed."));
                isValid = false;
            }
        }

        // Validate postal code
        var postalCode = companyPostCode.value;
        if ((companyCountry === 'JP' && postalCode.length !== 7) ||
            (companyCountry === 'US' && postalCode.length !== 5)) {
            setError(companyPostCode.name, __("Invalid fields"));
            isValid = false;
        }

        return isValid;
    }

    document.editPaySettingsForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Reset validation errors
        removeAllErrors(this);

        // Validate this form
        if (!validateForm()) {
            return;
        }

        // disable button
        var $submitBtn = $(this).find('#editPaySettingsSubmitBtn');
        $submitBtn.prop('disabled', true);

        var data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            company_name: companyName.value,
            company_post_code: companyPostCode.value,
            company_region: companyRegion.value,
            company_city: companyCity.value,
            company_street: companyStreet.value,
            contact_person_last_name: contactLastName.value,
            contact_person_first_name: contactFirstName.value,
            contact_person_email: contactEmail.value,
            contact_person_tel: contactTel.value
        };

        // If payment type is invoice, user can update contact person name kana.
        if (document.getElementById('editPaySettingsType').value === "0") {
            data.contact_person_last_name_kana = contactLastnameKana.value;
            data.contact_person_first_name_kana = contactFirstNameKana.value;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/company_info');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Updated, redirect to payment pages
                document.editPaySettingsForm.disabled = true;
                new Noty({
                    type: 'success',
                    text: __("Update completed"),
                    timeout: 3000
                }).on('onClose', function () {
                    window.location.reload();
                }).show();
            }
            else {
                var response = JSON.parse(xhr.response);
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
