import gulp from "gulp";
import config from "../config.js";
import webpack from "webpack";
import webpackDebugConfig from "../webpack.browsersync.config.js";
import webpackDevMiddleware from 'webpack-dev-middleware';
import webpackHotMiddleware from 'webpack-hot-middleware';
import browserSync from 'browser-sync';
import path from "path";
import proxyMiddleware from 'http-proxy-middleware';

gulp.task('watch', ['css:watch', 'js:watch', 'angular_app:watch', 'react:watch'])


gulp.task('js:watch', () => {
  const watcher = gulp.watch([...config.js.watch_files, ...config.coffee.watch_files], ['jsbuild'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})

gulp.task('angular_app:watch', () => {
  const watcher = gulp.watch(config.angular_app.watch_files, ['angular_app'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})

// reactのみbrowser-syncを試験的導入。hot reloadが可能
gulp.task('react:watch', () => {
  const bundler = webpack(webpackDebugConfig);
  const proxy = proxyMiddleware('/', {target: 'http://192.168.50.4'});
  browserSync({
    server: {
      baseDir: path.join(process.cwd(), config.compiled_assets_dir),
      port: 3000,
      middleware: [
        webpackDevMiddleware(bundler, {
          publicPath: webpackDebugConfig.output.publicPath,
          stats: {colors: true},
          watchOptions: {
            poll: true,
            ignored: /node_modules/
          }
        }),
        webpackHotMiddleware(bundler),
        proxy,
      ]
    },
  });
})

gulp.task('css:watch', () => {
  const watcher = gulp.watch([...config.css.watch_files, ...config.less.watch_files], ['css'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})
