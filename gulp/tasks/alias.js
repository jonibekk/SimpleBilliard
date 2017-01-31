import gulp from 'gulp'
import runSequence from 'run-sequence'
import config from '../config.js'

gulp.task('build', done => {
  return runSequence(['js', 'css'], done)
})

gulp.task('js', done => {
  return runSequence(['js_app', 'js_vendor', 'js_prerender', 'angular_app', 'angular_vendor', 'react_setup', 'react_signup', 'react_goal_create', 'react_goal_edit', 'react_goal_approval', 'react_goal_search', 'react_kr_column'], done)
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

// react apps
config.react_apps.map((app_name) => {
  gulp.task(app_name, done => {
    return runSequence(
      `${app_name}:eslint`,
      `${app_name}:browserify`,
      `${app_name}:uglify`,
      `${app_name}:clean`,
      done
    )
  })
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
