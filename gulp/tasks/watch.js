import gulp from 'gulp'
import config from '../config.js'
import runSequence from 'run-sequence'

gulp.task('watch', done => {
  return runSequence(
    ['css:watch', 'js:watch', 'react:watch'],
    done
  )
})

gulp.task('js:watch', () => {
  var watcher = gulp.watch(config.js.watch_files, ['js_app'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('js_vendor:watch', () => {
  var watcher = gulp.watch(config.js_vendor.watch_files, ['js_vendor'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('angular_app:watch', () => {
  var watcher = gulp.watch(config.angular_app.watch_files, ['angular_app'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('angular_vendor:watch', () => {
  var watcher = gulp.watch(config.angular_vendor.watch_files, ['angular_vendor'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('react_setup:watch', () => {
  var watcher = gulp.watch(config.react_setup.watch_files, ['react_setup'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('css:watch', () => {
  var watcher = gulp.watch([...config.css.watch_files, ...config.less.watch_files], ['css'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})
