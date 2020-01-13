/*
    gulp配置文件
 */

const elixir = require('laravel-elixir');
const path = require('path');
const webpackStream = require('webpack-stream');

elixir.config.assetsPath = 'resources';

elixir.config.browserlist = {
    browsers: [ '> 1%', 'last 2 versions', 'Firefox >= 20', 'Opera 12.1', 'IE >= 9']
};

elixir(function (mix) {
    /*
     |--------------------------------------------------------------------------
     | Build JS files
     |--------------------------------------------------------------------------
     */
     mix.task('run-webpack');
    /*
     |--------------------------------------------------------------------------
     | Copy fonts and other files
     |--------------------------------------------------------------------------
     */

});

gulp.task('run-webpack',  function(callback) {
  return gulp.src('./resources/client-entry.js')
    .pipe(webpackStream(require('./webpack.config.js')))
    .pipe(gulp.dest('./public/js'));
});

