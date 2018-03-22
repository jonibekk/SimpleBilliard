import gulp from "gulp";
import plumber from "gulp-plumber";
import duration from "gulp-duration";
import config from "../config.js";
import concat from "gulp-concat";
import cssmin from "gulp-cssmin";
import autoprefixer from "gulp-autoprefixer";
import accord from "gulp-accord";
import handleErrors from "../util/handleErrors";
import glob from "glob";
import rename from "gulp-rename";

gulp.task('css_vendor', () => {
  let obj = gulp.src([...config.css_vendor.src])
    .pipe(plumber())
    .pipe(autoprefixer())
    .pipe(concat(config.css_vendor.output.file_name + '.min.css'))

  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(cssmin());
  }

  return obj.pipe(gulp.dest(config.css_vendor.output.path))
    .pipe(duration('css_vendor'))
})

gulp.task('css_fonts:min', () => {
  let obj = gulp.src([...config.css_fonts.src])
    .pipe(plumber())
    .pipe(autoprefixer())
    .pipe(concat(config.css_fonts.output.file_name + '.min.css'))

  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(cssmin());
  }

  return obj.pipe(gulp.dest(config.css_fonts.output.path))
    .pipe(duration('css_fonts:min'))
})

gulp.task('css_fonts:copy', () => {
  return gulp.src([...config.css_fonts.font_src])
    .pipe(gulp.dest(config.css_fonts.output.path_name))
    .pipe(duration('css_fonts:copy'))    
})

gulp.task('less:common', () => {
    buildLess(config.less.src.common, false)
})
gulp.task('less:pages', () => {
  glob(config.less.src.pages, null, function (er, files) {
    files.forEach(function (file) {
      buildLess(file, false)
    })
    // console.log(files)
  })
})
gulp.task('less:homepage', () => {
    buildLess(config.less.src.homepage, true)
})

function buildLess(filePath, isHomepage) {
  let fileName = filePath.replace(/^.*[\\\/]/, '');
  fileName = fileName.replace(/.less/, '');

  let obj = gulp.src(filePath)
    .pipe(plumber())
    .pipe(accord('less'))
    .on('error', handleErrors)


  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(cssmin());
  }

  if (isHomepage){
    return obj.pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(config.less.output.homepage))
    .pipe(duration('buildLess:' + fileName))
  }else{
    return obj.pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(config.less.output.path))
    .pipe(duration('buildLess:' + fileName))
  }
}
