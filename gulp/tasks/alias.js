import gulp from 'gulp'
import runSequence from 'run-sequence'

gulp.task('build', done => {
  return runSequence(['js', 'css'], done)
})

gulp.task('js', done => {
  return runSequence(['js_app', 'js_vendor', 'js_prerender', 'angular_app', 'angular_vendor', 'react_setup', 'react_signup', 'react_goal_create'], done)
})

// js app
gulp.task('js_app', done => {
  return runSequence(
    'js:eslint',
    'js:coffeelint',
    'js:coffee',
    'js:concat',
    'js:uglify',
    'js:clean',
    done
  )
})

// js prerender
gulp.task('js_prerender', done => {
  return runSequence(
    'js_prerender:concat',
    'js_prerender:uglify',
    'js_prerender:clean',
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

// react setup
gulp.task('react_setup', done => {
  return runSequence(
    'react_setup:eslint',
    'react_setup:browserify',
    'react_setup:uglify',
    'react_setup:clean',
    done
  )
})

// react signup
gulp.task('react_signup', done => {
  return runSequence(
    'react_signup:eslint',
    'react_signup:browserify',
    'react_signup:uglify',
    'react_signup:clean',
    done
  )
})

// react goal create
gulp.task('react_goal_create', done => {
  return runSequence(
    'react_goal_create:eslint',
    'react_goal_create:browserify',
    'react_goal_create:uglify',
    'react_goal_create:clean',
    done
  )
})

// css
gulp.task('css', done => {
  return runSequence(
    'css:lesshint',
    'css:less',
    'css:concat',
    'css:minify',
    'css:clean',
    done
  )
})
