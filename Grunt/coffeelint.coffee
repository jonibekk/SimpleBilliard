module.exports = (grunt) ->

  #
  # setting of coffeelint
  # coffeescript is checked, based on coffeelint.json.
  #
  # release is for Goalous
  # gruntlint is for gruntfiles
  #
  release:
    files:
      src: ['<%= config.coffee %>/**/*.coffee']
    options:
      configFile: 'coffeelint.json'

  gruntlint:
    files:
      src: [
        'gruntfile.coffee'
        '<%= config.grunt %>/**/*.coffee'
      ]
    options:
      configFile: 'coffeelint.json'
