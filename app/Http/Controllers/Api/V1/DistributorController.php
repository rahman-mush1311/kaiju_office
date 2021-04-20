<?php

namespace App\Http\Controllers\Api\V1;


use App\Enums\DistributorProductStatus;
use App\Http\Controllers\Controller;
use App\Models\DistributorProduct;
use App\Presenters\DistributorPresenter;
use App\Presenters\DistributorProductPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\DistributorService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class DistributorController extends Controller
{

    public function index( Request $request )
    {
        $areaId = $request->get('area_id');

        if (blank($areaId)) {
            $areaId = data_get(auth('retailer')->user(), 'area.id');
        }

        $distributors = app(DistributorService::class)->getAreaWiseDistributor($areaId);

        return (new PaginatorPresenter($distributors))->presentBy(DistributorPresenter::class);
    }

    public function show($id, Request $request)
    {
        $data = app(DistributorService::class)->getById($id);

        return api($data)->success('Success!');
    }

    public function getDetails(Request $request)
    {
        $distributorId = get_distributor_id();

        $data = app(DistributorService::class)->getById($distributorId);

        if (!blank($data)) {
            $data = (new DistributorPresenter($data->toArray()))->get();
        }

        return api($data)->success('Success!');
    }

    public function products($distributorId)
    {
        $distributorProducts = app(ProductService::class)->distributorProductList($distributorId);
        return (new PaginatorPresenter($distributorProducts))
            ->presentBy(DistributorProductPresenter::class);
    }
}
