# Copies remaining files to places other tasks can use
module.exports = (grunt) ->

  #
  #  [*.min.js]
  #  dest/jsmin -> app/webroot/js
  #
  js:
    expand: true
    cwd: '<%= config.dest %>/jsmin/'
    dest: '<%= config.webroot %>/js/'
    src: [
      '*.min.js'
    ]

  #
  # [*.min.css]
  # dest/cssmin -> app/webroot/css
  #
  css:
    expand: true
    cwd: '<%= config.dest %>/cssmin/'
    dest: '<%= config.webroot %>/css/'
    src: [
      '*.min.css'
    ]
