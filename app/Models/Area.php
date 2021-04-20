<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class Area extends Model
{
    use HasTranslations;
    use Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lat',
        'long',
        'location_id',
        'ecom_area_id',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public $translatable = ['name'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
