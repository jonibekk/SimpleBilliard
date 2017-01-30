import gulp from 'gulp'
import cssmin from 'gulp-cssmin'
import rename from 'gulp-rename'
import plumber from 'gulp-plumber'
import uglify from 'gulp-uglify'
import duration from 'gulp-duration'
import config from '../config.js'

gulp.task("js:uglify", () => {
  return gulp.src(config.dest + "/js_cat/" + config.js.output.file_name + '.js')
    // .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js.output.path))
    .pipe(duration('js:uglify'))
})

gulp.task("js_vendor:uglify", () => {
  return gulp.src(config.dest + "/js_vendor_cat/" + config.js_vendor.output.file_name + '.js')
    // .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_vendor.output.path))
    .pipe(duration('js_vendor:uglify'))
})

gulp.task("js_prerender:uglify", () => {
  return gulp.src(config.dest + "/js_prerender_cat/" + config.js_prerender.output.file_name + '.js')
    // .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_prerender.output.path))
    .pipe(duration('js_prerender:uglify'))
})

gulp.task("angular_vendor:uglify", () => {
  return gulp.src(config.dest + "/angular_vendor_cat/" + config.angular_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_vendor.output.path))
    .pipe(duration('angular_vendor:uglify'))
})

gulp.task("angular_app:uglify", () => {
  return gulp.src(config.dest + "/angular_app_cat/" + config.angular_app.output.file_name + '.js')
    .pipe(uglify({options : {
      beautify : true,
      mangle   : true
    }}))
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_app.output.path))
    .pipe(duration('angular_app:uglify'))
})

config.react_apps.map((app_name) => {
  gulp.task(`${app_name}:uglify`, () => {
    return gulp.src(config.dest + `/${app_name}/` + config[app_name].output.file_name + '.js')
      // .pipe(uglify())
      .pipe(rename({
        suffix: '.min'
      }))
      .pipe(gulp.dest(config[app_name].output.path))
      .pipe(duration(`${app_name}:uglify`))
  })
})

gulp.task('css:minify', () => {
  return gulp.src(config.dest + '/css_cat/*.css')
    .pipe(plumber())
    .pipe(cssmin())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.css.output.path))
    .pipe(duration('css:min'))
})
