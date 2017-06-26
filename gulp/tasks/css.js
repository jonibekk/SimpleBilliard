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

gulp.task('less:common', () => {
    buildLess(config.less.src.common)
})
gulp.task('less:pages', () => {
  glob(config.less.src.pages, null, function (er, files) {
    files.forEach(function (file) {
      buildLess(file)
    })
    // console.log(files)
  })
})

function buildLess(filePath) {
  // TODO:delete
  let fileName = filePath.replace(/^.*[\\\/]/, '');
  fileName = fileName.replace(/.less/, '');
  const targetLessList = ['feed', 'goal_detail', 'common', 'user_profile',
    'topic', 'goal_create', 'setup_guide', 'goal_search'];
  if (targetLessList.indexOf(fileName) == -1)  {
    return;
  }

  let obj = gulp.src(filePath)
    .pipe(plumber())
    .pipe(accord('less'))
    .on('error', handleErrors)


  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(cssmin());
  }

  return obj.pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(config.less.output.path))
    .pipe(duration('buildLess:' + fileName))

}
