module.exports = (grunt) ->

  #
  # Setting of lesslint
  # grunt-lesslint requires grunt-contrib-csslint
  #
  # I think we should set more details of less rules.
  # Need to check only goalous.less because All less files are imported to goalous.less !!
  lesslint:
    src: '<%= config.less %>/goalous.less'
    options:
      csslint:
        'font-sizes': false
