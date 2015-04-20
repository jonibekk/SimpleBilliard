module.exports = (grunt) ->
  concat:
    options:
      separator: ';'
    cat:
      src: ['dest/jssrc/**/*.js']
      dest: 'dest/jscat/concat.js'
