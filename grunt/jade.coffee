# Setting of Jade
module.exports = (grunt) ->

  # docs task is for Docs index
  docs:
    files: [
      expand: yes
      cwd: 'docs/local'
      src: '**/*.jade'
      dest: '<%= config.docs %>'
      ext: '.html'
    ]
