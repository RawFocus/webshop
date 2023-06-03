<?php

namespace Raw\Webshop\Models;

use Images;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        "path",
        "product_id"
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($image) {
            Images::delete($image->path);
        });
    }
}
