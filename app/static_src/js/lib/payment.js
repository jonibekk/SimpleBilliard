if(document.enterCCInfo){
  var stripe = Stripe('pk_test_6pRNASCoBOKtIshFeQd4XMUh');
  var elements = stripe.elements();

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

  function setOutcome(result) {
    var successElement = document.querySelector('.success');
    var errorElement = document.querySelector('.error');
    successElement.classList.remove('visible');
    errorElement.classList.remove('visible');

    if (result.token) {
      // Use the token to create a charge or a customer
      // https://stripe.com/docs/charges
      successElement.querySelector('.token').textContent = result.token.id;
      successElement.classList.add('visible');
    } else if (result.error) {
      errorElement.textContent = result.error.message;
      errorElement.classList.add('visible');
    }
  }

  card.on('change', function(event) {
    setOutcome(event);
  });

  document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = document.querySelector('form');
    var extraDetails = {
      name: form.querySelector('input[name=cardholder-name]').value,
    };
    stripe.createToken(card, extraDetails).then(setOutcome);
  });
}


if(document.companyLocation){
  var companyLocation = {
    form: document.companyLocation,
    select: document.companyLocation.getElementsByTagName('select')[0],
    submit: document.companyLocation.getElementsByClassName('btn-primary')[0]
  };
  companyLocation.form.addEventListener('submit', function(e){
    e.preventDefault();
    if(companyLocation.select.value == 'JP'){
      document.getElementsByClassName('payment-options')[0].style.height = (document.getElementsByClassName('payment-option-container')[0].clientHeight+20)+'px';
    }else{
      window.location = '/Payment/enterCompanyInfo';
    }
  });
  companyLocation.select.addEventListener('change', function(){
    console.log(companyLocation.select.value);
    if(companyLocation.select.value != 'false'){
      companyLocation.submit.removeAttribute('disabled');
    }else{
      companyLocation.submit.setAttribute('disabled','disabled');
    }
  });
}