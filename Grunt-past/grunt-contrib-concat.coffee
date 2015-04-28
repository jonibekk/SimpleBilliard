module.exports = (grunt) ->
  options:
    separator: ';'
  cat:
    src: ['dest/jssrc/**/*.js']
    dest: 'dest/jscat/concat.js'
