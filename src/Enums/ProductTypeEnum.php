<?php

namespace Raw\Webshop\Enums;

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
}
