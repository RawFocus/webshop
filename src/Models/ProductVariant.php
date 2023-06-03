<?php

namespace Raw\Webshop\Models;

use Images;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
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
        return $this->hasMany(ProductVariantOption::class, "product_variant_id", "id");
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
