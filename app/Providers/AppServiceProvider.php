<?php

namespace App\Providers;

use BeyondCode\FathomAnalytics\FathomAnalytics;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FathomAnalytics::class, function () {
            return new FathomAnalytics(config('fathom.email'), config('fathom.password'));
        });
    }
}
