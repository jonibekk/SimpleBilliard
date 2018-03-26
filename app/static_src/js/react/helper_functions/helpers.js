export const getExifRotateStyle = function(file) {
  var styles;
  EXIF.getData(file, function () {
    var orientation = parseInt(EXIF.getTag(this, "Orientation") || 1);
    var angle = 0;
    var flip = false;
    switch (orientation) {
     default:
      case 1:
        angle = 0;
        break;
      case 2:
        angle = 0;
        flip = true;
        break;
      case 3:
        angle = 180;
        break;
      case 4:
        angle = 180;
        flip = true;
        break;
      case 5:
        angle = 270;
        flip = true;
        break;
      case 6:
        angle = 90;
        break;
      case 7:
        angle = 90;
        flip = true;
        break;
      case 8:
        angle = 270;
        break;
    }
    if(!flip){
      styles = {
        "transform": "rotate(" + angle + "deg)",
        "-ms-transform": "rotate(" + angle + "deg)",
        "-o-transform": "rotate(" + angle + "deg)",
        "-moz-transform": "rotate(" + angle + "deg)",
        "-webkit-transform": "rotate(" + angle + "deg)"
      };
    } else {
      styles = {
        "transform": "rotate(" + angle + "deg) scaleX(-1)",
        "-ms-transform": "rotate(" + angle + "deg) scaleX(-1)",
        "-o-transform": "rotate(" + angle + "deg) scaleX(-1)",
        "-moz-transform": "rotate(" + angle + "deg) scaleX(-1)",
        "-webkit-transform": "rotate(" + angle + "deg) scaleX(-1)"
      };
    }
  });
  return styles;
}

export const bindExifRotate = function(inputid,inputpreviewid) {
  document.getElementById(inputpreviewid).addEventListener('DOMNodeInserted', function() {
    var input = document.getElementById(inputid);
    if(!input.files || !input.files[0]){
      return false;
    }
    var file = input.files[0];
    loadImage.parseMetaData(file, function(data) {
      var styles;
      var orientation = 1;
      //if exif data available, update orientation
      if (data.exif) {
          orientation = data.exif.get('Orientation');
      }

      var angle = 0;
      var flip = false;
      switch (orientation) {
        case 1:
          angle = 0;
          break;
        case 2:
          angle = 0;
          flip = true;
          break;
        case 3:
          angle = 180;
          break;
        case 4:
          angle = 180;
          flip = true;
          break;
        case 5:
          angle = 270;
          flip = true;
          break;
        case 6:
          angle = 90;
          break;
        case 7:
          angle = 90;
          flip = true;
          break;
        case 8:
          angle = 270;
          break;
        default :
          angle = 0;
          break;
      }
      if(!flip){
        styles = {
          "transform": "rotate(" + angle + "deg)",
          "-ms-transform": "rotate(" + angle + "deg)",
          "-o-transform": "rotate(" + angle + "deg)",
          "-moz-transform": "rotate(" + angle + "deg)",
          "-webkit-transform": "rotate(" + angle + "deg)"
        };
      } else {
        styles = {
          "transform": "rotate(" + angle + "deg) scaleX(-1)",
          "-ms-transform": "rotate(" + angle + "deg) scaleX(-1)",
          "-o-transform": "rotate(" + angle + "deg) scaleX(-1)",
          "-moz-transform": "rotate(" + angle + "deg) scaleX(-1)",
          "-webkit-transform": "rotate(" + angle + "deg) scaleX(-1)"
        };
      }
      $('#' + inputpreviewid).find('img').css(styles);
    }); 
  }, false);
}