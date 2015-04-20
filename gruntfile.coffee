module.exports = (grunt) ->
  require('load-grunt-config')(grunt)
    loadGruntTasks:
      config: require('./package.json')
      scope: 'devDependencies'

  grunt.registerTask('default',['watch'])