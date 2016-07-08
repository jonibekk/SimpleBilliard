module.exports = (grunt) ->

  #
  # compile coffeescript to javascript
  # bare must be true
  # sourcemap must be true, or you cannot inspect coffeescript
  # coffee/**/*.coffee -> dest/jssrc
  #
  compile:
    options:
      bare:true
      sourceMap: true
    files: [
      expand: 'true'
      cwd: '<%= config.coffee %>'
      src: ['**/*.coffee']
      dest: '<%= config.dest %>/jssrc/'
      ext: '.js'
    ]
