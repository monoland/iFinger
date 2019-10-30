const mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue'],
        alias: {
            '@apps': __dirname + '/resources/apps'
        }
    }
});

mix.js('resources/apps/index.js', 'public/scripts/monoland.js')
mix.stylus('resources/design/main.styl', 'public/styles/monoland.css');
mix.stylus('resources/design/print.styl', 'public/styles/print.css');

mix.extract(['vue', 'vuetify']);