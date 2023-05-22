<?php

namespace Raw\Webshop\Enums;

enum OrderStatusEnum: string
{
    case OPEN = 'open';
    case FULFILLED = 'fulfilled';
    case SHIPPED = 'shipped';
    case ARRIVED = 'arrived';
    case REFUNDED = 'refunded';

    public function label(): string 
    {
        switch ($this->value) {
            case self::OPEN->value:
                return 'Open';
            case self::FULFILLED->value:
                return 'Fulfilled';
            case self::SHIPPED->value:
                return 'Shipped';
            case self::ARRIVED->value:
                return 'Arrived';
            case self::REFUNDED->value:
                return 'Refunded';
            default:
                return 'Unknown';
        }
    }
}
