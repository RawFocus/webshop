<?php

namespace Raw\Webshop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_variant_id",
        "stock",
        "name",
    ];

    //
    // Relationships
    //

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(ProductVariantOption::class);
    }

    //
    // Accessors
    //

    public function getNameAttribute($value)
    {
        return (array) json_decode($value);
    }

    //
    // Mutators
    //

    public function setNameAttribute($value)
    {
        $this->attributes["name"] = json_encode($value);
    }
}
