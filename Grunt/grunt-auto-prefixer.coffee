module.exports = (grunt) ->
  autoprefixer:
    multiple_files:
      expand: true
      flatten: true
      src: 'dest/csssrc/goalous-src.css' # -> src/css/file1.css, src/css/file2.css
      dest: 'dest/csspre/'     # -> dest/css/file1.css, dest/css/file2.css
      ext: '-pre.css'
