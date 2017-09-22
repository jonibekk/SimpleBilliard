/**
 * Script related to Stripe credit card forms
 */

// Only for credit card form
if (document.enterCCInfo) {
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
    card.on('change', function (event) {
        validateCreditCardForm(event);
    });
    cardName.addEventListener('keyup', validateCreditCardForm);
    var cardElement = document.getElementById('card-element');

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

        // Check if the credit card have been entered
        if (cardElement && cardElement.className.indexOf('StripeElement--empty') !== -1) {
            return false;
        }

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
            submitButton.disabled = true;
            return true;
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

        var data = {
            'data[_Token][key]': cake.data.csrf_token.key,
            'token': token.id,
            'payer_name': cardName.value
        };

        var xhr = new XMLHttpRequest();
        xhr.open('PUT', '/api/v1/payments/' + cake.data.team_id + '/credit_card');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Go back to payment method page
                window.location.href = '/payments/method';
            }
            else {
                var response = JSON.parse(xhr.response);
                // Disable submit button
                document.enterCCInfo.querySelector('input[type=submit]').disabled = true;
                // Display error message
                new Noty({
                    type: 'error',
                    text: '<h4>' + cake.word.error + '</h4>' + response.message,
                }).on('onClose', function () {
                    // Focus on card name
                    cardName.focus();
                }).show();
            }
        };
        xhr.send(urlEncode(data));
    }

    /**
     * Handle Form submit event.
     */
    document.enterCCInfo.addEventListener('submit', function (e) {
        e.preventDefault();
        var extraDetails = {
            name: cardName.value,
        };
        // Request new token from Stripe then validate it
        stripe.createToken(card, extraDetails).then(validateCreditCardForm);
    });

    validateCreditCardForm(null);
}
