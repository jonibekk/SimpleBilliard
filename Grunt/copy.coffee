# Copies remaining files to places other tasks can use
module.exports = dist: files: [
  expand: true
  dot: true
  cwd: '<%= config.app %>'
  dest: '<%= config.dist %>'
  src: [
    'content/**/**.*'
    '.htaccess'
    'images/{,*/}*.webp'
    'styles/fonts/{,*/}*.*'
  ]
]
module.exports = (grunt) ->
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
