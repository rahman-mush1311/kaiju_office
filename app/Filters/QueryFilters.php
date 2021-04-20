<?php


namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueryFilters
{
    protected $request;

    /**
     * @var $builder Builder
     */
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $params = $this->request->all();
        $this->builder = $builder;

        foreach($params as $method => $param) {
            $method = Str::camel($method) . 'Filter';

            if (!empty($param) && method_exists($this, $method)) {
                $this->builder = call_user_func_array([$this, $method], [$param]);
            }

        }

        return $this->builder;
    }
}
