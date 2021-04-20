<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Filters\Filterable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
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
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'name' => 'array'
    ];

    public $translatable = ['name'];

    protected $appends = [
        'active_products',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'distributor_products','distributor_id', 'product_id');
    }

    public function getImageAttribute()
    {
        return $this->attributes['image'] ? env('BIOSCOPE_MEDIA_URL', url()) . '/' . ltrim($this->attributes['image'], '/') : '';
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

    public function getActiveProductsAttribute()
    {
        return $this->products()->where('products.status', ProductStatus::ACTIVE)->count();
    }
}
