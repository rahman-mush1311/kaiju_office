<?php

namespace App\Services;

use App\Enums\DistributorProductStatus;
use App\Enums\ProductStatus;
use App\Filters\ProductFilter;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductService extends BaseService
{
    public function searchDistributorProduct(Request $request)
    {
        $query = DistributorProduct::query()
            ->select([
                'product_id',
                \DB::raw("REPLACE(name_en, '\"', '') as name_en"),
                \DB::raw("REPLACE(name_bn, '\"', '') as name_bn"),
                'short_description',
                'image',
                \DB::raw("distributor_price as price"),
                'mrp',
                'min_order_qty',
            ])
            ->join('products', 'distributor_products.product_id', '=', 'products.id')
            ->where('products.status', ProductStatus::ACTIVE)
            ->where('distributor_products.distributor_id', $request->input('distributor_id'));

        if($search = $request->get('search')) {
            $query->where(function($query) use($search) {
                $query->where(\DB::raw('LOWER(products.name_en)'), 'like', '%'. strtolower($search) .'%')
                    ->orWhere(\DB::raw('LOWER(products.name_bn)'), 'like', '%'. strtolower($search) .'%');
            });
        }

        if($excludeIds = $request->get('exclude_ids')) {
            $query->whereNotIn('product_id', $excludeIds);
        }

        return $query->limit(env('PER_PAGE_PAGINATION'))->get();
    }

    public function distributorProductList($distributorId, $withStockOut = false)
    {
        $distributorProducts = DistributorProduct::with(['product.brands'])
            ->where('distributor_id', $distributorId);

        if (request()->filled('status')) {
            $distributorProducts->where('distributor_products.status', request()->get('status'));
        } elseif ($withStockOut) {
            $distributorProducts->where('distributor_products.status', '<>', DistributorProductStatus::DELETED);
        } else {
            $distributorProducts->where('distributor_products.status', DistributorProductStatus::AVAILABLE);
        }

        if (request()->filled('brand')) {
            $distributorProducts->whereHas('product.brands', function ($brands) {
                $brands->where('id', request()->get('brand'));
            });
        }

        if (request()->filled('q')) {
            $distributorProducts->whereHas('product', function ($query) {
                return $query->whereRaw("LOWER(name_en) LIKE '%". strtolower(request()->get('q')) ."%'")
                    ->orWhereRaw("name_bn LIKE '%". strtolower(request()->get('q')) ."%'");
            });
        }

        return $distributorProducts->paginate(env('PER_PAGE_PAGINATION'));
    }

    public function updateDistributorProduct($distributorId, $productId, $data)
    {
        $product = DistributorProduct::where('distributor_id', $distributorId)
            ->where('product_id', $productId)
            ->first();

        if (blank($product)) {
            throw new \Exception("Product not found");
        }

        $product->fill($data);
        $product->save();

        return $product;
    }

    public function assignDistributorProduct($distributorId, $data)
    {
        $distributorProduct = DistributorProduct::where('distributor_id', $distributorId)
            ->where('product_id', Arr::get($data, 'product_id'))
            ->first();

        if (!$distributorProduct) {
            $distributorProduct = new DistributorProduct();
            $data['distributor_id'] = $distributorId;
        }

        $distributorProduct->fill($data);
        $distributorProduct->save();
    }

    public function updateDistributorProductStatus($distributorId, $productId, $status)
    {
        $distributorProduct = DistributorProduct::where('distributor_id', $distributorId)
            ->where('product_id', $productId)->first();

        if (blank($distributorProduct)) {
            throw new \Exception("Product not found");
        }

        $distributorProduct->update(['status' => $status]);
    }

}
