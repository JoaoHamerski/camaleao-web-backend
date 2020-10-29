<?php

namespace App\Providers;

use App\Util\Mask;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

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
            $items = array_map(function (...$items) {
                return $items;
            }, ...$this->values());

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
    }

    /**
     * Estruturas de condições customizadas do blade
     * 
     * @return void
     */
    private function bootBladeIfs()
    {
        Blade::if('role', function($role) {
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
        Validator::extend('max_double', 'App\Rules\MaxDouble@passes');
        Validator::replacer('max_double', function($message, $attribute, $rule, $parameters) {
            return str_replace(':max', Mask::money($parameters[0]), $message);
        });
    }
}
