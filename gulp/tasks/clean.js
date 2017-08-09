import gulp from 'gulp'
import rimraf from 'gulp-rimraf'
import duration from 'gulp-duration'
import config from '../config.js'
import gutil from 'gulp-util'

gulp.task('js:clean', () => {
  return gulp.src([config.dest + '/js', config.dest + '/js_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js:clean'))
    .on('end', () => { gutil.log('----------------- js task finished --------------------------') })
})

gulp.task('js_home:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jshome_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_home:clean'))
        .on('end', () => { gutil.log('----------------- js_home task finished --------------------------') })
});

gulp.task('js_goals:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jsgoals_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_goals:clean'))
        .on('end', () => { gutil.log('----------------- js_goals task finished --------------------------') })
});

gulp.task('js_team:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jsteam_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_team:clean'))
        .on('end', () => { gutil.log('----------------- js_team task finished --------------------------') })
});

gulp.task('js_user:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jsuser_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_user:clean'))
        .on('end', () => { gutil.log('----------------- js_user task finished --------------------------') })
});

gulp.task('js_evaluation:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jseval_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_evaluation:clean'))
        .on('end', () => { gutil.log('----------------- js_evaluation task finished --------------------------') })
});

gulp.task('js_payment:clean', () => {
    return gulp.src([config.dest + '/js', config.dest + '/jspayment_cat'], { read: false })
        .pipe(rimraf({ force: true }))
        .pipe(duration('js_payment:clean'))
        .on('end', () => { gutil.log('----------------- js_payment task finished --------------------------') })
});

gulp.task('js_vendor:clean', () => {
  return gulp.src([config.dest + '/js_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_vendor:clean'))
    .on('end', () => { gutil.log('----------------- js_vendor task finished --------------------------') })
})

gulp.task('js_prerender:clean', () => {
  return gulp.src([config.dest + '/js_prerender_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('js_prerender:clean'))
    .on('end', () => { gutil.log('----------------- js_prerender task finished --------------------------') })
})

gulp.task('angular_app:clean', () => {
  return gulp.src([config.dest + '/angular_app_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_app:clean'))
    .on('end', () => { gutil.log('----------------- angular_app task finished --------------------------') })
})

gulp.task('angular_vendor:clean', () => {
  return gulp.src([config.dest + '/angular_vendor_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('angular_vendor:clean'))
    .on('end', () => { gutil.log('----------------- angular_vendor task finished --------------------------') })
})

config.react_apps.map((app_name) => {
  gulp.task(`${app_name}:clean`, [`${app_name}:clean_files`, `${app_name}:clean_dir`]);

  gulp.task(`${app_name}:clean_files`, () => {
    return gulp.src([config.dest + `/${app_name}/**/*.js`], { read: false })
      .pipe(duration(`${app_name}:clean_files`))
  })
  gulp.task(`${app_name}:clean_dir`, [`${app_name}:clean_files`], () => {
    return gulp.src([config.dest + `/${app_name}`], { read: false })
      .pipe(duration(`${app_name}:clean_dir`))
      .on('end', () => { gutil.log(`----------------- ${app_name} task finished --------------------------`) })
  })

})

gulp.task('css:clean', () => {
  return gulp.src([config.dest + '/css', config.dest + '/css_cat'], { read: false })
    .pipe(rimraf({ force: true }))
    .pipe(duration('css:clean'))
    .on('end', () => { gutil.log('----------------- css task finished --------------------------') })
})
