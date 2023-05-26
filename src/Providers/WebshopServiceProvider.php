<?php

namespace Raw\Webshop\Providers;

use Illuminate\Support\ServiceProvider;
use Raw\Webshop\Services\PaymentService;
use Raw\Webshop\Services\WebshopService;
use Illuminate\Contracts\Support\DeferrableProvider;

class PackageServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the webshop service as a singleton
        $this->app->singleton("webshop", function() {
            return new WebshopService;
        });

        $this->app->singleton("payments", function() {
            return new PaymentService;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Setup loading of the API routes
        $this->loadRoutesFrom(__DIR__."/../routes/api.php");

        // Setup migration loading
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Setup loading of the translation files
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'webshop');

        // Setup publishing of the language files
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/webshop'),
        ]);
        
        // Setup publishing of the configuration file
        $this->publishes([
            __DIR__."/../config/webshop.php" => config_path("webshop.php"),
        ]);
    }
}