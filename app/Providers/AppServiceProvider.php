<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('database');
        $this->app->configure('custom');
        $this->app->configure('repositories');
        $this->app->configure('travis');
    }
}
