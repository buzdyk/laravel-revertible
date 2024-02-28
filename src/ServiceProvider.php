<?php

namespace Buzdyk\Revertible;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeRevertibleAction::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->bind('revertible',function(){
            return new FacadeAccessor();
        });
    }
}