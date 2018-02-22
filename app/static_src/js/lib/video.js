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
    })
}
