module.exports = (grunt) ->

  # Setting of uglify, minifying for JavaScript.
  my_target:
    files:
      '<%= config.app %>/webroot/dest/jsmin/goalous.min.js': ['<%= config.app %>/webroot/dest/jscat/concat.js']
