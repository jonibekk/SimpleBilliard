
// Edit Invoice info
if(document.editPaySettingsForm) {

    let companyName = document.editPaySettingsForm.querySelector('input[name=company_name]');
    let companyCountry = document.editPaySettingsForm.querySelector('select[name=company_country]');
    let postalCode = document.editPaySettingsForm.querySelector('input[name=company_post_code]');
    let region = document.editPaySettingsForm.querySelector('input[name=company_region]');
    let city = document.editPaySettingsForm.querySelector('input[name=company_city]');
    let street = document.editPaySettingsForm.querySelector('input[name=company_street]');
    let companyTel = document.editPaySettingsForm.querySelector('input[name=company_tel]');
    let lastName = document.editPaySettingsForm.querySelector('input[name=contact_person_last_name]');
    let firstName = document.editPaySettingsForm.querySelector('input[name=contact_person_first_name]');
    let lastNameKana = document.editPaySettingsForm.querySelector('input[name=contact_person_last_name_kana]');
    let firstNameKana = document.editPaySettingsForm.querySelector('input[name=contact_person_first_name_kana]');
    let email = document.editPaySettingsForm.querySelector('input[name=contact_person_email]');
    let telephone = document.editPaySettingsForm.querySelector('input[name=contact_person_tel]');

    let allFields = document.editPaySettingsForm.querySelectorAll('input[type=text]');
    for(var i = 0; i < allFields.length; i++) {
        allFields[i].addEventListener('change', removeError);
    }

    document.editPaySettingsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        let data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            'company_name': companyName.value,
            'company_country': companyCountry.value,
            'company_post_code': postalCode.value,
            'company_region': region.value,
            'company_city': city.value,
            'company_street': street.value,
            'company_tel': companyTel.value,
            'contact_person_last_name': lastName.value,
            'contact_person_first_name': firstName.value,
            'contact_person_last_name_kana': lastNameKana.value,
            'contact_person_first_name_kana': firstNameKana.value,
            'contact_person_email': email.value,
            'contact_person_tel': telephone.value
        };

        let xhr = new XMLHttpRequest();
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
                let response  = JSON.parse(xhr.response);
                let fields = Object.keys(response.validation_errors.payment_setting);
                fields.forEach(function (item) {
                    setError(item, response.validation_errors.payment_setting[item]);
                });
            }
        };
        xhr.send(urlEncode(data));
    });
}
