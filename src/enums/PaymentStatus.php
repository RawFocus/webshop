<?php
namespace App\Enums;


enum PaymentStatusEnum:string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PENDING = 'pending';
}