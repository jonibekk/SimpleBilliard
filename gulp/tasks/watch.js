import gulp from 'gulp'
import config from '../config.js'

gulp.task('watch', ['css:watch', 'js:watch',  'angular_app:watch', 'react_setup:watch', 'react_signup:watch'])

gulp.task('js:watch', () => {
  const watcher = gulp.watch([...config.js.watch_files, ...config.coffee.watch_files], ['js_app'])

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

gulp.task('react_setup:watch', () => {
  const  watcher = gulp.watch(config.react_setup.watch_files, ['react_setup'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})

gulp.task('react_signup:watch', () => {
  const watcher = gulp.watch(config.react_signup.watch_files, ['react_signup'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})

gulp.task('react_create_goal:watch', () => {
  const watcher = gulp.watch(config.react_create_goal.watch_files, ['react_create_goal'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})

gulp.task('css:watch', () => {
  const watcher = gulp.watch([...config.css.watch_files, ...config.less.watch_files], ['css'])

  watcher.on('change', event => {
    /* eslint-disable no-console */
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
    /* eslint-enable no-console */
  })
})
