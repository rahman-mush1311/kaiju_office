<?php


namespace App\Presenters;


use Nahid\Presento\Presenter;

use App\Presenters\BasePresenter;

class PaginatorPresenter extends BasePresenter
{
    protected $dataPresenter = null;

    public function convert($data)
    {
        return $this->init($data);
    }

    public function presentBy(string $dataPresenter)
    {
        if (class_exists($dataPresenter)) {
            $this->dataPresenter = $dataPresenter;
            $present = $this->present();
            $present['data'] =  [$this->dataPresenter => ['data']];
            $this->setPresent($present);
        }

        $output = $this->handle();
        if ($output['data'] === null) {
            $output['data'] = [];
        }
        return $output;
    }

    public function present(): array
    {
        return [
            'data',
            'to',
            'total',
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page'
        ];
    }
}
