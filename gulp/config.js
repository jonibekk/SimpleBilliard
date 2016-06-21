const config = {
  dest: './app/webroot/dest',
  coffee: {
    src: ['./app/webroot/coffee/**/*.coffee'],
    output: {
      file_name: 'goalous',
      path: './public/js'
    },
    wacth_files: ['./app/webroot/coffee/**/*.coffee']
  },
  js: {
    src: {
      js: ['./app/webroot/js/gl_basic.js'],
      coffee: ['./app/webroot/coffee/**/*.coffee']
    },
    modules: [
      './app/webroot/js/vendor/jquery-1.11.1.min.js'
      './app/webroot/js/vendor/bootstrap.js'
      './app/webroot/js/vendor/jasny-bootstrap.js'
      './app/webroot/js/vendor/bootstrapValidator.js'
      './app/webroot/js/vendor/bootstrap-switch.js'
      './app/webroot/js/vendor/bvAddition.js'
      './app/webroot/js/vendor/pnotify.custom.js'
      './app/webroot/js/vendor/jquery.nailthumb.1.1.js'
      './app/webroot/js/vendor/jquery.autosize.js'
      './app/webroot/js/vendor/jquery.lazy.js'
      './app/webroot/js/vendor/lightbox-custom.js'
      './app/webroot/js/vendor/jquery.showmore.src.js'
      './app/webroot/js/vendor/placeholders.js'
      './app/webroot/js/vendor/customRadioCheck.js'
      './app/webroot/js/vendor/select2.js'
      './app/webroot/js/vendor/bootstrap-datepicker.js'
      './app/webroot/js/vendor/locales/bootstrap-datepicker.ja.js'
      './app/webroot/js/vendor/moment.js'
      './app/webroot/js/vendor/pusher.js'
      './app/webroot/js/vendor/dropzone.js'
      './app/webroot/js/vendor/jquery.flot.js'
      './app/webroot/js/vendor/jquery.balanced-gallery.js'
      './app/webroot/js/vendor/imagesloaded.pkgd.js'
      './app/webroot/js/vendor/bootstrap.youtubepopup.js'
      './app/webroot/js/vendor/fastClick.js'
      './app/webroot/js/vendor/require.js'
      './app/webroot/js/vendor/exif.js'
      './app/webroot/js/gl_basic.js'
    ],
    output: {
      file_name: {
        vendors: 'vendors',
        js: 'goalous'
      },
      path: './public/js',
    },
    wacth_files: ['./app/webroot/js/gl_basic.js', './app/webroot/coffee/**/*.coffee']
  },
  angular: {
    src: './frontend/js/react/app.js',
    output: {
      file_name: 'react',
      path: './public/js'
    },
    wacth_files: ['./frontend/js/react/**/*.js', './frontend/js/react/**/*.jsx'],
  },
  react: {
    src: './frontend/js/react/app.js',
    output: {
      file_name: 'react',
      path: './public/js'
    },
    wacth_files: ['./frontend/js/react/**/*.js', './frontend/js/react/**/*.jsx'],
  },
  css: {
    src: {
      css: [
        './frontend/css/**/*.css'
      ],
      less: [
        './frontend/sass/**/*.less'
      ]
    },
    modules: [
      './node_modules/skeleton-css/css/normalize.css',
      './node_modules/skeleton-css/css/skeleton.css',
      './node_modules/purecss/build/pure-min.css'
    ],
    wacth_files: ['./frontend/sass/**/*.scss', './frontend/css/**/*.css'],
    output: {
      file_name: 'app',
      path: './public/css'
    }
  }
}

export default config
