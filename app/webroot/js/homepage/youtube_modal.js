/**
 * Created by daikihirakata on 2/14/16.
 */
// https://developers.google.com/youtube/iframe_api_reference

//FUNCTION TO GET AND AUTO PLAY YOUTUBE VIDEO FROM DATATAG
function autoPlayYouTubeModal(){
    var trigger = $("body").find('[data-toggle="modal"]');
    trigger.click(function() {
        var theModal = $(this).data( "target" ),
            videoSRC = $(this).attr( "data-theVideo" ),
            videoSRCauto = videoSRC+"?autoplay=1" ;
        $(theModal+' iframe').attr('src', videoSRCauto);
        $(theModal).on('hidden.bs.modal', function() {
            $(theModal+' iframe').attr('src', videoSRC);
        });

    });
}
$(document).ready(function(){
    autoPlayYouTubeModal();
});