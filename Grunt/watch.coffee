module.exports = (grunt) ->
  options:
    spawn: false
    livereload: true

  coffee:
    files: ['<%= config.coffee %>/**/*.coffee']
    tasks: ['js']
  less:
    files: ['<%= config.less %>/**/*.less']
    tasks: ['css']
