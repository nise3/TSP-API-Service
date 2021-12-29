<?php

namespace App\Providers;

use App\Helpers\Classes\AuthTokenUtilityHandler;
use App\Helpers\Classes\ServiceToServiceCallHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ResponseFactory::class, function () {
            return new \Laravel\Lumen\Http\ResponseFactory();
        });


        $this->app->singleton('auth_token_utility', AuthTokenUtilityHandler::class);
        $this->app->singleton('service_to_service_call', ServiceToServiceCallHandler::class);

    }

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
