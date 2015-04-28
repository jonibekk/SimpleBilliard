module.exports = (grunt) ->
  coffeelint:
    files:
      src: ['<%= config.app %>/webroot/coffee/**/*.coffee']
    options:
      indentation:
        value: 2
        level: 'warn'
      'no_trailing_semicolons':
        level: 'warn'
