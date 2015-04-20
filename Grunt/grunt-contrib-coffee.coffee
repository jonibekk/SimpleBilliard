module.exports = (grunt) ->
  coffee:
    compile:
      files: [
        expand: 'true'
        cwd: 'coffee/'
        src: ['**/*.coffee']
        dest: 'dest/jssrc/'
        ext: '.js'
      ]