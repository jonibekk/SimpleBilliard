import gulp from 'gulp'
import rimraf from 'gulp-rimraf'
import del from 'del'
import runSequence from 'run-sequence'
import duration from 'gulp-duration'
import config from '../config.js'

gulp.task('js:clean', () => {
  return gulp.src([config.dest + '/js', config.dest + '/js_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js:clean'))
})

gulp.task('js_vendor:clean', () => {
  return gulp.src([config.dest + '/js_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_vendor:clean'))
})

gulp.task('js_prerender:clean', () => {
  return gulp.src([config.dest + '/js_prerender_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_prerender:clean'))
})

gulp.task('angular_app:clean', () => {
  return gulp.src([config.dest + '/angular_app_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_app:clean'))
})

gulp.task('angular_vendor:clean', () => {
  return gulp.src([config.dest + '/angular_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_vendor:clean'))
})

gulp.task('react_setup:clean', ['react_setup:clean_files', 'react_setup:clean_dir']);

gulp.task('react_setup:clean_files', () => {
  return gulp.src([config.dest + '/react_setup/**/*.js'], { read: false })
    .pipe(duration('react_setup:clean_files'))
})

gulp.task('react_setup:clean_dir', ['react_setup:clean_files'], cb => {
  return gulp.src([config.dest + '/react_setup'], { read: false })
    .pipe(duration('react_setup:clean_dir'))
})

gulp.task('css:clean', () => {
  return gulp.src([config.dest + '/css', config.dest + '/css_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('css:clean'))
})
