<?php

namespace Raw\Webshop\Enums;

enum OrderStatusEnum: string
{
    case OPEN = 'open';
    case FULFILLED = 'fulfilled';
    case SHIPPED = 'shipped';
    case ARRIVED = 'arrived';
    case REFUNDED = 'refunded';
}
