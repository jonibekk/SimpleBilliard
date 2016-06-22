import gulp from 'gulp'
import plumber from 'gulp-plumber'
import less from 'gulp-less'
import cssmin from 'gulp-cssmin'
import config from '../config.js'

gulp.task('css:less', () => {
  return gulp.src(config.less.src)
    .pipe(plumber())
    .pipe(less())
    .pipe(gulp.dest(config.dest + '/css'))
})
