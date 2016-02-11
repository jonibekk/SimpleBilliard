$(document).ready(function() {

    /* ======= jQuery Placeholder ======= */
    $('input, textarea').placeholder();

    /* ======= jQuery FitVids - Responsive Video ======= */
    $(".video-container").fitVids();

    /* ======= Header Background Slideshow - Flexslider ======= */
    /* Ref: https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties */

    $('#bg-slider').flexslider({
        animation: "fade",
        directionNav: false, //remove the default direction-nav - https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties
        controlNav: false, //remove the default control-nav
        slideshowSpeed: 6000
    });


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

    /* ======= Toggle between Signup & Login & ResetPass Modals ======= */

    /* ======= Price Plan CTA buttons trigger signup modal ======= */

    /* ======= Style Switcher (REMOVE ON YOUR PRODUCTION SITE) ======= */

    $('#config-trigger').on('click', function(e) {
        var $panel = $('#config-panel');
        var panelVisible = $('#config-panel').is(':visible');
        if (panelVisible) {
            $panel.hide();
        } else {
            $panel.show();
        }
        e.preventDefault();
    });

    $('#config-close').on('click', function(e) {
        e.preventDefault();
        $('#config-panel').hide();
    });


    $('#color-options a').on('click', function(e) {
        var $styleSheet = $(this).attr('data-style');
        $('#theme-style').attr('href', $styleSheet);

        var $listItem = $(this).closest('li');
        $listItem.addClass('active');
        $listItem.siblings().removeClass('active');

        e.preventDefault();

    });

    $("#agree").on('change', function() {
        if ($(this).is(':checked')) {
            $("#go-confirm").prop('disabled', false);
        } else {
            $("#go-confirm").prop('disabled', true);
        }
    });

    // #tour-videoのモーダルが開いたら
    $('#tour-video').on('shown.bs.modal', function () {
        // id vimeo-videoのiframeの srcにhttps://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=1を引き渡す。
        $('#vimeo-video').attr('src', 'https://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=1');
    });

    // #tour-videoのモーダルが閉じたら
    $('#tour-video').on('hidden.bs.modal', function () {
        $('#vimeo-video').attr('src', 'https://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=0');
    });

});
