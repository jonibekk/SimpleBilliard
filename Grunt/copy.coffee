# Copies remaining files to places other tasks can use
module.exports = (grunt) ->
  js:
    expand: true
    cwd: '<%= config.dest %>/jsmin/'
    dest: '<%= config.webroot %>/js/'
    src: [
      '*.min.js'
    ]
  css:
    expand: true
    cwd: '<%= config.dest %>/cssmin/'
    dest: '<%= config.webroot %>/css/'
    src: [
      '*.min.css'
    ]
