<?php

namespace RawFocus\Webshop\Models;

use Uuid;

use RawFocus\Webshop\Enums\OrderStatusEnum;
use RawFocus\Webshop\Enums\PaymentStatusEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "uuid",
        "user_id",
        "order_status",
        "payment_status",
        "payment_method",
        "payment_id",
        "name",
        "email",
        "street",
        "postal_code",
        "city",
        "country",
        "num_products",
        "total_price",
    ];
    protected $casts = [
        "order_status" => OrderStatusEnum::class,
        "payment_status" => PaymentStatusEnum::class,
    ];

    //
    // UUID
    //
    
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }

    //
    // Relationships
    //

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity', 'variants', 'total_price'])
            ->using(OrderProduct::class);
    }

    //
    // Accessors
    //

    public function getTotalPriceAttribute($value)
    {
        return $value / 100;
    }

    //
    // Mutators
    //

    public function setTotalPriceAttribute($value)
    {
        $this->attributes["total_price"] = $value * 100;
    }
}
