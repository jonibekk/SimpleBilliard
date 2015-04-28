module.exports = (grunt) ->
  dev:
    src: ['<%= config.app %>/webroot/coffee/**/*.coffee']
  options:
    output: './docs/docco'
