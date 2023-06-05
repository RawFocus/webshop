<?php

namespace RawFocus\Webshop\Enums;

use Exception;

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
            case self::SHIPPED->value:
                return 'Shipped';
            case self::ARRIVED->value:
                return 'Arrived';
            default:
                return 'Unknown';
        }
    }
    
    public static function labelFromValue(string $value): string 
    {
        try
        {
            return self::from($value)->label();
        }
        catch (Exception $e)
        {
            return '';
        }
    }
}
