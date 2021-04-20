<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class Location extends Model
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
        'details',
        'ecom_location_id',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public $translatable = ['name'];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }


}
