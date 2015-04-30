module.exports = (grunt) ->
  dist:
    options:
      name: ['Goalous CSS']
    files:
      'docs/styledocco':'<%= config.less %>/**/**.less'
