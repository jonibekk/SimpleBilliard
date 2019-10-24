/* ======= Animations ======= */
$(document).ready(function() {

    //Only animate elements when using non-mobile devices
    if (isMobile.any === false) {

        /* Animate elements in #promo (homepage) */
        $('#promo .intro .title').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInLeft delayp1');
            }
        });
        $('#promo .intro .summary').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInRight delayp3');
            }
        });


        /* Animate elements in #video (homepage) */
        $('#video .title').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInLeft delayp1');
            }
        });

        $('#video .summary').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInRight delayp3');
            }
        });


        /* Animate elements in #features-promo */
        $('#features-promo .title').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInLeft delayp1');
            }
        });

        $('#features-promo .features-list').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInRight delayp3');
            }
        });

        $('.home-page .from-left').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInLeft delayp6');
            }
        });

        $('.home-page .from-right').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInRight delayp6');
            }
        });
        $('.home-page .from-bottom').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInUp delayp1');
            }
        });

        $('.tour-page .from-left').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInLeft delayp6');
            }
        });

        $('.tour-page .from-right').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInRight delayp6');
            }
        });

        $('.tour-page .from-bottom-1').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInDown delayp1');
            }
        });
        $('.tour-page .from-bottom-2').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInDown delayp2');
            }
        });
        $('.tour-page .from-bottom-3').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInDown delayp3');
            }
        });
        $('.tour-page .from-bottom-4').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInDown delayp4');
            }
        });
        /* Animate elements in #price-plan */
        $('#price-plan .price-figure').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInUp delayp1');
            }
        });

        $('#price-plan .heading .label').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInDown delayp6');
            }
        });

        /* Animate elements in #contact-main */
        $('#contact-main .item .icon').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInUp delayp1');
            }
        });

        /* Animate elements in #signup */

        $('#signup .signup-form').css('opacity', 0).one('inview', function(isInView) {
            if (isInView) {
                $(this).addClass('animated fadeInUp delayp6');
            }
        });

    }
  $(window).scroll(function (){
    $('.fadein').each(function(){
      var elemPos = $(this).offset().top;
      var scroll = $(window).scrollTop();
      var windowHeight = $(window).height();
      if (scroll > elemPos - windowHeight + 200){
        $(this).addClass('scrollin');
      }
    });
  });

});
