let mix = require('laravel-mix');
let webpack = require('webpack')

mix.js('resources/js/app.js', 'public/js').vue({ version: 2 });
mix.sass('resources/sass/app.scss', 'public/css');
mix.sass('resources/sass/sin-theme.scss', 'public/css');

// FIXME: delete the function in the archivio.js file and remove the file
mix.copy('resources/js/archivio.js', 'public/js');

mix.override((webpackConfig) => {
  webpackConfig.resolve.modules = [
    "node_modules"
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
