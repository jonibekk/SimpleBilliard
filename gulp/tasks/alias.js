import gulp from 'gulp'
import runSequence from 'run-sequence'
import config from '../config.js'

gulp.task('all', ['js', 'css'])

gulp.task('js', ['js_app', 'js_vendor', 'angular_app', 'angular_vendor', 'react_setup'])

// js
gulp.task('js_app', done => {
  runSequence(
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
  runSequence(
    'js_vendor:concat',
    'js_vendor:uglify',
    'js_vendor:clean',
    done
  )
})

// angular app
gulp.task('angular_app', done => {
  runSequence(
    'angular_app:concat',
    'angular_app:uglify',
    'angular_app:clean',
    done
  )
})

// angular vendor
gulp.task('angular_vendor', done => {
  runSequence(
    'angular_vendor:concat',
    'angular_vendor:uglify',
    'angular_vendor:clean',
    done
  )
})

// react
gulp.task('react_setup', done => {
  runSequence(
    'react_setup:browserify',
    'react_setup:uglify',
    'react_setup:clean',
    done
  )
})

// css
gulp.task('css', done => {
  runSequence(
    'css:less',
    'css:concat',
    'css:min',
    'css:clean',
    done
  )
})
