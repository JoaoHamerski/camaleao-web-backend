<?php

namespace App\Providers;

use App\Util\Mask;
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

    }

    private function bootBlade()
    {
        $this->bootBladeValidation();
        $this->bootBladeCustomIfs();
    }

    private function bootBladeCustomIfs()
    {
        Blade::if('role', function($role) {
            return (auth()->check() && auth()->user()->hasRole($role));
        });
    }

    private function bootBladeValidation()
    {
        Validator::extend('max_double', 'App\Rules\MaxDouble@passes');
        Validator::replacer('max_double', function($message, $attribute, $rule, $parameters) {
            return str_replace(':max', Mask::money($parameters[0]), $message);
        });
    }
}
