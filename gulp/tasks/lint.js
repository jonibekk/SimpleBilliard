import gulp from 'gulp'
import plumber from 'gulp-plumber'
import coffeelint from 'gulp-coffeelint'
import duration from 'gulp-duration'
import eslint from 'gulp-eslint'
import lesshint from 'gulp-lesshint'
import config from '../config.js'

gulp.task('js:coffeelint', () => {
  return gulp.src(config.coffee.src)
    .pipe(plumber())
    .pipe(coffeelint('./coffeelint.json'))
    .pipe(duration('js:coffeelint'))
})

gulp.task('js:eslint', () => {
  return gulp.src(config.js.src)
    .pipe(plumber())
    .pipe(eslint({ useEslintrc: true }))
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())
    .pipe(duration('js:eslint'))
})

gulp.task('react_setup:eslint', () => {
  return gulp.src(config.react_setup.src)
    .pipe(plumber())
    .pipe(eslint({ useEslintrc: true }))
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())
    .pipe(duration('js:eslint'))
})

gulp.task('css:lesshint', function() {
  return gulp.src(config.less.src)
    .pipe(lesshint())
    .pipe(lesshint.reporter())
});
