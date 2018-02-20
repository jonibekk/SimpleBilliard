"use strict";

function feedVideoJs(elementId){
    videojs(elementId, {
        controlBar: {
            fullscreenToggle: false,
            captionsButton: false
        },
        textTrackSettings: false,
        html5: {
            nativeTextTracks: false
        }
        // Use `handleManifestRedirects` option if videojs support HLS on IE11 in future
        // FYI: https://github.com/videojs/videojs-contrib-hls/pull/912#discussion_r164196518
        //, handleManifestRedirects: true
    }).ready(function() {
        var userAgent = window.navigator.userAgent.toLowerCase();
        // decide if IE or not
        var isIE = userAgent.indexOf('trident') != -1;
        if (!isIE) {
            return;
        }
        // below is IE only process
        // @see https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Media_events
        // see this PR for the reason this process existing
        // https://github.com/IsaoCorp/goalous/pull/6653
        var isLoadedFirstTime = false;
        // videoElement is the real video element that playing video
        var videoElement = $(this.contentEl()).find('video')[0];

        // this play event triggerd on every play action
        videoElement.addEventListener("play", function(){
            if (false === isLoadedFirstTime) {
                // if not played video yet, the first seen is shown original video size on IE11
                // hide video element to avoid original video size shown
                $(this).css('display', 'none');
            }
        }, false);
        // this loadeddata is triggerd only on first time data is loaded
        videoElement.addEventListener('loadeddata', function(){
            // if the first data is loaded, video player fixed to correct size on IE11
            $(this).css('display', 'block');
            isLoadedFirstTime = true;
        }, false);
    })
}