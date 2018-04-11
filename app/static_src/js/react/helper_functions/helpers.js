export const setExifRotateStyle = function(parentElement) {
  const parent = $(parentElement).parent();
  const input = parent.find('input:file').first().get(0);
  if(!input.files ||!input.files.length){
    return;
  }    
  const reader = new FileReader();
  const exifImg = new Image();

  reader.onload = function (e) {
    exifImg.onload = function() {
      EXIF.getData(exifImg, function () {
        const orientation = parseInt(EXIF.getTag(this, "Orientation") || 1);
        let styles;
        let angle = 0;
        let flip = false;
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

export const setExifRotateStyleFile = function(file, ref) {
  const reader = new FileReader();
  const exifImg = new Image();

  reader.onload = function (e) {
    exifImg.onload = function() {
      EXIF.getData(exifImg, function () {
        const orientation = parseInt(EXIF.getTag(this, "Orientation") || 1);
        let styles;
        let angle = 0;
        let flip = false;
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
        $(ref).find('img').css(styles);
      });
    }
    exifImg.src = e.target.result;        
  }

  reader.readAsDataURL(file);
}