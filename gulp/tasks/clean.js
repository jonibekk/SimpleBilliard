import gulp from 'gulp'
import rimraf from 'gulp-rimraf'
import del from 'del'
import config from '../config.js'

gulp.task('js:clean', function() {
  return gulp.src([config.dest + '/js', config.dest + '/js_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('js_vendor:clean', function() {
  return gulp.src([config.dest + '/js_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('angular_app:clean', function() {
  return gulp.src([config.dest + '/angular_app_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('angular_vendor:clean', function() {
  return gulp.src([config.dest + '/angular_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('react_setup:clean', function() {
  // return gulp.src([config.dest + '/react_setup'], { read: false })
  //   .pipe(rimraf({ force: true }))
})

gulp.task('css:clean', function() {
  return gulp.src([config.dest + '/css', config.dest + '/css_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})
