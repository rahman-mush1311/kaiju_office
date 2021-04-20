<?php

namespace App\Presenters;

class SalesRepresentativePresenter extends BasePresenter
{
    public function present(): array
    {
        return [
            'id',
            'distributor_id',
            'name' => 'user.name',
            'email' => 'user.email',
            'mobile',
            'role',
        ];
    }
}
