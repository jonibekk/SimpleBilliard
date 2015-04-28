module.exports = (grunt) ->
  my_target:
    files:
      'dest/jsmin/goalous.min.js': ['dest/jscat/concat.js']

