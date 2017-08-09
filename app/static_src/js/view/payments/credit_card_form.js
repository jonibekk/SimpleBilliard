/**
 * Script related to Stripe credit card forms
 */

// Only for credit card form
if(document.enterCCInfo){
    // Setup Stripe
    var stripe = Stripe(cake.stripe_publishable_key);
    var elements = stripe.elements();

    // Stripe Elements API
    // Custom styling can be passed to options when creating an Element.
    // Check: https://stripe.com/docs/stripe.js#elements-create
    var card = elements.create('card', {
        style: {
            base: {
                iconColor: '#808080',
                color: '#808080',
                lineHeight: '40px',
                fontWeight: 300,
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSize: '15px',
                '::placeholder': {
                    color: '#808080',
                },
            },
        }
    });
    card.mount('#card-element');

    // Add validator listeners
    var cardName = document.enterCCInfo.querySelector('input[name=cardholder-name]');
    card.on('change', function(event) { validateCreditCardForm(event); });
    cardName.addEventListener('change', validateCreditCardForm);

    /**
     * Validate form input and Stripe token results
     *
     * @param result
     * @returns {boolean}
     */
    function validateCreditCardForm(result) {
        var submitButton = document.enterCCInfo.querySelector('input[type=submit]');
        var errorElement = document.querySelector('.error');
        errorElement.classList.remove('visible');

        // Validate Card Name
        if (cardName.value.trim() == '') {
            submitButton.disabled = true;
            return false;
        }

        // Credit card invalid
        if (result.error) {
            errorElement.textContent = result.error.message;
            errorElement.classList.add('visible');
            submitButton.disabled = true;
            return false;
        }

        // Credit card form not completed
        if (result.complete === false) {
            submitButton.disabled = true;
            return false;
        }

        // Validate Token
        if (result.token) {
            // Call function to update the card
            updateCreditCard(result.token);
        }

        submitButton.disabled = false;
        return true;
    }

    /**
     * Send form data to backend API
     *
     * @param token
     */
    function updateCreditCard(token) {

        var formData = new FormData(document.enterCCInfo);
        formData.append('token', token);
        formData.append('payer_name', cardName.value);

        $.ajax({
            url: '/api/v1/payments/credit_card',
            method: 'put',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            timeout: 300000 //5min
        }).done(function (data) {
            // TODO: Redirect to next page
        }).fail(function (data) {
            // Display error message
            new Noty({
                type: 'error',
                text: '<h4>'+cake.word.error+'</h4>' + data.statusText,
            }).show();
        });
    }

    /**
     * Handle Form submit event.
     */
    document.enterCCInfo.addEventListener('submit', function(e) {
        e.preventDefault();
        var extraDetails = {
            name: cardName.value,
        };
        // Request new token from Stripe then validate it
        stripe.createToken(card, extraDetails).then(validateCreditCardForm);
    });

    validateCreditCardForm();
}
