import gulp from 'gulp'
import cssmin from 'gulp-cssmin'
import rename from 'gulp-rename'
import plumber from 'gulp-plumber'
import uglify from 'gulp-uglify'
import config from '../config.js'

gulp.task("js:uglify", () => {
  gulp.src(config.dest + "/js_cat/" + config.js.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.js.output.path))
})

gulp.task("js_vendor:uglify", () => {
  gulp.src(config.dest + "/js_vendor_cat/" + config.js_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_vendor.output.path))
})

gulp.task("angular_vendor:uglify", () => {
  gulp.src(config.dest + "/angular_vendor_cat/" + config.angular_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_vendor.output.path))
})

gulp.task("angular_app:uglify", () => {
  gulp.src(config.dest + "/angular_app_cat/" + config.angular_app.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_app.output.path))
})

gulp.task("react:uglify", () => {
  gulp.src('./.tmp/react/' + config.react.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.react.output.path))
})

gulp.task('css:min', () => {
  return gulp.src('./.tmp/css/app.css')
    .pipe(plumber())
    .pipe(cssmin())
    .pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.css.output.path))
})
