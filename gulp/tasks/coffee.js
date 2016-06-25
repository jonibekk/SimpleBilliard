const gulp = require('gulp')
const plumber = require('gulp-plumber')
const coffee = require('gulp-coffee')
const duration = require('gulp-duration')
const config = require('../config.js')

gulp.task('js:coffee', () => {
  return gulp.src(config.coffee.src)
    .pipe(plumber())
    .pipe(coffee())
    .pipe(gulp.dest(config.dest + '/js'))
    .pipe(duration('js:coffee'))
})
