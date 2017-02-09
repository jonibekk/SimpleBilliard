import gulp from 'gulp'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'
import duration from 'gulp-duration'
import config from '../config.js'
import handleErrors from '../util/handleErrors'
import wrap from 'gulp-wrap'

config.react_apps.map((app_name) => {
  gulp.task(`${app_name}:browserify`, () => {
    var bundler = browserify({
      entries: [config[app_name].src],
      transform: [
        [babelify, config.browserify.transform.babelify_options],
        ['browserify-css', {global: true}]
      ]
    });
      return bundler.bundle()
      .on('error', handleErrors)
      .pipe(source(config[app_name].output.file_name + '.js'))
      .pipe(wrap('(function () { var define = undefined; <%=contents%> })();'))
      .pipe(gulp.dest(config.dest + `/${app_name}`))
      .pipe(duration(`${app_name}:browserify`))
  })
})
