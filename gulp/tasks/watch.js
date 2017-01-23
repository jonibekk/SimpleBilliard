import gulp from 'gulp'
import config from '../config.js'

gulp.task('watch', ['css:watch', 'js:watch',  'angular_app:watch', 'react_setup:watch', 'react_signup:watch', 'react_goal_create:watch', 'react_goal_edit:watch', 'react_goal_approval:watch', 'react_goal_search:watch', 'react_kr_column:watch'])

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

config.react_apps.map((app_name) => {
  gulp.task(`${app_name}:watch`, () => {
    const  watcher = gulp.watch(config[app_name].watch_files, [app_name])

    watcher.on('change', event => {
      /* eslint-disable no-console */
      console.log('File ' + event.path + ' was ' + event.type + ', running tasks...')
      /* eslint-enable no-console */
    })
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
