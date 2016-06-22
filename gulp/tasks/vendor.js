import gulp from 'gulp'
import plumber from 'gulp-plumber'
import config from '../config.js'

gulp.task('js:vendor', () =>  {
  return gulp.src(config.js.modules)
    .pipe(plumber())
    .pipe(gulp.dest(config.dest + '/js_vendors'))
})
