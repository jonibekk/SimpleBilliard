module.exports = (grunt) ->
  'use strict'

  grunt.registerTask 'js', ['coffeelint','coffee','concat','uglify','copy:js','docco']
  grunt.registerTask 'css', ['lesslint','less','autoprefixer','cssmin','copy:css','styledocco']
  grunt.registerTask 'default', ['watch']