module.exports = (grunt) ->
  dist:
    options:
      'no-minify': true
    files:
      "<%= config.docs %>/styledocco": "<%= config.less %>/common.less"
