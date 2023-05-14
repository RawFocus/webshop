<?php

namespace Raw\Webshop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Cviebrock\EloquentSluggable\Sluggable;

class Order extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        "name",
        "email",
        "address_street",
        "address_country",
        "address_postal_code",
        "address_city",
        "total_price",
    ];

    //
    // Relationships
    //

    public function products()
    {
        return $this->hasMany(Product::class);
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
