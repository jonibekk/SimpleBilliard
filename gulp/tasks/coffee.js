import gulp from 'gulp'
import plumber from 'gulp-plumber'
import coffee from 'gulp-coffee'
import config from '../config.js'

gulp.task('js:coffee', () => {
  return gulp.src(config.coffee.src)
    .pipe(plumber())
    .pipe(coffee())
    .pipe(gulp.dest(config.dest + '/js'))
})
