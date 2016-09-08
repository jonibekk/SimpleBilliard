import gulp from 'gulp'
import rimraf from 'gulp-rimraf'
import duration from 'gulp-duration'
import config from '../config.js'
import gutil from 'gulp-util'

gulp.task('js:clean', () => {
  return gulp.src([config.dest + '/js', config.dest + '/js_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js:clean'))
    .on('end', function(){ gutil.log('----------------- js task finished --------------------------'); });
})

gulp.task('js_vendor:clean', () => {
  return gulp.src([config.dest + '/js_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_vendor:clean'))
    .on('end', function(){ gutil.log('----------------- js_vendor task finished --------------------------'); });
})

gulp.task('js_prerender:clean', () => {
  return gulp.src([config.dest + '/js_prerender_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_prerender:clean'))
    .on('end', function(){ gutil.log('----------------- js_prerender task finished --------------------------'); });
})

gulp.task('angular_app:clean', () => {
  return gulp.src([config.dest + '/angular_app_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_app:clean'))
    .on('end', function(){ gutil.log('----------------- angular_app task finished --------------------------'); });
})

gulp.task('angular_vendor:clean', () => {
  return gulp.src([config.dest + '/angular_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_vendor:clean'))
    .on('end', function(){ gutil.log('----------------- angular_vendor task finished --------------------------'); });
})

gulp.task('react_setup:clean', ['react_setup:clean_files', 'react_setup:clean_dir']);

gulp.task('react_setup:clean_files', () => {
  return gulp.src([config.dest + '/react_setup/**/*.js'], { read: false })
    .pipe(duration('react_setup:clean_files'))
})

gulp.task('react_setup:clean_dir', ['react_setup:clean_files'], () => {
  return gulp.src([config.dest + '/react_setup'], { read: false })
    .pipe(duration('react_setup:clean_dir'))
    .on('end', function(){ gutil.log('----------------- react_setup task finished --------------------------'); });
})

gulp.task('react_signup:clean', ['react_signup:clean_files', 'react_signup:clean_dir']);

gulp.task('react_signup:clean_files', () => {
  return gulp.src([config.dest + '/react_signup/**/*.js'], { read: false })
    .pipe(duration('react_signup:clean_files'))
})

gulp.task('react_signup:clean_dir', ['react_signup:clean_files'], () => {
  return gulp.src([config.dest + '/react_signup'], { read: false })
    .pipe(duration('react_signup:clean_dir'))
    .on('end', function(){ gutil.log('----------------- react_signup task finished --------------------------'); });
})

gulp.task('react_goal_create:clean', ['react_goal_create:clean_files', 'react_goal_create:clean_dir']);

gulp.task('react_goal_create:clean_files', () => {
  return gulp.src([config.dest + '/react_goal_create/**/*.js'], { read: false })
    .pipe(duration('react_goal_create:clean_files'))
})

gulp.task('react_goal_create:clean_dir', ['react_goal_create:clean_files'], () => {
  return gulp.src([config.dest + '/react_goal_create'], { read: false })
    .pipe(duration('react_goal_create:clean_dir'))
    .on('end', function(){ gutil.log('----------------- react_goal_create task finished --------------------------'); });
})

gulp.task('react_goal_approval:clean', ['react_goal_approval:clean_files', 'react_goal_approval:clean_dir']);

gulp.task('react_goal_approval:clean_files', () => {
  return gulp.src([config.dest + '/react_goal_approval/**/*.js'], { read: false })
    .pipe(duration('react_goal_approval:clean_files'))
})

gulp.task('react_goal_approval:clean_dir', ['react_goal_approval:clean_files'], () => {
  return gulp.src([config.dest + '/react_goal_approval'], { read: false })
    .pipe(duration('react_goal_approval:clean_dir'))
    .on('end', function(){ gutil.log('----------------- react_goal_approval task finished --------------------------'); });
})

gulp.task('css:clean', () => {
  return gulp.src([config.dest + '/css', config.dest + '/css_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('css:clean'))
    .on('end', function(){ gutil.log('----------------- css task finished --------------------------'); });
})
