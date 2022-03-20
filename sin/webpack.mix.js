let mix = require('laravel-mix');
let webpack = require('webpack')


mix.js('resources/js/app.js', 'public/js').vue({ version: 2 });
mix.sass('resources/sass/app.scss', 'public/css');
mix.sass('resources/sass/sin-theme.scss', 'public/css');

mix.override((webpackConfig) => {
  webpackConfig.resolve.modules = [
    "node_modules",
    __dirname + "/vendor/spatie/laravel-medialibrary-pro/resources/js",
  ];
});

module.exports = {
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    })
  ]
}
