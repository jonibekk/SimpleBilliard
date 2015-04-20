module.exports = (grunt) ->
  less:
    dist:
      files:
        'dest/csssrc/goalous-src.css': ['less/goalous.less']
