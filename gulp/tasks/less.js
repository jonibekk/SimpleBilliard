import gulp from 'gulp'
import plumber from 'gulp-plumber'
import accord from 'gulp-accord'
import duration from 'gulp-duration'
import config from '../config.js'
import handleErrors from '../util/handleErrors'

gulp.task('css:less', () => {
  return gulp.src(config.less.src)
    .pipe(plumber())
    .pipe(accord('less'))
    .on('error', handleErrors)
    .pipe(gulp.dest(config.dest + '/css'))
    .pipe(duration('css:less'))
})
