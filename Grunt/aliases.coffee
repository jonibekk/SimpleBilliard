module.exports = (grunt) ->
  'use strict'

  #
  # setting of js task.
  # This task is watched. The task is going to run, when coffeescript(app/webroot/coffee/**/*.coffee) is changed.
  #
  grunt.registerTask 'js', ['coffeelint:release','coffee','concat:js','uglify','copy:js','copy:jsMap','docco:release','concat:jsDocs','clean:dest','clean:docco']

  #
  # setting of css task.
  # This task is watched. The task is going to run, when less(app/webroot/less/**/*.less) is changed.
  #
  grunt.registerTask 'css', ['lesslint','less','autoprefixer','cssmin','copy:css','copy:cssMap','styleguide','concat:cssDocs','clean:dest','clean:styledocco']

  #
  # setting of gruntdocs task.
  # This task is NOT watched.
  #
  grunt.registerTask 'gruntdocs', ['coffeelint:gruntlint','docco:gruntDocs','concat:gruntDocs','clean:gruntDocs']

  #
  # alltask (!watch)
  #
  grunt.registerTask 'all', ['js','css','gruntdocs']

  #
  # setting of watch task.
  #
  grunt.registerTask 'default', ['watch']
