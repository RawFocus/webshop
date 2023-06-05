<?php

namespace RawFocus\Webshop\Enums;

use Exception;

enum PaymentStatusEnum: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PENDING = 'pending';
    case FAILED = 'failed';

    public function label(): string 
    {
        switch ($this->value) {
            case self::PAID->value:
                return 'Paid';
            case self::UNPAID->value:
                return 'Unpaid';
            case self::PENDING->value:
                return 'Pending';
            case self::FAILED->value:
                return 'Failed';
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
