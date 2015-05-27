module.exports = (grunt) ->
  'use strict'

  #
  # setting of js task.
  # This task is watched. The task is going to run, when coffeescript(app/webroot/coffee/**/*.coffee) is changed.
  #
  grunt.registerTask 'js', [
    'coffeelint:release'
    'coffee'
    'concat:js'
    'uglify'
    'copy:js'
    'copy:jsMap'
    'clean:dest'
  ]

  #
  # setting of css task.
  # This task is watched. The task is going to run, when less(app/webroot/less/**/*.less) is changed.
  #
  grunt.registerTask 'css', [
    'lesslint'
    'less'
    'autoprefixer'
    'cssmin'
    'copy:css'
    'copy:cssMap'
    'clean:dest'
  ]

  #
  # chef task (!watch)
  #
  grunt.registerTask 'chef', [
    'js'
    'css'
  ]

  #
  # setting of jsdocs task.
  # This task is NOT watched.
  #
  grunt.registerTask 'jsdocs', [
    'docco:release'
    'concat:jsDocs'
  ]

  #
  # setting of cssdocs task.
  # This task is NOT watched.
  #
  grunt.registerTask 'cssdocs', [
    'copy:readme'
    'styleguide'
    'concat:cssDocs'
  ]

  #
  # setting of gruntdocs task.
  # This task is NOT watched.
  #
  grunt.registerTask 'gruntdocs', [
    'coffeelint:gruntlint'
    'docco:gruntDocs'
    'concat:gruntDocs'
  ]

  #
  # DO NOT use on Virtual Machine.
  # combine 3 above tasks.
  # Run `grunt docs` before push.
  #
  grunt.registerTask 'docs', [
    'jsdocs'
    'cssdocs'
    'gruntdocs'
    'jade'
    'connect'
  ]

  #
  # setting of watch task.
  #
  grunt.registerTask 'default', ['watch']
