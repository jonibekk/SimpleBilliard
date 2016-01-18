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

  ng_app:
    src: ['<%= config.js %>/app/**/*.js']
    dest: '<%= config.dest %>/jscat/ng_app.js'
    options:
      separator: ';'

  ng_controller:
    src: ['<%= config.js %>/controller/**/*.js']
    dest: '<%= config.js %>/ng_controller.js'
    options:
      separator: ';'

  # ng_vendor:
  #   src: [
  #     '<%= config.js %>/vendor/angular/**/*.js'
  #     '!<%= config.js %>/vendor/angular/angular.js'
  #     '!<%= config.js %>/vendor/angular/**/*.min.js'
  #   ]
  #   dest: '<%= config.js %>/ng_vendor.js'
  #   options:
  #     separator: ';'

  # modules:
  #   src: '<%= config.js %>/modules/*.js'
  #   dest: '<%= config.js %>/modules/release/modules.js'

  jsDocs:
    src: ['<%= config.docs %>/js/**/*.html','!<%= config.docs %>/js/index.html']
    dest: '<%= config.docs %>/js/index.html'

  cssDocs:
    src: ['<%= config.docs %>/css/**/*.html','!<%= config.docs %>/styledocco/index.html']
    dest: '<%= config.docs %>/css/index.html'

  gruntDocs:
    src: ['<%= config.docs %>/grunt/**/*.html','!<%= config.docs %>/grunt/index.html']
    dest: '<%= config.docs %>/grunt/index.html'
