module.exports = (grunt) ->
  target:
    files:[
      expand: true
      cwd: '<%= config.dest %>/csspre'
      src: ['*.css','!*.min.css']
      dest: '<%= config.dest %>/cssmin'
      ext: '.min.css'
    ]
