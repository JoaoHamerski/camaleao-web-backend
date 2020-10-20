const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('resources/sass/app.scss', 'public/css')
	.js('resources/js/app.js', 'public/js')
	.js('resources/js/partials/client-form.js', 'public/js/partials')
	.js('resources/js/partials/order-form.js', 'public/js/partials')
	.js('resources/js/partials/show-order.js', 'public/js/partials')
	.js('resources/js/partials/users.js', 'public/js/partials')
	.js('resources/js/partials/my-account.js', 'public/js/partials')
	.version();
