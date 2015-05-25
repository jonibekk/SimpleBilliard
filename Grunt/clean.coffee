module.exports = (grunt) ->

  #
  # clean the directory "dest"
  #
  dest:
    "<%= config.dest %>"

  # Todo : clean the directory for docs
  #
  # NOTICE: We need this task NO LONGER.
  #
  # styledocco:
  #   "<%= config.docs %>/styledocco"
  # docco:
  #   "<%= config.docs %>/docco"
  # gruntDocs:
  #   "<%= config.docs %>/grunt"

  # Todo : clean the files the past version
  #   jsRelease:
  #     "<%= config.webroot %>/js"
  #   cssRelease:
  #     "<%= config.webroot %>/css"
