module.exports = (grunt) ->
  copy:
    csscopy:
      expand: true
      cwd: 'dest/cssmin/'
      src: 'goalous.min.css'
      dest: '../app/webroot/css/'
    jscopy:
      expand: true
      cwd: 'dest/jsmin/'
      src: 'goalous.min.js'
      dest: '../app/webroot/js/'
