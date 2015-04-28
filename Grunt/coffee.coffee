module.exports = (grunt) ->
  compile:
    options:
      bare:true
    files: [
      expand: 'true'
      cwd: '<%= config.app %>/webroot/coffee/'
      src: ['**/*.coffee']
      dest: '<%= config.app %>/webroot/dest/js/'
      ext: '.js'
    ]