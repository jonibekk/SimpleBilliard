module.exports = (grunt) ->
  compile:
    options:
      bare:true
    files: [
      expand: 'true'
      cwd: 'coffee/'
      src: ['**/*.coffee']
      dest: 'dest/jssrc/'
      ext: '.js'
    ]