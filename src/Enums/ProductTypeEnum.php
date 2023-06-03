<?php

namespace Raw\Webshop\Enums;

use Exception;

enum ProductTypeEnum: string
{
    case TSHIRT = 'tshirt';
    case HOODIE = 'hoodie';

    public function label(): string 
    {
        switch ($this->value)
        {
            default:
            case self::TSHIRT->value:
                return 'Tshirt';
            case self::HOODIE->value:
                return 'Hoodie';
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
