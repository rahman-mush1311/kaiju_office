<?php

namespace App\Http\Controllers\Api\V1;


use App\Enums\DistributorProductStatus;
use App\Enums\SRStatus;
use App\Filters\SrFilter;
use App\Http\Controllers\Controller;
use App\Models\DistributorProduct;
use App\Models\SalesRepresentative;
use App\Presenters\DistributorPresenter;
use App\Presenters\DistributorProductPresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\SalesRepresentativePresenter;
use App\Services\DistributorService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class SalesRepresentativeController extends Controller
{

    public function list(SrFilter $filter)
    {
        $distributorId = get_distributor_id();
        $srList = SalesRepresentative::with(['user'])
            ->filter($filter)
            ->where('sales_representatives.distributor_id', $distributorId)
            ->where('sales_representatives.status', SRStatus::ACTIVE)
            ->paginate(env('PER_PAGE_PAGINATION'));

        return (new PaginatorPresenter($srList))->presentBy(SalesRepresentativePresenter::class);
    }
}
