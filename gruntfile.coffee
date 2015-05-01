#
# ** Grunt guideline v0.54 **
# time-grunt is used for measuring the time How long does Grunt take.
# load-grunt-config is used for 3 purposes.
# 1. devide gruntfile to gruntfiles. (e.g. grunt/coffee.coffee, grunt/less.coffee
# 2. omit writing loadNpmTasks.
# 3. set Variables for directory. (e.g. app, webroot, coffee, docs
#
# Next you should check grunt/aliases.coffee
#

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
      docs: 'docs'
      grunt: 'grunt'
