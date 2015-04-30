module.exports = (grunt) ->
  compile:
    options:
      bare:true
    files: [
      expand: 'true'
      cwd: '<%= config.coffee %>'
      src: ['**/*.coffee']
      dest: '<%= config.dest %>/jssrc/'
      ext: '.js'
    ]
