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
	.sass('resources/sass/uniform-simulator/app.scss', 'public/css/uniform-simulator')
	.sass('resources/sass/_date-picker.scss', 'public/css')
	.js('resources/js/app.js', 'public/js')
	.js('resources/js/_date-picker.js', 'public/js')
	.js('resources/js/partials/client-form.js', 'public/js/partials')
	.js('resources/js/partials/orders/show.js', 'public/js/partials/orders')
	.js('resources/js/partials/orders/form.js', 'public/js/partials/orders')
	.js('resources/js/partials/orders/index.js', 'public/js/partials/orders')
	.js('resources/js/partials/users.js', 'public/js/partials')
	.js('resources/js/partials/my-account.js', 'public/js/partials')
	.js('resources/js/partials/expenses/index.js', 'public/js/partials/expenses')
	.js('resources/js/partials/expenses/create.js', 'public/js/partials/expenses')
	.js('resources/js/partials/payments/index.js', 'public/js/partials/payments')
	.js('resources/js/partials/cash-flow/index.js', 'public/js/partials/cash-flow')
	.js('resources/js/_service-worker.js', 'public/')
	.js('resources/js/uniform-simulator/app.js', 'public/js/uniform-simulator')
	.version();

mix.copyDirectory('resources/fonts', 'public/fonts');
