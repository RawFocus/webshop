<?php

namespace Raw\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class ProductsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "products";
    }
}
