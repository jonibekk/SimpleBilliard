module.exports = (grunt) ->

  #
  # I know that grunt-styleguide is NO LONGER ACTIVELY MAINTAINED.
  # But I have no idea of alternative. So, I continue to use it.
  #
  # Setting of styledocco, styleguide generator for CSS.
  # We should set no-minify is true, or we have to wait longer time for generatoring.
  dist:
    options:
      'no-minify': true
    files:
      "<%= config.docs %>/css": "<%= config.less %>/goalous.less"
