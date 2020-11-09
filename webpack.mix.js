const mix = require('laravel-mix');

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

mix.setPublicPath('../public_html');
mix.js('resources/js/app.js', '../public_html/js')
    // .extract(['jquery','bootstrap'])
    // .js('resources/js/functions.js', '../public_html/js/moip')
    // .js('resources/js/cep.js', '../public_html/js')
    // .js('resources/js/pagamento.js', '../public_html/js')
    // .copy('node_modules/moment/min/moment.min.js', '../public_html/js/moment')
    .copyDirectory('resources/outros', '../public_html/')
    .copyDirectory('resources/outros', 'public')
    // .js('resources/js/moip-2.7.1.min.js', '../public_html/js/moip')
    .sass('resources/sass/app.scss', '../public_html/css')
    // .styles('resources/sass/produto.css', '../public_html/css/produto.css')
    // .styles('resources/sass/loja.css', '../public_html/css/loja.css');

// mix.js('resources/js/app-clean.js', '../public_html/js');

// mix.copyDirectory('resources/img', '../public_html/img');


// mix.copyDirectory('../public_html/css', 'public/css');
// mix.copyDirectory('../public_html/js', 'public/js');
// mix.copyDirectory('../public_html/fonts', 'public/fonts');
// mix.copyDirectory('../public_html/img', 'public/img');

mix.sourceMaps();
mix.version();
// mix.copyDirectory('../public_html', 'public');
