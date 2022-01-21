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
        Paginator::useBootstrap();

        $this->bootBlade();
        $this->bootCollectionMacros();
    }

    /**
     * Inicializa as macros customizadas das Collections
     *
     * @return void
     */
    private function bootCollectionMacros()
    {
        Collection::macro('transpose', function () {
            $items = [];
            $arr = $this->all();

            foreach ($arr as $key => $subarr) {
                foreach ($subarr as $subkey => $subvalue) {
                    $items[$subkey][$key] = $subvalue;
                }
            }

            return new static($items);
        });
    }

    /**
     * Inicializa tudo relativo ao blade no service provider
     *
     * @return void
     */
    private function bootBlade()
    {
        $this->bootBladeValidation();
        $this->bootBladeIfs();
        $this->bootBladeComponents();
    }

    /**
     * Estruturas de condições customizadas do blade
     *
     * @return void
     */
    private function bootBladeIfs()
    {
        Blade::if('role', function ($role) {
            return (auth()->check() && auth()->user()->hasRole($role));
        });
    }

    /**
     * Validações customizadas do blade
     *
     * @return void
     */
    private function bootBladeValidation()
    {
        Validator::extend('max_currency', 'App\Rules\MaxCurrency@passes');
        Validator::extend('min_currency', 'App\Rules\MinCurrency@passes');
        Validator::extend('equal', 'App\Rules\Equal@passes');

        Validator::replacer('max_currency', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max', Mask::money($parameters[0]), $message);
        });

        Validator::replacer('min_currency', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':min', Mask::money($parameters[0]), $message);
        });
    }

    private function bootBladeComponents()
    {
        Blade::include('components.forms.input', 'input');
        Blade::include('components.forms.data-list', 'dataList');
        Blade::include('components.forms.input-file', 'inputFile');
        Blade::include('components.forms.select', 'select');
        Blade::include('components.modal', 'modal');
        Blade::include('components.forms.radio', 'radio');
        Blade::include('components.button', 'button');
    }
}
