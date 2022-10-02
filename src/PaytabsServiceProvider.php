<?php

namespace MTGofa\Paytabs;

use Illuminate\Support\ServiceProvider;

class PaytabsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // Config file.
            __DIR__ . '/config/mtgofa-paytabs.php' => config_path('mtgofa-paytabs.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('paytabs', function () {
            return new Paytabs;
        });
    }
}
