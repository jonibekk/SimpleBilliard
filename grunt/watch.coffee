module.exports = (grunt) ->

  # Setting of watch.
  # NOTICE: I don't set livereload by Grunt. Use Global browser-sync :-) I wanna set it by Grunt someday.
  # If you set "spawn: false", you can use grunt-contrib-watch much faster.
  # We set 2 watch tasks
  # 1. coffee
  # 2. less
  options:
    spawn: false

  js:
    files: [
      '<%= config.coffee %>/**/*.coffee'
      '<%= config.js %>/**/*.js',
    ]
    tasks: ['js']
  css:
    files: [
      '<%= config.less %>/**/*.less'
      '<%= config.css %>/**/*.css'
    ]
    tasks: ['css']
