import path from "path";
import webpack from "webpack";
import config from "./config.js";

function getReactEntries() {
  let entries = {};
  config.react.map((app_name) => {
    entries[`react_${app_name}`] = `./js/react/${app_name}/app.js`;
  });
  return entries;
}

export default {
  devtool: '#inline-source-map',
  context: path.join(process.cwd(), config.assets_dir),
  entry: getReactEntries(),
  output: {
    path: process.cwd() + "/app/webroot/js",
    publicPath: '/js/',
    filename: '[name]_app.min.js',
    jsonpFunction: 'reactVendor'
  },
  module: {
    loaders: [
      {
        loaders: ['babel-loader'],
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
    new webpack.optimize.CommonsChunkPlugin({
      name: 'react_vendors',
      filename: 'react_vendors.min.js'
    }),
    new webpack.NoEmitOnErrorsPlugin(),
  ]
}
