module.exports = (grunt) ->

  #
  # setting of concat.
  # This task make js files to a js file.
  #
  # task js is for JavaScript to be released.
  # The other tasks is for docs.
  js:
    src: ['<%= config.dest %>/jssrc/**/*.js']
    dest: '<%= config.dest %>/jscat/concat.js'
    options:
      separator: ';'

  jsDocs:
    src: ['<%= config.docs %>/js/**/*.html']
    dest: '<%= config.docs %>/js/index.html'

  cssDocs:
    src: ['<%= config.docs %>/css/**/*.html','!<%= config.docs %>/styledocco/index.html']
    dest: '<%= config.docs %>/css/index.html'

  gruntDocs:
    src: ['<%= config.docs %>/grunt/**/*.html']
    dest: '<%= config.docs %>/grunt/index.html'
