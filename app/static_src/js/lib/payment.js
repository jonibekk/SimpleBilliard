if(document.enterCCInfo){
  var stripe = Stripe(cake.stripe_publishable_key);
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
    select: document.companyLocation.getElementsByClassName('company-location-select')[0],
    submit: document.companyLocation.getElementsByClassName('btn-primary')[0]
  };
  companyLocation.form.addEventListener('submit', function(e){
    e.preventDefault();
    if(companyLocation.select.value == 'JP'){
      document.getElementsByClassName('payment-options')[0].style.height = (document.getElementsByClassName('payment-option-container')[0].clientHeight+20)+'px';
    }else{
      window.location = '/payments/enterCompanyInfo';
    }
  });
  companyLocation.select.addEventListener('change', function(){
    if(companyLocation.select.value != 'false'){
      companyLocation.submit.removeAttribute('disabled');
    }else{
      companyLocation.submit.setAttribute('disabled','disabled');
    }
  });
}

// If .confetti-cannon class exists, make it rain confetti
var confettiCannon = document.getElementsByClassName('confetti-cannon')[0];

if(confettiCannon){
  // Delay the confetti animation to prevent animation while assets are still loading.
  setTimeout(function(){
    // The amount of confetti should be based on how wide the screen is.
    for(i=0;i<(confettiCannon.clientWidth);i++){
      setTimeout(function(){
        // Create a single peice of confetti
        var confetti = document.createElement("DIV");
        confetti.classList.add('confetti');
        // Assign one of four classes (each class has a different animation)
        // Ex: confetti-1 || confetti-2 || confetti-3 || confetti-4
        confetti.classList.add('confetti-'+parseInt((Math.round(Math.random()*3))+1));
        // Assign the left pixel value by a random number.
        confetti.style.left=(Math.round(Math.random()*100))+'vw';
        // Attach the piece of confetti to the container
        confettiCannon.appendChild(confetti);
        // We don't want all of the confetti to fall at the exact same time,
        // so we create a setTimeout to spread out the confetti.
        // We have a base delay between 1ms-5ms, then we add a modifier.
        // If the screen size is smaller (closer to 320) then the modifier is larger
        // This way, smaller screens have a more spread out delay
      }, (Math.round(Math.random()*1))+(i*5)+(320/confettiCannon.clientWidth));
    }
  },1000);
}