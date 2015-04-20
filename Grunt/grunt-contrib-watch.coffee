module.exports = (grunt) ->
  options:
    spawn: false
    livereload: true

  coffee:
    files: ['coffee/**/*.coffee']
    tasks: ['jstask']
  less:
    files: ['less/**/*.less']
    tasks: ['csstask']
