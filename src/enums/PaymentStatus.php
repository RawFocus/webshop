<?php
namespace Raw\Webshop\Enums;


enum PaymentStatusEnum:string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PENDING = 'pending';
}