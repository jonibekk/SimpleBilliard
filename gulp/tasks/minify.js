import gulp from "gulp";
import rename from "gulp-rename";
import uglify from "gulp-uglify";
import duration from "gulp-duration";
import config from "../config.js";

// production環境のみ圧縮する

gulp.task("js:uglify", () => {
  let obj = gulp.src(config.dest + "/js_cat/" + config.js.output.file_name + '.js');
  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(uglify());
  }

  return obj.pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js.output.path))
    .pipe(duration('js:uglify'));
})

gulp.task("js_feed:uglify", () => {
    let obj = gulp.src(config.dest + "/jsfeed_cat/" + config.js.output.feed_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_feed:uglify'));
});

gulp.task("js_goals:uglify", () => {
    let obj = gulp.src(config.dest + "/jsgoals_cat/" + config.js.output.goals_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_goals:uglify'));
});

gulp.task("js_team:uglify", () => {
    let obj = gulp.src(config.dest + "/jsteam_cat/" + config.js.output.team_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_team:uglify'));
});

gulp.task("js_user:uglify", () => {
    let obj = gulp.src(config.dest + "/jsuser_cat/" + config.js.output.user_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_user:uglify'));
});

gulp.task("js_evaluation:uglify", () => {
    let obj = gulp.src(config.dest + "/jseval_cat/" + config.js.output.evaluation_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_evaluation:uglify'));
});

gulp.task("js_evaluator_settings:uglify", () => {
    let obj = gulp.src(config.dest + "/jseval_cat/" + config.js.output.evaluator_setting_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
    .pipe(gulp.dest(config.js.output.path))
    .pipe(duration('js_evaluator_setting:uglify'));
});

gulp.task("js_payment:uglify", () => {
    let obj = gulp.src(config.dest + "/jspayment_cat/" + config.js.output.payments_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_payment:uglify'));
});

gulp.task("js_circle:uglify", () => {
    let obj = gulp.src(config.dest + "/jscircle_cat/" + config.js.output.circle_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path))
        .pipe(duration('js_circle:uglify'));
});

gulp.task("js_homepage:uglify", () => {
    let obj = gulp.src(config.dest + "/jshomepage_cat/" + config.js.output.homepage_script_name + '.js');
    if (process.env.NODE_ENV === "production") {
        obj = obj.pipe(uglify());
    }

    return obj.pipe(rename({
        suffix: '.min'
    }))
        .pipe(gulp.dest(config.js.output.path+'/homepage/'))
        .pipe(duration('js_homepage:uglify'));
});

gulp.task("js_vendor:uglify", () => {
  return gulp.src(config.dest + "/js_vendor_cat/" + config.js_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_vendor.output.path))
    .pipe(duration('js_vendor:uglify'))
})

gulp.task("js_prerender:uglify", () => {
  return gulp.src(config.dest + "/js_prerender_cat/" + config.js_prerender.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_prerender.output.path))
    .pipe(duration('js_prerender:uglify'))
})

gulp.task("js_prerender_exif:uglify", () => {
  return gulp.src(config.dest + "/js_prerender_exif_cat/" + config.js_prerender_exif.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.js_prerender_exif.output.path))
    .pipe(duration('js_prerender_exif:uglify'))
})

gulp.task("angular_vendor:uglify", () => {
  return gulp.src(config.dest + "/angular_vendor_cat/" + config.angular_vendor.output.file_name + '.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_vendor.output.path))
    .pipe(duration('angular_vendor:uglify'))
})

gulp.task("angular_app:uglify", () => {
  let obj = gulp.src(config.dest + "/angular_app_cat/" + config.angular_app.output.file_name + '.js');
  if (process.env.NODE_ENV === "production") {
    obj = obj.pipe(uglify({
      options: {
        beautify: true,
        mangle: true
      }
    }));
  }

  return obj.pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(config.angular_app.output.path))
    .pipe(duration('angular_app:uglify'))
})
