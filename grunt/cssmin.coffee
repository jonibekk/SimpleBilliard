module.exports = (grunt) ->

  #
  # Setting of cssmin.
  # *.css -> *.min.css
  # excluding minified css (!*.min.css)
  # expand is
  target:
    files:[
      expand: true
      cwd: '<%= config.dest %>/csspre'
      src: ['*.css','!*.min.css']
      dest: '<%= config.dest %>/cssmin'
      ext: '.min.css'
    ]

  regacy:
    files:[
      expand: true
      cwd: '<%= config.css %>'
      src: ['release.css']
      dest: '<%= config.css %>'
      ext: '.min.css'
    ]
