module.exports = (grunt) ->

  #
  # setting of concat.
  # This task make js files to a js file.
  js:
    src: ['<%= config.dest %>/jssrc/**/*.js']
    dest: '<%= config.dest %>/jscat/concat.js'
    options:
      separator: ';'

  jsDocs:
    src: ['<%= config.docs %>/docco/**/*.html']
    dest: '<%= config.docs %>/frontend/js.html'

  cssDocs:
    src: ['<%= config.docs %>/styledocco/**/*.html','!<%= config.docs %>/styledocco/index.html']
    dest: '<%= config.docs %>/frontend/css.html'

  gruntDocs:
    src: ['<%= config.docs %>/grunt/**/*.html']
    dest: '<%= config.docs %>/frontend/grunt.html'
