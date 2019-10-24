$(document).ready(function() {
  // Delete token to prevent access to new Goalous after logout
  if(!cake.data.user_id && localStorage.getItem('token')) {
    localStorage.removeItem('token');
  }

  if (window.outerWidth >= 992) {
    var headerHeight = $('#header').outerHeight();
    $('.wrapper').css('margin-top', headerHeight);
  }

  /* ======= FAQ accordion ======= */

    function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find('.panel-title a')
            .toggleClass('active')
            .find("i.fa")
            .toggleClass('fa-plus-square fa-minus-square');
    }
    $('.panel').on('hidden.bs.collapse', toggleIcon);
    $('.panel').on('shown.bs.collapse', toggleIcon);

    /* ======= Fixed header when scrolled ======= */

    $("#agree").on('change', function() {
        if ($(this).is(':checked')) {
            $("#go-confirm").prop('disabled', false);
        } else {
            $("#go-confirm").prop('disabled', true);
        }
    });

    var $promoVideo = $('#promoVideo')[0];
    
    // #tour-videoのモーダルが開いたら
    $('#tour-video').on('shown.bs.modal', function () {
        $promoVideo.src += "https://www.youtube.com/embed/"+$promoVideo.dataset.src+"?rel=0&autoplay=1";
    });

    // #tour-videoのモーダルが閉じたら
    $('#tour-video').on('hidden.bs.modal', function () {
        $promoVideo.src += "https://www.youtube.com/embed/"+$promoVideo.dataset.src+"?rel=0&autoplay=0";
    });

    faqInit();
    contactFormInit();
});

$(window).resize(function(){
    faqInit();
});

var canSubmit = 0;
var $requiredInput = $('#contact_section').find('input:required');
var $contactSubmit = $('#contact_section button')[0];

function faqInit(){
    var $questions = $('#faqs .question');
    
    $questions.each(function(){
        var $question = $(this),
        $answer = $question.find('.answer')[0],
        setHeight = 0;
        for(i=0; i<$answer.childElementCount; i++){
            setHeight += parseInt($answer.children[i].clientHeight);
        }
        $answer.dataset.height = setHeight+30;
        $question.click(function(){
            if(!$(this).hasClass('active')){
                $(this).addClass('active');
                $answer.style.height=$answer.dataset.height+'px';
            }else{
                $(this).removeClass('active').find('.answer').css('height',0);
            }
        });
    });
}

function contactFormInit(){
    if($contactSubmit === 'undefined'){ 
        $contactSubmit.setAttribute('disabled','disabled');
        $requiredInput.each(function(){
            var $this = $(this);
            $(this).keyup(function(){
                if($this.val() !== '' && !$this.hasClass('valid')){
                    $this.addClass('valid');
                    checkForm(true);
                }else if($this.val() === '' && $this.hasClass('valid')){
                    $this.removeClass('valid');
                    checkForm(false);
                }
            });
            if($this.val() !== '' && !$this.hasClass('valid')){
                $this.addClass('valid');
                checkForm(true);
            }
        });
    }
}

function checkForm(isValid){
    if(isValid){
        canSubmit++;
    }else{
        canSubmit--;
    }
    if(canSubmit === $requiredInput.length){
        $contactSubmit.removeAttribute('disabled');
    }else{
        $contactSubmit.setAttribute('disabled','disabled');
    }
}
