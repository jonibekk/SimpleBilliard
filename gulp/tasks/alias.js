import gulp from 'gulp'
import runSequence from 'run-sequence'
import config from '../config.js'

// all
gulp.task('all', done => {
  return runSequence(
    ['js', 'react', 'css'],
    done
  )
})

// js
gulp.task('js', done => {
  return runSequence(
    'js:clean',
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
    'js_vendor:clean',
    'js_vendor:concat',
    'js_vendor:uglify',
    'js_vendor:clean',
    done
  )
})

// angular
gulp.task('angular', done => {
  return runSequence(
    'react:clean',
    'react:browserify',
    'react:uglify',
    'react:clean',
    done
  )
})

// react
gulp.task('react', done => {
  return runSequence(
    'react:clean',
    'react:browserify',
    'react:uglify',
    'react:clean',
    done
  )
})

// css
gulp.task('css', done => {
  return runSequence(
    'css:clean',
    'css:sass',
    'css:vendor',
    'css:concat',
    'css:min',
    'css:clean',
    done
  )
})
