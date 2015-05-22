module.exports = (grunt) ->

  # Setting of watch.
  # NOTICE: Now, I don't set livereload yet. I wanna set it someday.
  # If you set "spawn: false", you can use grunt-contrib-watch much faster.
  # We set 2 watch tasks
  # 1. coffee
  # 2. less
  options:
    spawn: false
    livereload: true

  coffee:
    files: ['<%= config.coffee %>/**/*.coffee']
    tasks: ['js']
  less:
    files: ['<%= config.less %>/**/*.less']
    tasks: ['css']
