import gulp from 'gulp'
import plumber from 'gulp-plumber'
import coffee from 'gulp-coffee'
import duration from 'gulp-duration'
import config from '../config.js'

gulp.task('js:coffee', () => {
  return gulp.src(config.coffee.src)
    .pipe(plumber())
    .pipe(coffee())
    .pipe(gulp.dest(config.dest + '/js'))
    .pipe(duration('js:coffee'))
})
