<?php

namespace RawFocus\Webshop\Providers;

use RawFocus\Webshop\Services\OrderService;
use RawFocus\Webshop\Services\PaymentService;
use RawFocus\Webshop\Services\ProductService;

use RawFocus\Webshop\Listeners\OrderEventSubscriber;

use Illuminate\Support\ServiceProvider;

class WebshopServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("orders", function() {
            return new OrderService;
        });

        $this->app->singleton("products", function() {
            return new ProductService;
        });
        
        $this->app->singleton("payments", function() {
            return new PaymentService;
        });
    }

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
        ], 'webshop-lang'); 

        // Setup publishing of the configuration file
        $this->publishes([
            __DIR__.'/../config/webshop.php' => config_path('webshop.php'),
        ], 'webshop-config');
    }
}
