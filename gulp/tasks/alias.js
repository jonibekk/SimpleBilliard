import gulp from 'gulp';
import runSequence from 'run-sequence';
import config from '../config.js';
import webpack from "webpack";
import webpackProdConfig from "../webpack.config.js";
import webpackDevConfig from "../webpack.dev.config.js";
import gutil from 'gulp-util';

gulp.task('jsbuild', done => {
    return runSequence([
        'js_feed',
        'js_goals',
        'js_team',
        'js_user',
        'js_evaluation',
        'js_evaluator_settings',
        'js_app',
        'js_vendor',
        'js_payment',
        'js_circle',
        'js_homepage'], done)
});

gulp.task('build', done => {
  return runSequence(['js', 'css'], done)
});

gulp.task('js', done => {
  return runSequence([
      'js_feed',
      'js_goals',
      'js_team',
      'js_user',
      'js_evaluation',
      'js_evaluator_settings',
      'js_app',
      'js_vendor',
      'js_prerender_exif',
      'js_prerender',
      'js_payment',
      'js_circle',
      'js_homepage',
      'angular_app',
      'angular_vendor',
      'react'], done)
});

// js feed
gulp.task('js_feed', done => {
  return runSequence(
    'js_feed:concat',
    'js_feed:uglify',
    'js_feed:clean',
    done
  );
});

// js goals
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

// js user
gulp.task('js_evaluator_settings', done => {
    return runSequence(
        'js_evaluator_settings:concat',
        'js_evaluator_settings:uglify',
        'js_evaluator_settings:clean',
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

// js circle
gulp.task('js_circle', done => {
    return runSequence(
        'js_circle:concat',
        'js_circle:uglify',
        'js_circle:clean',
        done
    );
});

// js homepage
gulp.task('js_homepage', done => {
  return runSequence(
    'js_homepage:concat',
    'js_homepage:uglify',
    'js_homepage:clean',
    done
  );
});

// js app
gulp.task('js_app', done => {
  return runSequence(
    'js:eslint',
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

// js prerender exif
gulp.task('js_prerender_exif', done => {
  return runSequence(
    'js_prerender_exif:concat',
    'js_prerender_exif:uglify',
    'js_prerender_exif:clean',
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
  return runSequence(
    'react:all',
    done
  )
})

// css
gulp.task('react:all', () => {
  // run webpack
  const webpackConfig = process.env.NODE_ENV === "production" ? webpackProdConfig : webpackDevConfig;
  webpack(webpackConfig, function (err, stats) {
    if (err) throw new gutil.PluginError("webpack:build", err);
    gutil.log("[react]", stats.toString({
      colors: true
    }));
  });
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
    'less:common',
    'less:pages',
    'less:homepage',
    done
  )
})
