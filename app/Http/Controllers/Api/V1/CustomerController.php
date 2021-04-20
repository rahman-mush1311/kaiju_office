<?php

namespace App\Http\Controllers\Api\V1;

use App\Apis\Ecom\EcomApi;
use App\Filters\CustomerFilter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Presenters\CustomerPresenter;
use App\Presenters\PaginatorPresenter;

class CustomerController extends Controller
{
    public function list(CustomerFilter $filter)
    {
        $customerList = Customer::filter($filter)->paginate(env('PER_PAGE_PAGINATION'));
        return (new PaginatorPresenter($customerList))->presentBy(CustomerPresenter::class);
    }

    public function getAddress($authId)
    {
        $ecomApi = new EcomApi();
        return $ecomApi->getAddress($authId);
    }
}
