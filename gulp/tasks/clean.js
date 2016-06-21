import gulp from 'gulp'
import rimraf from 'gulp-rimraf'

gulp.task('js:clean', function() {
 return gulp.src('./.tmp/js', { read: false })
   .pipe(rimraf({ force: true }))
})

gulp.task('css:clean', function() {
  return gulp.src('./.tmp/css', { read: false })
    .pipe(rimraf({ force: true }))
})

gulp.task('react:clean', function() {
 return gulp.src('./.tmp/react', { read: false })
   .pipe(rimraf({ force: true }))
})
