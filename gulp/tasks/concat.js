import gulp from 'gulp'
import plumber from 'gulp-plumber'
import concat from 'gulp-concat'
import autoprefixer from 'gulp-autoprefixer'
import duration from 'gulp-duration'
import ngAnnotate from 'gulp-ng-annotate'
import config from '../config.js'

gulp.task('js_home:concat', () => {
    return gulp.src([...config.js.pages.home])
        .pipe(plumber())
        .pipe(concat(config.js.output.home_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jshome_cat'))
        .pipe(duration('js_home:concat'))
});

gulp.task('js_goals:concat', () => {
    return gulp.src([...config.js.pages.goals])
        .pipe(plumber())
        .pipe(concat(config.js.output.goals_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jsgoals_cat'))
        .pipe(duration('js_goals:concat'))
});

gulp.task('js_team:concat', () => {
    return gulp.src([...config.js.pages.team])
        .pipe(plumber())
        .pipe(concat(config.js.output.team_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jsteam_cat'))
        .pipe(duration('js_team:concat'))
});

gulp.task('js:concat', () => {
  return gulp.src([...config.js.src, config.dest + '/js/*.js'])
    .pipe(plumber())
    .pipe(concat(config.js.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_cat'))
    .pipe(duration('js:concat'))
})

gulp.task('js_vendor:concat', () => {
  return gulp.src(config.js_vendor.src)
    .pipe(plumber())
    .pipe(concat(config.js_vendor.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_vendor_cat'))
    .pipe(duration('js_vendor:concat'))
})

gulp.task('js_prerender:concat', () => {
  return gulp.src(config.js_prerender.src)
    .pipe(plumber())
    .pipe(concat(config.js_prerender.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_prerender_cat'))
    .pipe(duration('js_prerender:concat'))
})

gulp.task('angular_app:concat', () => {
  return gulp.src(config.angular_app.src)
    .pipe(plumber())
    .pipe(ngAnnotate({
      remove: true,
      add: true,
      single_quotes: true
    }))
    .pipe(concat(config.angular_app.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/angular_app_cat'))
    .pipe(duration('angular_app:concat'))
})

gulp.task('angular_vendor:concat', () => {
  return gulp.src(config.angular_vendor.src)
    .pipe(plumber())
    .pipe(concat(config.angular_vendor.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/angular_vendor_cat'))
    .pipe(duration('angular_vendor:concat'))
})

gulp.task('css:concat', () => {
  return gulp.src([...config.css.src, config.dest + '/css/*.css'])
    .pipe(plumber())
    .pipe(autoprefixer())
    .pipe(concat(config.css.output.file_name + '.css'))
    .pipe(gulp.dest(config.dest + '/css_cat'))
    .pipe(duration('css:concat'))
})
