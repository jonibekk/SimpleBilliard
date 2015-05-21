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
  #  [*.js.src]
  #  dest/jssrc -> app/webroot/js
  #
  jsMap:
    expand: true
    cwd: '<%= config.dest %>/jssrc/'
    dest: '<%= config.webroot %>/js/'
    src: [
      '*.js.map'
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

  #
  # [*.css.map]
  # dest/csssrc -> app/webroot/css
  #
  cssMap:
    expand: true
    cwd: '<%= config.dest %>/csssrc/'
    dest: '<%= config.webroot %>/css/'
    src: [
      '*.css.map'
    ]
