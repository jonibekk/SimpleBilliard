module.exports = (grunt) ->
  pkg: grunt.file.readJSON 'package.json'

  browserify:
    dist:
      options:
        transform: [['babelify', {presets: ['es2015', 'react']}]]
      src: ['**/*.jsx']
      dest: '<%= config.dest %>/jssrc/'

  grunt.loadNpmTasks('grunt-contrib-watch')
  grunt.loadNpmTasks('grunt-browserify')

  grunt.registerTask('default', ['browserify'])
