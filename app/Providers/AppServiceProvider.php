<?php

namespace App\Providers;

use Algolia\AlgoliaSearch\Support\UserAgent;
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
        UserAgent::addCustomUserAgent('Api Key Dealer', '1.0.0');

        $this->app->configure('database');
        $this->app->configure('custom');
        $this->app->configure('repositories');
        $this->app->configure('travis');
    }
}
