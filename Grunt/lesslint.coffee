module.exports = (grunt) ->

  #
  # Setting of lesslint
  # grunt-lesslint requires grunt-contrib-csslint
  #
  # I think we should set more details of less rules.
  lesslint:
    src: '<%= config.less %>/**/**.less'
