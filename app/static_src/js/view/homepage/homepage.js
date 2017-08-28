$(document).ready(function() {
    
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
    
        $(window).bind('scroll', function() {
            if ($(window).scrollTop() > 0) {
                $('#header').addClass('navbar-fixed-top');
            } else {
                $('#header').removeClass('navbar-fixed-top');
            }
        });
    
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
    
    });
    