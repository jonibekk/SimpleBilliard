module.exports = (grunt) ->
  options:
    separator: ';'
  cat:
    src: ['<%= config.app %>/webroot/dest/jssrc/**/*.js']
    dest: '<%= config.app %>/webroot/dest/jscat/concat.js'
