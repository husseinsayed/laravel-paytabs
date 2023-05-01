<?php
namespace Husseinsayed\Paytabs;

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
            __DIR__ . '/config/paytabs.php' => config_path('paytabs.php'),
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
