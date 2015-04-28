module.exports = (grunt) ->
  options:
    separator: ';'
  cat:
    src: ['<%= config.dest %>/jssrc/**/*.js']
    dest: '<%= config.dest %>/jscat/concat.js'
