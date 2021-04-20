<?php

namespace App\Presenters;


use App\Transformers\CategoryTransformer;

class CategoryPresenter extends BasePresenter
{

    public function present(): array
    {
        return [
            'id',
            'name',
            'slug',
            'description'
        ];
    }

    public function transformer()
    {
        return CategoryTransformer::class;
    }
}
