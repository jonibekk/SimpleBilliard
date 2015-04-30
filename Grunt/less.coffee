module.exports = (grunt) ->
  dist:
    files:
      '<%= config.dest %>/csssrc/sample.css': ['<%= config.less %>/sample.less']
