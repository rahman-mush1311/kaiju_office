<?php

namespace App\Presenters;

use Nahid\Presento\Presenter;

abstract class BasePresenter extends Presenter
{
    public function init( $data )
    {
        return to_array($data);
    }

    public function map($data)
    {
        return to_array($data);
    }
}
