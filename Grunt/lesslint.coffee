module.exports = (grunt) ->
  lesslint:
    src: '<%= config.less %>/**/**.less'
