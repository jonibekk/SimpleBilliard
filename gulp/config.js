const assets_dir = assets_dir + ''
const config = {
  dest: assets_dir + '/dest',
  js: {
    src: [assets_dir + '/js/gl_basic.js'],
    output: {
      file_name: 'goalous'
      path: assets_dir + '/js',
    },
    wacth_files: [assets_dir + '/js/gl_basic.js', assets_dir + '/coffee/**/*.coffee']
  },
  js_vendor: {
    src: [
      assets_dir + '/js/vendor/jquery-1.11.1.min.js',
      assets_dir + '/js/vendor/bootstrap.js',
      assets_dir + '/js/vendor/jasny-bootstrap.js',
      assets_dir + '/js/vendor/bootstrapValidator.js',
      assets_dir + '/js/vendor/bootstrap-switch.js',
      assets_dir + '/js/vendor/bvAddition.js',
      assets_dir + '/js/vendor/pnotify.custom.js'
      assets_dir + '/js/vendor/jquery.nailthumb.1.1.js',
      assets_dir + '/js/vendor/jquery.autosize.js',
      assets_dir + '/js/vendor/jquery.lazy.js',
      assets_dir + '/js/vendor/lightbox-custom.js',
      assets_dir + '/js/vendor/jquery.showmore.src.js',
      assets_dir + '/js/vendor/placeholders.js',
      assets_dir + '/js/vendor/customRadioCheck.js',
      assets_dir + '/js/vendor/select2.js',
      assets_dir + '/js/vendor/bootstrap-datepicker.js',
      assets_dir + '/js/vendor/locales/bootstrap-datepicker.ja.js',
      assets_dir + '/js/vendor/moment.js',
      assets_dir + '/js/vendor/pusher.js',
      assets_dir + '/js/vendor/dropzone.js',
      assets_dir + '/js/vendor/jquery.flot.js',
      assets_dir + '/js/vendor/jquery.balanced-gallery.js',
      assets_dir + '/js/vendor/imagesloaded.pkgd.js',
      assets_dir + '/js/vendor/bootstrap.youtubepopup.js',
      assets_dir + '/js/vendor/fastClick.js',
      assets_dir + '/js/vendor/require.js',
      assets_dir + '/js/vendor/exif.js',
      assets_dir + '/js/gl_basic.js'
    ],
    output: {
      file_name: 'vendors',
      path: assets_dir + '/js',
    }
  },
  coffee: {
    src: [assets_dir + '/coffee/**/*.coffee'],
    output: {
      file_name: 'goalous',
      path: './public/js'
    },
    wacth_files: [assets_dir + '/coffee/**/*.coffee']
  },
  angular: {
    src: './frontend/js/react/app.js',
    output: {
      file_name: 'react',
      path: assets_dir + './public/js'
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
