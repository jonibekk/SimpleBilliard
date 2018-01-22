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
    })
}
