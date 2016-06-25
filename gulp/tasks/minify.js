const gulp = require('gulp')
const cssmin = require('gulp-cssmin')
const rename = require('gulp-rename')
const plumber = require('gulp-plumber')
const uglify = require('gulp-uglify')
const duration = require('gulp-duration')
const config = require('../config.js')

gulp.task("js:uglify", () => {
  return gulp.src(config.dest + "/js_cat/" + config.js.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.js.output.path))
    .pipe(duration('js:uglify'))
})

gulp.task("js_vendor:uglify", () => {
  return gulp.src(config.dest + "/js_vendor_cat/" + config.js_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_vendor.output.path))
    .pipe(duration('js_vendor:uglify'))
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
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_app.output.path))
    .pipe(duration('angular_app:uglify'))
})

gulp.task('css:minify', () => {
  return gulp.src(config.dest + '/css_cat')
    .pipe(plumber())
    .pipe(cssmin())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.css.output.path))
    .pipe(duration('css:min'))
})
