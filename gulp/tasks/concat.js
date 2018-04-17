import gulp from 'gulp'
import plumber from 'gulp-plumber'
import concat from 'gulp-concat'
import duration from 'gulp-duration'
import ngAnnotate from 'gulp-ng-annotate'
import config from '../config.js'

gulp.task('js_feed:concat', () => {
    return gulp.src([...config.js.pages.feed])
        .pipe(plumber())
        .pipe(concat(config.js.output.feed_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jsfeed_cat'))
        .pipe(duration('js_feed:concat'))
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

gulp.task('js_user:concat', () => {
    return gulp.src([...config.js.pages.user])
        .pipe(plumber())
        .pipe(concat(config.js.output.user_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jsuser_cat'))
        .pipe(duration('js_user:concat'))
});

gulp.task('js_evaluation:concat', () => {
    return gulp.src([...config.js.pages.evaluation])
.pipe(plumber())
    .pipe(concat(config.js.output.evaluation_script_name + '.js'))
    .pipe(gulp.dest(config.dest + '/jseval_cat'))
    .pipe(duration('js_evaluation:concat'))
});

gulp.task('js_evaluator_settings:concat', () => {
    return gulp.src([...config.js.pages.evaluator_settings])
.pipe(plumber())
    .pipe(concat(config.js.output.evaluator_setting_script_name + '.js'))
    .pipe(gulp.dest(config.dest + '/jseval_cat'))
    .pipe(duration('js_evaluator_settings:concat'))
});

gulp.task('js_payment:concat', () => {
    return gulp.src([...config.js.pages.payments])
        .pipe(plumber())
        .pipe(concat(config.js.output.payments_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jspayment_cat'))
        .pipe(duration('js_payment:concat'))
});

gulp.task('js_circle:concat', () => {
    return gulp.src([...config.js.pages.circle_pins])
        .pipe(plumber())
        .pipe(concat(config.js.output.circle_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jscircle_cat'))
        .pipe(duration('js_circlet:concat'))
});

gulp.task('js_homepage:concat', () => {
    return gulp.src([...config.js.pages.homepage])
        .pipe(plumber())
        .pipe(concat(config.js.output.homepage_script_name + '.js'))
        .pipe(gulp.dest(config.dest + '/jshomepage_cat'))
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

gulp.task('js_prerender_exif:concat', () => {
  return gulp.src(config.js_prerender_exif.src)
    .pipe(plumber())
    .pipe(concat(config.js_prerender_exif.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_prerender_exif_cat'))
    .pipe(duration('js_prerender_exif:concat'))
})

gulp.task('js_prerender_scroll:concat', () => {
  return gulp.src(config.js_prerender_scroll.src)
    .pipe(plumber())
    .pipe(concat(config.js_prerender_scroll.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/js_prerender_scroll_cat'))
    .pipe(duration('js_prerender_scroll:concat'))
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
