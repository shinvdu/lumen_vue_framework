const path = require('path');
const projectRoot = path.resolve(__dirname, '../');
const ExtractTextPlugin = require('extract-text-webpack-plugin')
console.log(projectRoot);
// const vueConfig = require('./vue-loader.config');

module.exports ={
  entry: './resources/client-entry.js',
  output: {
      path: path.resolve(__dirname, './public/js'), //打包输出路径
      publicPath: '/js/', //请求路径
      filename: 'client-bundle.js'
  },
  module: {
    loaders: [
      {test: /\.js$/, loaders:
        [
        'babel-loader',
        'eslint-loader'
        ],
        exclude: /node_modules/
      },
      {
        enforce: 'pre',
        test: /\.vue$/,
        loader: 'eslint-loader',
        exclude: /node_modules/
      },
      // but use vue-loader for all *.vue files
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.png|jpg|gif$/,
        // loader: 'url-loader?limit=4000&name=[hash:8].[name].[ext]&outputPath=img/&publicPath=/js/'
        loader: 'url-loader',
        options: {
          limit: 4000,
          name: '[hash:8].[name].[ext]',
          outputPath: 'img/',
          publicPath: '/js/'
        }
      },
      { test: /\.scss$/, loader: ExtractTextPlugin.extract({
        fallback: 'style-loader',
        use: ['css-loader', 'sass-loader']
      }) }
      ]
  },
  plugins: [
    new ExtractTextPlugin("styles.css")
  ]
};
