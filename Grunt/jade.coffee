# Setting of Jade
module.exports = (grunt) ->

  # docs task is for Docs index
  docs:
    files: [
      expand: yes
      cwd: 'docs/'
      src: '**/*.jade'
      dest: 'docs/'
      ext: '.html'
    ]
