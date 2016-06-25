const gulp = require('gulp')
const config = require('../config.js')
const duration = require('gulp-duration')
const runSequence = require('run-sequence')

gulp.task('watch', ['css:watch', 'js:watch',  'angular_app:watch', 'react_setup:watch'])

gulp.task('js:watch', () => {
  var watcher = gulp.watch(config.js.watch_files, ['js_app'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

// gulp.task('js_vendor:watch', () => {
//   var watcher = gulp.watch(config.js_vendor.watch_files, ['js_vendor'])
//   watcher.on('change', event => {
//     console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
//   })
// })

gulp.task('angular_app:watch', () => {
  var watcher = gulp.watch(config.angular_app.watch_files, ['angular_app'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

// gulp.task('angular_vendor:watch', () => {
//   var watcher = gulp.watch(config.angular_vendor.watch_files, ['angular_vendor'])
//   watcher.on('change', event => {
//     console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
//   })
// })

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
