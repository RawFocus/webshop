<?php

namespace Raw\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class WebshopOrdersFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "orders";
    }
}
