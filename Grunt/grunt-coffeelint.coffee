module.exports = (grunt) ->
  coffeelint:
    one:
      files:
        src: ['coffee/**/*.coffee']
      options:
        indentation:
          value: 2
          level: 'warn'
        'no_trailing_semicolons':
          level: 'warn'
