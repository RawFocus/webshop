<?php

namespace RawFocus\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class WebshopPaymentsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "payments";
    }
}
