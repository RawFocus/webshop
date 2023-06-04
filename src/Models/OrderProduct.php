<?php

namespace Raw\Webshop\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    protected $fillable = [
        "order_id",
        "product_id",
        "quantity",
        "variants",
        "total_price",
    ];

    //
    // Relationships
    //

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //
    // Accessors
    // 

    public function getVariantsAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getTotalPriceAttribute($value)
    {
        return $value / 100;
    }

    //
    // Mutators
    //

    public function setVariantsAttribute($value)
    {
        $this->attributes["variants"] = json_encode($value);
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes["total_price"] = $value * 100;
    }
}