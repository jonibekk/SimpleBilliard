module.exports = (grunt) ->
  pkg = grunt.file.readJSON 'package.json'
  grunt.initConfig

    watch:
      options:
        spawn: false

      coffee:
        files: ['coffee/**/*.coffee']
        tasks: ['jstask']
      less:
        files: ['less/**/*.less']
        tasks: ['csstask']

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

    coffee:
      compile:
        files: [
          expand: 'true'
          cwd: 'coffee/'
          src: ['**/*.coffee']
          dest: 'dest/jssrc/'
          ext: '.js'
        ]

    concat:
      options:
        separator: ';'
      cat:
        src: ['dest/jssrc/**/*.js']
        dest: 'dest/jscat/concat.js'

    uglify:
      my_target:
        files:
          'dest/jsmin/goalous.min.js': ['dest/jscat/concat.js']

    docco:
      dev:
        src: ['coffee/**/*.coffee']
      options:
        output: '../docs/docco'

    less:
      dist:
        files:
          'dest/csssrc/goalous-src.css': ['less/goalous.less']

    autoprefixer:
      multiple_files:
        expand: true
        flatten: true
        src: 'dest/csssrc/goalous-src.css' # -> src/css/file1.css, src/css/file2.css
        dest: 'dest/csspre/'     # -> dest/css/file1.css, dest/css/file2.css
        ext: '-pre.css'

    cssmin:
      target:
        files:
          'dest/cssmin/goalous.min.css': 'dest/csspre/goalous-src-pre.css'


    styleguide:
      dist:
        options:
          'no-minify': true
        files:
          "../docs/styledocco": "less/common.less"
    copy:
      csscopy:
        expand: true
        cwd: 'dest/cssmin/'
        src: 'goalous.min.css'
        dest: '../app/webroot/css/'
      jscopy:
        expand: true
        cwd: 'dest/jsmin/'
        src: 'goalous.min.js'
        dest: '../app/webroot/js/'


  grunt.loadNpmTasks 'grunt-contrib-coffee'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-less'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-autoprefixer'
  grunt.loadNpmTasks 'grunt-coffeelint'
  grunt.loadNpmTasks 'grunt-docco'
  grunt.loadNpmTasks 'grunt-styleguide'
  grunt.loadNpmTasks 'grunt-contrib-copy'
  grunt.registerTask 'csstask', ['styleguide', 'less', 'autoprefixer', 'cssmin', 'copy:csscopy']
  grunt.registerTask 'jstask', ['coffeelint', 'docco', 'coffee', 'concat', 'uglify', 'copy:jscopy']
  grunt.registerTask 'default', ['watch']
