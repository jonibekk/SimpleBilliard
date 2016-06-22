import gulp from 'gulp'
import plumber from 'gulp-plumber'
import coffeelint from 'gulp-coffeelint'
import config from '../config.js'

gulp.task('js:coffeelint', () => {
  return gulp.src(config.coffee.src)
    .pipe(plumber())
    .pipe(coffeelint('./coffeelint.json'))
    .pipe(coffeelint.reporter())
})
