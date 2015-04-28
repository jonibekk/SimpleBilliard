module.exports = (grunt) ->
  'use strict'

  grunt.registerTask 'js', ['coffeelint','coffee','concat','uglify','copy:jscopy','docco']
  grunt.registerTask 'css', ['lesslint','less','autoprefixer','cssmin','copy:csscopy','styledocco']
  grunt.regiterTask 'default', ['watch']