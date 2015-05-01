module.exports = (grunt) ->
  'use strict'

  grunt.registerTask 'js', ['coffeelint','coffee','concat','uglify','copy:js','docco:release','clean']

  grunt.registerTask 'css', ['lesslint','less','autoprefixer','cssmin','copy:css','styleguide','clean']

  grunt.registerTask 'gruntdocs', ['docco:gruntdocs']

  grunt.registerTask 'default', ['watch']
