module.exports = (grunt) ->

  #
  # setting of less compile
  # less -> css
  # I'm concerned with less-plugin-clean-css
  # sourceMap must be true.
  dist:
    files:
      '<%= config.dest %>/csssrc/sample.css': ['<%= config.less %>/sample.less']
    options:
      sourceMap: true
