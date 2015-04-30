module.exports = (grunt) ->
  multiple_files:
    expand: true
    flatten: true
    src: '<%= config.dest %>/csssrc/**/*.css' # -> src/css/file1.css, src/css/file2.css
    dest: '<%= config.dest %>/csspre/'     # -> dest/css/file1.css, dest/css/file2.css
    ext: '.css'
