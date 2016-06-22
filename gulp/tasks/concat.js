import gulp from 'gulp'
import plumber from 'gulp-plumber'
import concat from 'gulp-concat'
import config from '../config.js'

gulp.task('js:concat', () => {
  return gulp.src(config.js.src + [config.dest + '/js/**/*.js'])
    .pipe(plumber())
    .pipe(concat(config.js.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_cat'))
})

gulp.task('js_vendor:concat', () => {
  return gulp.src(config.js_vendor.src)
    .pipe(plumber())
    .pipe(concat(config.js_vendor.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_vendor_cat'))
})

gulp.task('css:concat', () => {
  return gulp.src(['./.tmp/css/*.css'])
    .pipe(plumber())
    .pipe(concat('app.css'))
    .pipe(gulp.dest('./.tmp/css'))
})
