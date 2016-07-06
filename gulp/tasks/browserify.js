import gulp from 'gulp'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'
import duration from 'gulp-duration'
import config from '../config.js'

gulp.task('react_setup:browserify', () => {
  return browserify({entries: [config.react_setup.src]})
    .transform(babelify, {presets: ["es2015", "react"], plugins: ["babel-plugin-transform-object-assign"]})
    .bundle()
    .pipe(source(config.react_setup.output.file_name + '.js'))
    .pipe(gulp.dest(config.dest + '/react_setup'))
    .pipe(duration('react_setup:browserify'))
})
