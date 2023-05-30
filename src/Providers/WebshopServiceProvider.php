<?php

namespace Raw\Webshop\Providers;

use Illuminate\Support\ServiceProvider;
use Raw\Webshop\Listeners\OrderEventSubscriber;
use Raw\Webshop\Services\PaymentService;
use Raw\Webshop\Services\WebshopService;

class WebshopServiceProvider extends ServiceProvider
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
            __DIR__.'/../config/webshop.php' => config_path('webshop.php'),
        ], 'webshop-config');

         // Path to your package's resources directory
         $source = __DIR__ . '/../resources';

        // Register view files to be loaded
        $this->loadViewsFrom($source, 'webshop');

        // Publish view files for users to override
        $this->publishes([
            $source . '/emails' => resource_path('views/emails/vendor/webshop/emails'),
        ], 'webshop-views');

    }

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        OrderEventSubscriber::class,
    ];
}
