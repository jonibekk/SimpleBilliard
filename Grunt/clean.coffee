module.exports = (grunt) ->

  #
  # clean the directory "dest"
  #
  dest:
    "<%= config.dest %>"

  # Todo : clean the directory for docs
  #   styledocco:
  #     "<%= config.docs %>/styledocco"
  #   docco:
  #     "<%= config.docs %>/docco"

  # Todo : clean the files the past version
  #   jsRelease:
  #     "<%= config.webroot %>/js"
  #   cssRelease:
  #     "<%= config.webroot %>/css"
