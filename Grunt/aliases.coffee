module.exports =
  default: ['coffee']
  jstask: [
    'coffeelint'
    'docco'
    'coffee'
    'concat'
    'uglify'
    'copy:jscopy'
  ]
  csstask: [
    'styleguide'
    'less'
    'autoprefixer'
    'cssmin'
    'copy:csscopy'
  ]