import gulp from 'gulp'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'
import duration from 'gulp-duration'
import config from '../config.js'

gulp.task('react_setup:browserify', () => {
  return browserify({entries: [config.react_setup.src]})
    .transform(babelify, config.browserify.transform.babelify_options)
    .bundle()
    .pipe(source(config.react_setup.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_setup'))
    .pipe(duration('react_setup:browserify'))
})

gulp.task('react_signup:browserify', () => {
  return browserify({entries: [config.react_signup.src]})
    .transform(babelify, config.browserify.transform.babelify_options)
    .bundle()
    .pipe(source(config.react_signup.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_signup'))
    .pipe(duration('react_signup:browserify'))
})

gulp.task('react_goal_create:browserify', () => {
  return browserify({entries: [config.react_goal_create.src]})
    .transform(babelify, config.browserify.transform.babelify_options)
    .bundle()
    .pipe(source(config.react_goal_create.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_goal_create'))
    .pipe(duration('react_goal_create:browserify'))
})

gulp.task('react_goal_edit:browserify', () => {
  return browserify({entries: [config.react_goal_edit.src]})
    .transform(babelify, config.browserify.transform.babelify_options)
    .bundle()
    .pipe(source(config.react_goal_edit.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_goal_edit'))
    .pipe(duration('react_goal_edit:browserify'))
})

gulp.task('react_goal_approval:browserify', () => {
  return browserify({entries: [config.react_goal_approval.src]})
    .transform(babelify, config.browserify.transform.babelify_options)
    .bundle()
    .pipe(source(config.react_goal_approval.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_goal_approval'))
    .pipe(duration('react_goal_approval:browserify'))
})
