<?php

namespace Raw\Webshop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "email",
        "address_street",
        "address_country",
        "address_postal_code",
        "address_city",
        "total_price",
        "payment_status",
        "payment_method",
        "payment_id"
    ];
    protected $casts = [
        'payment_status' => ServerStatus::class,
    ];

    //
    // Relationships
    //

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
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
