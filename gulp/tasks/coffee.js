import gulp from 'gulp'
import plumber from 'gulp-plumber'
import coffee from 'coffee'
import config from '../config.js'

gulp.task('js:coffee', () => {
  return gulp.src(config.js.src.coffee)
    .pipe(plumber())
    .pipe(coffee())
    .pipe(gulp.dest(config.dest + '/js'))
})
