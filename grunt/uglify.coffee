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

  ng_vendors:
    files:
      '<%= config.app %>/webroot/dest/jsmin/ng_vendors.min.js': [
        '<%= config.app %>/webroot/dest/jscat/ng_vendors.js'
      ]

  # ng_controller:
  #   files:
  #     '<%= config.app %>/webroot/dest/jsmin/ng_controller.min.js': [
  #       '<%= config.app %>/webroot/dest/jscat/ng_controller.js'
  #     ]

  react_target:
    files:
      '<%= config.app %>/webroot/dest/jsmin/react_app.min.js': [
        '<%= config.app %>/webroot/dest/jssrc/react_app.js'
      ]

  vendors_target:
    files:
      '<%= config.app %>/webroot/js/vendors.min.js': [
        '<%= config.app %>/webroot/dest/jscat/vendors.js'
      ]
