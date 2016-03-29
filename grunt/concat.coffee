module.exports = (grunt) ->

  #
  # setting of concat.
  # This task make js files to a js file.
  #
  # task js is for JavaScript to be released.
  # The other tasks is for docs.
  js:
    src: ['<%= config.dest %>/jssrc/**/*.js']
    dest: '<%= config.dest %>/jscat/concat.js'
    options:
      separator: ';'

  vendors:
    src:[
      '<%= config.js %>/vendor/jquery-1.11.1.min.js'
      '<%= config.js %>/vendor/bootstrap.js'
      '<%= config.js %>/vendor/jasny-bootstrap.js'
      '<%= config.js %>/vendor/bootstrapValidator.js'
      '<%= config.js %>/vendor/bootstrap-switch.js'
      '<%= config.js %>/vendor/bvAddition.js'
      '<%= config.js %>/vendor/pnotify.custom.js'
      '<%= config.js %>/vendor/jquery.nailthumb.1.1.js'
      '<%= config.js %>/vendor/jquery.autosize.js'
      '<%= config.js %>/vendor/jquery.lazy.js'
      '<%= config.js %>/vendor/lightbox-custom.js'
      '<%= config.js %>/vendor/jquery.showmore.src.js'
      '<%= config.js %>/vendor/placeholders.js'
      '<%= config.js %>/vendor/customRadioCheck.js'
      '<%= config.js %>/vendor/select2.js'
      '<%= config.js %>/vendor/bootstrap-datepicker.js'
      '<%= config.js %>/vendor/locales/bootstrap-datepicker.ja.js'
      '<%= config.js %>/vendor/moment.js'
      '<%= config.js %>/vendor/pusher.js'
      '<%= config.js %>/vendor/dropzone.js'
      '<%= config.js %>/vendor/jquery.flot.js'
      '<%= config.js %>/vendor/jquery.balanced-gallery.js'
      '<%= config.js %>/vendor/imagesloaded.pkgd.js'
      '<%= config.js %>/vendor/bootstrap.youtubepopup.js'
      '<%= config.js %>/vendor/fastClick.js'
      '<%= config.js %>/vendor/require.js'
      '<%= config.js %>/vendor/exif.js'
      '<%= config.js %>/gl_basic.js'
    ]
    dest: '<%= config.dest %>/jscat/vendors.js'
    options:
      separator: ';'

  ng_app:
    src: ['<%= config.js %>/app/**/*.js']
    dest: '<%= config.dest %>/jscat/ng_app.js'
    options:
      separator: ';'


  # controllerはmin化するとバグが発生するのでそのままjsディレクトリに。
  ng_controller:
    src: ['<%= config.js %>/controller/**/*.js']
    dest: '<%= config.js %>/ng_controller.js'
    options:
      separator: ';'

  ng_vendors:
    src: [
      '<%= config.js %>/vendor/angular/angular.js'
      '<%= config.js %>/vendor/angular/angular-ui-router.js'
      '<%= config.js %>/vendor/angular/angular-route.js'
      '<%= config.js %>/vendor/angular/angular-translate.js'
      '<%= config.js %>/vendor/angular/angular-translate-loader-static-files.js'
      '<%= config.js %>/vendor/angular/ui-bootstrap-tpls-0.13.0.js'
      '<%= config.js %>/vendor/angular/angular-pnotify.js'
      '<%= config.js %>/vendor/angular/angular-sanitize.js'
    ]
    dest: '<%= config.dest %>/jscat/ng_vendors.js'
    options:
      separator: ';'

  # ng_vendor:
  #   src: [
  #     '<%= config.js %>/vendor/angular/**/*.js'
  #     '!<%= config.js %>/vendor/angular/angular.js'
  #     '!<%= config.js %>/vendor/angular/**/*.min.js'
  #   ]
  #   dest: '<%= config.js %>/ng_vendor.js'
  #   options:
  #     separator: ';'

  react_vendors:
    src: [
      '<%= config.js %>/vendor/react/react.js',
      '<%= config.js %>/vendor/react/react-dom.js',
      '<%= config.js %>/vendor/react/react-router.js'
    ]
    dest: '<%= config.dest %>/jscat/react_vendors.js'
    options:
      separator: ';'

  # modules:
  #   src: '<%= config.js %>/modules/*.js'
  #   dest: '<%= config.js %>/modules/release/modules.js'

  jsDocs:
    src: ['<%= config.docs %>/js/**/*.html','!<%= config.docs %>/js/index.html']
    dest: '<%= config.docs %>/js/index.html'

  cssDocs:
    src: ['<%= config.docs %>/css/**/*.html','!<%= config.docs %>/styledocco/index.html']
    dest: '<%= config.docs %>/css/index.html'

  gruntDocs:
    src: ['<%= config.docs %>/grunt/**/*.html','!<%= config.docs %>/grunt/index.html']
    dest: '<%= config.docs %>/grunt/index.html'

  css:
    src: [
      '<%= config.css %>/goalstrap.css'
      '<%= config.css %>/jasny-bootstrap.css'
      '<%= config.css %>/font-awesome.css'
      '<%= config.css %>/jquery.nailthumb.1.1.css'
      '<%= config.css %>/bootstrapValidator.css'
      '<%= config.css %>/bootstrap-switch.css'
      '<%= config.css %>/pnotify.custom.css'
      '<%= config.css %>/lightbox.css'
      '<%= config.css %>/showmore.css'
      '<%= config.css %>/bootstrap-ext-col.css'
      '<%= config.css %>/customRadioCheck.css'
      '<%= config.css %>/select2.css'
      '<%= config.css %>/select2-bootstrap.css'
      '<%= config.css %>/datepicker3.css'
      '<%= config.css %>/style.css'
      '<%= config.css %>/nav.css'
      '<%= config.css %>/nav_media.css'
    ]
    dest: '<%= config.css %>/release.css'
    sourceMap: true
