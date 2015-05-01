module.exports = (grunt) ->

  #
  # make coffee docs for Goalous
  #
  release:
    src: ['<%= config.coffee %>/**/*.coffee']
    options:
      output: '<%= config.docs %>/docco'

  #
  # make coffee docs for Grunt
  # If you change the gruntfile.coffee or grunt/**/*.coffee, you MUST use ```grunt gruntdocs``` on terminal, or I'll never forgive you.
  #
  gruntDocs:
    src: [
      'gruntfile.coffee'
      '<%= config.grunt %>/**/*.coffee'
    ]
    options:
      output: '<%= config.docs %>/grunt'
