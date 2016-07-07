module.exports = (grunt) ->

  #
  # setting of less compile
  # less -> css
  # I'm concerned with less-plugin-clean-css
  # sourceMap must be true.
  dist:
    files:
      '<%= config.dest %>/csssrc/goalous.css': ['<%= config.less %>/goalous.less']
    options:
      sourceMap: true

  docs:
    files:
      '<%= config.docs %>/local/docs.min.css':['<%= config.docs%>/local/less/docs.less']
    options:
      compress: true
