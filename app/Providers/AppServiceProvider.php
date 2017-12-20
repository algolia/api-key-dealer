<?php

namespace App\Providers;

use AlgoliaSearch\Client;
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
        $this->app->singleton(Client::class, function ($app) {
            return new Client(env('ALGOLIA_APP_ID'), env('ALGOLIA_ADMIN_KEY'));
        });
    }
}
