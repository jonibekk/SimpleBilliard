module.exports = (grunt) ->
  'use strict'

  require('time-grunt') grunt

  require('load-grunt-config') grunt,
    init: true
    data: config:
      app:'app'
      dist: 'dist'