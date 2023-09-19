const config = require('./webpack.config');
const path = require('path');
const mix = require('laravel-mix');
require('laravel-mix-eslint');

function resolve(dir) {
  return path.join(
    __dirname,
    '/resources/js',
    dir
  );
}

Mix.listen('configReady', webpackConfig => {
  // Add "svg" to image loader test
  const imageLoaderConfig = webpackConfig.module.rules.find(
    rule =>
      String(rule.test) ===
      String(/(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/)
  );
  imageLoaderConfig.exclude = resolve('icons');
});

mix.webpackConfig(config);

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/js/app.js', 'public/js')
  .extract([
    'vue',
    'vuex',
    'vue-router',
    'vue-i18n',
    'vue-html2pdf',
    'axios',
    'vue-chartjs',
    'core-js',
    'element-ui',
    'sortablejs',
    'chart.js',
    // 'moment.js',
    'html2pdf.js',
    'dropzone',
    'html2canvas',
    'jspdf',
    'lodash',
  ])
  .options({
    processCssUrls: false,
    postCss: [
      require('autoprefixer'),
    ],
  })
  .sass('resources/js/styles/index.scss', 'public/css/app.css', {
    implementation: require('node-sass'),
  });

if (mix.inProduction()) {
  mix.version();
} else {
  if (process.env.LARAVUE_USE_ESLINT === 'true') {
    mix.eslint();
  }
  // Development settings
  mix
    .sourceMaps()
    .webpackConfig({
      devtool: 'cheap-eval-source-map', // Fastest for development
      devServer: {
        proxy: {
          '*': 'http://localhost:8000', // proxy to host:port which the 'artisan serve' command will use
        },
      },
    });
  mix.options({
    hmrOptions: {
      host: 'localhost',
      port: 8070, // a unuse port
    },
  });
}

// if (mix.inProduction()) {
//   mix.version();
// } else {
//   if (process.env.LARAVUE_USE_ESLINT === 'true') {
//     mix.eslint();
//   }
//   // Development settings
//   mix
//     .sourceMaps()
//     .webpackConfig({
//       devtool: 'cheap-eval-source-map', // Fastest for development
//       devServer: {
//         proxy: {
//           '*': 'http://localhost:8000', // proxy to host:port which the 'artisan serve' command will use
//         },
//       },
//     });
//   mix.options({
//     hmrOptions: {
//       host: 'localhost',
//       port: 8070, // a unuse port
//     },
//   });
// }
