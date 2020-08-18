import path from "path";
import webpack from "webpack";
import config from "./config.js";
import minimist from "minimist";

function getReactEntries() {
  // Can specify watching react app as option
  // e.g. "gulp watch --react_app signup"
  const argv = minimist(process.argv.slice(2), config.argOptions);

  let entries = {};
  // hot reload用設定
  const devClient = [
    'webpack/hot/dev-server',
    'webpack-hot-middleware/client',
  ];

  const react_apps = argv.react_app ? [argv.react_app] : config.react;
  // entrypointを動的に作成。(hot reload用設定もentrypoint毎に付加しなければならない)
  react_apps.map((app_name) => {
    entries[`react_${app_name}`] = [...devClient, `./js/react/${app_name}/app.js`];
  });
  return entries;
}

export default {
  watch: true,
  devtool: '#inline-source-map',
  context: path.join(process.cwd(), config.assets_dir),
  entry: getReactEntries(),
  output: {
    path: process.cwd() + "/app/webroot/js",
    publicPath: '/js/',
    filename: '[name]_app.min.js',
  },
  module: {
    loaders: [
      {
        loaders: ['react-hot-loader', 'babel-loader'],
        test: /\.js?$/,
        exclude: /node_modules/,
        include: [
          path.join(process.cwd(), config.assets_dir)
        ],
      },
      {
        test: /\.css$/,
        loaders: ['style-loader', 'css-loader'],
      },
    ]
  },
  plugins: [
    new webpack.ProvidePlugin({
      Promise: 'es6-promise-promise'
    }),
    new webpack.LoaderOptionsPlugin({
      debug: true
    }),
    new webpack.optimize.OccurrenceOrderPlugin(),
    // If it enable, page get slow. So once I commented these line
    // new webpack.optimize.CommonsChunkPlugin({
    //   name: 'react_vendors',
    //   filename: 'react_vendors.min.js'
    // }),
    new webpack.NoEmitOnErrorsPlugin(),
    new webpack.HotModuleReplacementPlugin(),
  ],
}