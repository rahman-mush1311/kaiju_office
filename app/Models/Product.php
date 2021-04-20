<?php

namespace App\Models;

use App\Filters\Filterable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use Sluggable, HasTranslations;
    use Filterable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'mrp',
        'trade_price',
        'short_description',
        'long_description',
        'status',
        'image',
    ];

    protected $casts = [
        'name' => 'array'
    ];

    public $translatable = ['name'];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_products', 'product_id', 'brand_id')->withTimestamps();
    }

    /**
     * @inheritDoc
     */
    public function sluggable(): array
    {
        return  [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getImageAttribute()
    {
        return env('BIOSCOPE_MEDIA_URL', url()) . '/' . ltrim($this->attributes['image'], '/');
    }

    public function getNameEnAttribute()
    {
        return str_replace('"', "", $this->attributes['name_en']);
    }

    public function getNameBnAttribute()
    {
        return str_replace('"', "", $this->attributes['name_bn']);
    }
}
