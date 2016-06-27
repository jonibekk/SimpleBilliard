const gulp = require('gulp')
const runSequence = require('run-sequence')
const config = require('../config.js')

gulp.task('all', done => {
  return runSequence(['js', 'css'], done)
})

gulp.task('js', done => {
  return runSequence(['js_app', 'js_vendor', 'angular_app', 'angular_vendor', 'react_setup'], done)
})

// js app
gulp.task('js_app', done => {
  return runSequence(
    'js:coffeelint',
    'js:coffee',
    'js:concat',
    'js:uglify',
    'js:clean',
    done
  )
})

// js vendor
gulp.task('js_vendor', done => {
  return runSequence(
    'js_vendor:concat',
    'js_vendor:uglify',
    'js_vendor:clean',
    done
  )
})

// angular app
gulp.task('angular_app', done => {
  return runSequence(
    'angular_app:concat',
    'angular_app:uglify',
    'angular_app:clean',
    done
  )
})

// angular vendor
gulp.task('angular_vendor', done => {
  return runSequence(
    'angular_vendor:concat',
    'angular_vendor:uglify',
    'angular_vendor:clean',
    done
  )
})

// react
gulp.task('react_setup', done => {
  return runSequence(
    'react_setup:browserify',
    'react_setup:uglify',
    'react_setup:clean',
    done
  )
})

// css
gulp.task('css', done => {
  return runSequence(
    'css:less',
    'css:concat',
    'css:minify',
    'css:clean',
    done
  )
})
