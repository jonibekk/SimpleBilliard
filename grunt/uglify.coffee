module.exports = (grunt) ->

  # Setting of uglify, minifying for JavaScript.
  my_target:
    files:
      '<%= config.app %>/webroot/dest/jsmin/goalous.min.js': [
        '<%= config.app %>/webroot/dest/jscat/concat.js'
      ]

  ng_target:
    files:
      '<%= config.app %>/webroot/dest/jsmin/ng_app.min.js': [
        '<%= config.app %>/webroot/dest/jscat/ng_app.js'
      ]
