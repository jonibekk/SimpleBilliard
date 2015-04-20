module.exports = (grunt) ->
  require('load-grunt-config')(grunt)
    loadGruntTasks:
      pattern: '*'
      config: require('./package.json')
      scope: 'devDependencies'
    postProcess: (config) ->
    perMerge: (config, data) ->

  grunt.registerTask('default',['watch'])