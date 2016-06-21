import gulp from 'gulp'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'
import config from '../config.js'

gulp.task('react:browserify', () => {
  return browserify({entries: [config.react.src]})
    .transform(babelify, {presets: ["es2015", "react"]})
    .bundle()
    .pipe(source(config.react.output.file_name + '.js'))
    .pipe(gulp.dest('./.tmp/react'))
})
