const gulp = require('gulp')
const plumber = require('gulp-plumber')
const coffeelint = require('gulp-coffeelint')
const duration = require('gulp-duration')
const config = require('../config.js')

gulp.task('js:coffeelint', () => {
  // return gulp.src(config.coffee.src)
  //   .pipe(plumber())
  //   .pipe(coffeelint('./coffeelint.json'))
  //   .pipe(duration('js:coffeelint'))
})
