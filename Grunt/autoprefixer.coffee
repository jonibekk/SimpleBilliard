module.exports = (grunt) ->

  #
  # using autoprefixer
  # expand is
  # flatten is
  multiple_files:
    expand: true
    flatten: true

      # app/webroot/dest/csssrc/**/*.css ->
    src: '<%= config.dest %>/csssrc/**/*.css'

      # -> app/webroot/dest/csspre/**/*.css
    dest: '<%= config.dest %>/csspre/'
    ext: '.css'
