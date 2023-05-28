<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Raw\Webshop\Listeners\OrderEventSubscriber;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

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
