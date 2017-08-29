
// Edit Invoice info
if(document.editInvoiceForm) {

    let companyName = document.editInvoiceForm.querySelector('input[name=company_name]');
    let postalCode = document.editInvoiceForm.querySelector('input[name=company_post_code]');
    let region = document.editInvoiceForm.querySelector('input[name=company_region]');
    let city = document.editInvoiceForm.querySelector('input[name=company_city]');
    let street = document.editInvoiceForm.querySelector('input[name=company_street]');
    let lastName = document.editInvoiceForm.querySelector('input[name=contact_person_last_name]');
    let firstName = document.editInvoiceForm.querySelector('input[name=contact_person_first_name]');
    let lastNameKana = document.editInvoiceForm.querySelector('input[name=contact_person_last_name_kana]');
    let firstNameKana = document.editInvoiceForm.querySelector('input[name=contact_person_first_name_kana]');
    let email = document.editInvoiceForm.querySelector('input[name=contact_person_email]');
    let telephone = document.editInvoiceForm.querySelector('input[name=contact_person_tel]');

    let allFields = document.editInvoiceForm.querySelectorAll('input[type=text]');
    for(var i = 0; i < allFields.length; i++) {
        allFields[i].addEventListener('input', removeError);
    }

    document.editInvoiceForm.addEventListener('submit', function(e) {
        e.preventDefault();

        let data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            'company_name': companyName.value,
            'company_post_code': postalCode.value,
            'company_region': region.value,
            'company_city': city.value,
            'company_street': street.value,
            'contact_person_last_name': lastName.value,
            'contact_person_first_name': firstName.value,
            'contact_person_last_name_kana': lastNameKana.value,
            'contact_person_first_name_kana': firstNameKana.value,
            'contact_person_email': email.value,
            'contact_person_tel': telephone.value
        };

        let xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/invoice');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var userInfo = JSON.parse(xhr.responseText);
                console.log(userInfo);
            }
            else {
                console.log('Request failed.  Returned status of ' + xhr.status);

                let response  = JSON.parse(xhr.response);
                let fields = Object.keys(response.validation_errors.payment_setting);
                fields.forEach(function (item) {
                    setError(item, response.validation_errors.payment_setting[item]);
                });

                console.log('Message: ' + response);
            }
        };
        xhr.send(urlEncode(data));
    });

    function setError(fieldName, message) {
        let field = document.editInvoiceForm.querySelector('input[name=' + fieldName + ']');
        field.parentNode.className += ' has-error';
        let error = document.createElement('small');
        error.className = 'help-block';
        error.innerHTML = message;
        field.parentNode.appendChild(error);
    }

    function removeError(e) {
        let field = e.target;
        field.parentNode.className = field.parentNode.className.replace('has-error', '');
        let error = field.parentNode.querySelector('small');
        if (error) {
            error.remove();
        }
    }

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
}