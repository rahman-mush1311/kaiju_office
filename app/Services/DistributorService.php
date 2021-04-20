<?php


namespace App\Services;


use App\Models\Distributor;

class DistributorService extends BaseService
{
    public function getAreaWiseDistributor( $areaId = null)
    {
        $distributorQuery = Distributor::has('products')->with(['products.brands', 'delivery_charge_rules']);

        if(filled($areaId)) {
            $distributorQuery->whereHas('area', function ($query) use ($areaId) {
                $query->where('area_id', $areaId);
            });
        }

        return $distributorQuery->paginate(env('PER_PAGE_PAGINATION'));
    }

    public function getById($id)
    {
        return Distributor::with([
            'products.brands',
            'area',
            'delivery_charge_rules'
        ])->find($id);
    }
}
