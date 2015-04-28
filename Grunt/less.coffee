module.exports = (grunt) ->
  dist:
    files:
      '<%= config.app %>/webroot/dest/css/sample.css': ['<%= config.app %>/webroot/less/sample.less']
