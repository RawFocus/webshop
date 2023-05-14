<?php

namespace Raw\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class WebshopFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "webshop";
    }
}