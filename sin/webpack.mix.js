let mix = require('laravel-mix');
let webpack = require('webpack')


mix.js('resources/js/app.js', 'public/js')
	.sass('resources/sass/app.scss', 'public/css')
	.sass('resources/sass/sin-theme.scss', 'public/css');


module.exports = {
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    })
  ]
}
