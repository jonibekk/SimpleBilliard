// This is not vendor file
// but preventing videojs preventing Additional <style> Elements
// call before the video.js load

// https://github.com/videojs/video.js/blob/master/docs/guides/skins.md#disabling-additional-style-elements
window.VIDEOJS_NO_DYNAMIC_STYLE = true;