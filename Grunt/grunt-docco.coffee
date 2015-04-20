module.exports = (grunt) ->
  docco:
    dev:
      src: ['coffee/**/*.coffee']
    options:
      output: '../docs/docco'
