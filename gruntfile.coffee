module.exports = (grunt) ->
  'use strict'

  require('time-grunt') grunt

  require('load-grunt-config') grunt,
    init: true
    data: config:
      app: 'app'
      webroot: 'app/webroot'
      dest: 'app/webroot/dest'
      coffee: 'app/webroot/coffee'
      less: 'app/webroot/less'
