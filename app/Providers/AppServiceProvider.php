<?php

namespace App\Providers;

use App\Util\Mask;
use App\Util\Helper;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->bootValidationRules();
    }

    /**
     * Validações customizadas do blade
     *
     * @return void
     */
    private function bootValidationRules()
    {
        Validator::extend('max_currency', 'App\Rules\MaxCurrency@passes');
        Validator::extend('min_currency', 'App\Rules\MinCurrency@passes');
        Validator::extend('equal', 'App\Rules\Equal@passes');

        Validator::replacer('max_currency', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max', Mask::currencyBRL($parameters[0]), $message);
        });

        Validator::replacer('min_currency', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':min', Mask::currencyBRL($parameters[0]), $message);
        });
    }
}
