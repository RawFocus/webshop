<?php

namespace Raw\Webshop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Cviebrock\EloquentSluggable\Sluggable;

class Product extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        "title",
        "summary",
        "price",
        "stock",
        "listed",
        "uuid"
    ];

    //
    // Slug configuration
    //

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title.nl'
            ]
        ];
    }

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

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

    //
    // Accessors
    //

    public function getTitleAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getSummaryAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    //
    // Mutators
    //

    public function setTitleAttribute($value)
    {
        $this->attributes["title"] = json_encode($value);
    }

    public function setSummaryAttribute($value)
    {
        $this->attributes["summary"] = json_encode($value);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes["price"] = $value * 100;
    }
}
