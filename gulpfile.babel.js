import gulp from 'gulp'
import runSequence from 'run-sequence'
import requireDir from 'require-dir'
requireDir('./gulp/tasks', {recurse: true});

// all
gulp.task('all', done => {
  return runSequence(
    ['js', 'react', 'css'],
    done
  )
})

// js
gulp.task('js', done => {
  return runSequence(
    'js:clean',
    'js:concat',
    'js:uglify',
    'js:clean',
    done
  )
})

// react
gulp.task('react', done => {
  return runSequence(
    'react:clean',
    'react:browserify',
    'react:uglify',
    'react:clean',
    done
  )
})

// css
gulp.task('css', done => {
  return runSequence(
    'css:clean',
    'css:sass',
    'css:vendor',
    'css:concat',
    'css:min',
    'css:clean',
    done
  )
})
