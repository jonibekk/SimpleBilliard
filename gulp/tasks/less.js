const gulp = require('gulp')
const plumber = require('gulp-plumber')
const less = require('gulp-less')
const cssmin = require('gulp-cssmin')
const duration = require('gulp-duration')
const config = require('../config.js')

gulp.task('css:less', () => {
  return gulp.src(config.less.src)
    .pipe(plumber())
    .pipe(less())
    .pipe(gulp.dest(config.dest + '/css'))
    .pipe(duration('css:less'))
})
