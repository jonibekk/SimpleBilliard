import gulp from 'gulp'
import rimraf from 'gulp-rimraf'
import config from '../config.js'

gulp.task('js:clean', function() {
  return gulp.src([config.dest + '/js_cat', config.dest + '/js'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('js_vendor:clean', function() {
  return gulp.src([config.dest + '/js_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('angular_vendor:clean', function() {
  return gulp.src([config.dest + '/angular_app_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('angular_vendor:clean', function() {
  return gulp.src([config.dest + '/angular_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('css:clean', function() {
  return gulp.src('./.tmp/css', { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('react:clean', function() {
  return gulp.src('./.tmp/react', { read: false })
    .pipe(rimraf({ force: true }))
})
