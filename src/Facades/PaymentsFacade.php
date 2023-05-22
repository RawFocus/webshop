<?php

namespace Raw\Webshop\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "payments";
    }
}
