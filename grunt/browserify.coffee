module.exports = (grunt) ->

  react:
    options:
      transform: [['babelify', {
        compact: false,
        presets: ['es2015', 'react'],
        plugins: ['babel-plugin-transform-object-assign']
      }]]
    src: ['<%= config.react %>/setup_guide/app.js']
    dest: '<%= config.dest %>/jssrc/react_app.js'
