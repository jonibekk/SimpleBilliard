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
  var watcher = gulp.watch(config.js.wacth_files, ['js'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('angular:watch', () => {
  var watcher = gulp.watch(config.angular.wacth_files, ['angular'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

// react watchタスク
gulp.task('react:watch', () => {
  var watcher = gulp.watch(config.react.wacth_files, ['react'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})

gulp.task('css:watch', () => {
  var watcher = gulp.watch(config.css.wacth_files, ['css'])
  watcher.on('change', event => {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
  })
})
