/**
 * This file contains script to image rotation
 */
"use strict";

function exifRotate(parentElement){
  var parent = $(parentElement).parent();
  var input = parent.find('input:file').first().get(0);
  if(!input.files ||!input.files.length){
    return;
  }    
  var reader = new FileReader();
  var exifImg = new Image();

  reader.onload = function (e) {
    exifImg.onload = function() {
      EXIF.getData(exifImg, function () {
        var orientation = parseInt(EXIF.getTag(this, "Orientation") || 1);
        var styles;
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
        parent.find('img').css(styles);
      });
    }
    exifImg.src = e.target.result;        
  }

  reader.readAsDataURL(input.files[0]);
}