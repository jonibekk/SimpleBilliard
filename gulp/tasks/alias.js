import gulp from 'gulp';
import runSequence from 'run-sequence';
import config from '../config.js';
import webpack from "webpack";
import webpackProdConfig from "../webpack.config.js";
import webpackDevConfig from "../webpack.dev.config.js";
import gutil from 'gulp-util';

gulp.task('jsbuild', done => {
    return runSequence([
        'js_home',
        'js_goals',
        'js_team',
        'js_user',
        'js_evaluation',
        'js_app',
        'js_vendor',
        'js_payment'], done)
});

gulp.task('build', done => {
  return runSequence(['js', 'css'], done)
});

gulp.task('js', done => {
  return runSequence([
      'js_home',
      'js_goals',
      'js_team',
      'js_user',
      'js_evaluation',
      'js_app',
      'js_vendor',
      'js_prerender',
      'js_payment',
      'angular_app',
      'angular_vendor',
      'react'], done)
});

// js home
gulp.task('js_home', done => {
  return runSequence(
    'js_home:concat',
    'js_home:uglify',
    'js_home:clean',
    done
  );
});

// js home
gulp.task('js_goals', done => {
  return runSequence(
    'js_goals:concat',
    'js_goals:uglify',
    'js_goals:clean',
    done
  );
});

// js team
gulp.task('js_team', done => {
    return runSequence(
        'js_team:concat',
        'js_team:uglify',
        'js_team:clean',
        done
    );
});

// js user
gulp.task('js_user', done => {
    return runSequence(
        'js_user:concat',
        'js_user:uglify',
        'js_user:clean',
        done
    );
});

// js user
gulp.task('js_evaluation', done => {
    return runSequence(
        'js_evaluation:concat',
        'js_evaluation:uglify',
        'js_evaluation:clean',
        done
    );
});

// js payment
gulp.task('js_payment', done => {
    return runSequence(
        'js_payment:concat',
        'js_payment:uglify',
        'js_payment:clean',
        done
    );
});

// js app
gulp.task('js_app', done => {
  return runSequence(
    'js:eslint',
    'js:coffeelint',
    'js:coffee',
    'js:concat',
    'js:uglify',
    'js:clean',
    done
  )
})

// js prerender
gulp.task('js_prerender', done => {
  return runSequence(
    'js_prerender:concat',
    'js_prerender:uglify',
    'js_prerender:clean',
    done
  )
})

// js vendor
gulp.task('js_vendor', done => {
  return runSequence(
    'js_vendor:concat',
    'js_vendor:uglify',
    'js_vendor:clean',
    done
  )
})

// angular app
gulp.task('angular_app', done => {
  return runSequence(
    'angular_app:concat',
    'angular_app:uglify',
    'angular_app:clean',
    done
  )
})

// angular vendor
gulp.task('angular_vendor', done => {
  return runSequence(
    'angular_vendor:concat',
    'angular_vendor:uglify',
    'angular_vendor:clean',
    done
  )
})

// react all application
gulp.task('react', done => {
  //TODO:webpackからeslintを使用

  // run webpack
  const webpackConfig = process.env.NODE_ENV === "production" ? webpackProdConfig : webpackDevConfig;
  webpack(webpackConfig, function(err, stats) {
    if(err) throw new gutil.PluginError("webpack:build", err);
    gutil.log("[react]", stats.toString({
      colors: true
    }));
  });
  return done;
})

// css
gulp.task('css', done => {
  return runSequence(
    'css_vendor',
    'less',
    done
  )
})

// less
gulp.task('less', done => {
  return runSequence(
    // 'css:lesshint',
    'less:common',
    'less:pages',
    done
  )
})
