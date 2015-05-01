module.exports = (grunt) ->

  release:
    src: ['<%= config.app %>/webroot/coffee/**/*.coffee']
    options:
      output: '<%= config.docs %>/docco'

  gruntdocs:
    src: [
      'gruntfile.coffee'
      '<%= config.grunt %>/**/*.coffee'
    ]
    options:
      output: '<%= config.docs %>/docco'
