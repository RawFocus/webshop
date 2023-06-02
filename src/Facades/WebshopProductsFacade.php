<?php

namespace Raw\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class WebshopProductsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "products";
    }
}
