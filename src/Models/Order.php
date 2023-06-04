<?php

namespace Raw\Webshop\Models;

use Uuid;

use Raw\Webshop\Enums\OrderStatusEnum;
use Raw\Webshop\Enums\PaymentStatusEnum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "name",
        "email",
        "street",
        "postal_code",
        "city",
        "country",
        "total_price",
        "order_status",
        "payment_status",
        "payment_method",
        "payment_id",
        "uuid"
    ];
    protected $casts = [
        "payment_status" => PaymentStatusEnum::class,
        "order_status" => OrderStatusEnum::class,
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
        return $this->belongsToMany(Product::class)->withPivot(['quantity', 'variants']);
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
