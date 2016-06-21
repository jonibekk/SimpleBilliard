import gulp from 'gulp'
import plumber from 'gulp-plumber'
import less from 'gulp-less'
import cssmin from 'gulp-cssmin'
import autoprefixer from 'gulp-autoprefixer'
import config from '../config.js'

gulp.task('css:sass', () => {
  return gulp.src(config.css.src.sass)
    .pipe(plumber())
    .pipe(less())
    .pipe(autoprefixer())
    .pipe(gulp.dest('./.tmp/css'))
})
