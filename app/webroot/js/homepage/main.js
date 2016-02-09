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
         }
         else {
             $('#header').removeClass('navbar-fixed-top');
         }
    });
    
    /* ======= Toggle between Signup & Login & ResetPass Modals ======= */ 
    $('#signup-link').on('click', function(e) {
        $('#signup-modal').modal();
        $('#login-modal').modal('toggle');
        e.preventDefault();
    });
    
    $('#login-link').on('click', function(e) {
        $('#login-modal').modal();
        $('#signup-modal').modal('toggle');
        e.preventDefault();
    });
    
    $('#back-to-login-link').on('click', function(e) {
        $('#login-modal').modal();
        $('#resetpass-modal').modal('toggle');
        e.preventDefault();
    });
    
    $('#resetpass-link').on('click', function(e) {
        $('#login-modal').modal('hide');
        e.preventDefault();
    });
    
    /* ======= Price Plan CTA buttons trigger signup modal ======= */ 
    
    $('#price-plan .btn-cta').on('click', function(e) {
        $('#signup-modal').modal();
        e.preventDefault();
    });
 
    
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



});