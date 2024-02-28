<?php

namespace Buzdyk\Revertible;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        $this->app->bind('revertible',function(){
            return new FacadeAccessor();
        });
    }
}