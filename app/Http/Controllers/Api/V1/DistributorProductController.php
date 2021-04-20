<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DistributorProductStatus;
use App\Enums\ProductStatus;
use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Models\DistributorProduct;
use App\Models\Product;
use App\Presenters\DistributorProductPresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\ProductPresenter;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DistributorProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(ProductService $productService)
    {

        $this->productService = $productService;
    }

    public function list()
    {
        $distributorId = get_distributor_id();
        $distributorProducts = $this->productService->distributorProductList($distributorId, true);
        return (new PaginatorPresenter($distributorProducts))
            ->presentBy(DistributorProductPresenter::class);
    }

    public function allProducts(Request $request, ProductFilter $filter)
    {
        $distributorId = get_distributor_id();
        $productIds = DistributorProduct::where('distributor_id', $distributorId)
            ->where('status', '<>', DistributorProductStatus::DELETED)
            ->pluck('product_id');

        $products = Product::filter($filter)
            ->where('status', ProductStatus::ACTIVE)
            ->whereNotIn('id', $productIds)
            ->paginate(env('PER_PAGE_PAGINATION'));

        return (new PaginatorPresenter($products))->presentBy(ProductPresenter::class);
    }

    public function addOrUpdateDistributorProduct(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required',
                'status' => 'required|integer|in:' . implode(",", [
                        DistributorProductStatus::AVAILABLE,
                        DistributorProductStatus::OUT_OF_STOCK,
                    ]),
                'distributor_price' => 'required|numeric',
                'min_order_qty' => 'required|numeric',
            ]);

            $distributorId = get_distributor_id();
            $this->productService->assignDistributorProduct($distributorId, $request->all());
            return api()->details("Successfully updated distributor product")
                ->success('PRODUCT_UPDATED');
        } catch (ValidationException $e) {
            return api($e->errors())->details($e->getMessage())
                ->fails('PRODUCT_UPDATE_FAILED', Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return api()->details("Failed to update distributor product!")->fails('PRODUCT_UPDATE_FAILED');
        }
    }

    public function updateStatus($productId, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|integer|in:' . implode(",", [
                        DistributorProductStatus::AVAILABLE,
                        DistributorProductStatus::OUT_OF_STOCK,
                    ]),
            ], [
                'status.in' => 'Invalid product status',
            ]);

            $distributorId = get_distributor_id();
            $status = $request->get('status');
            $this->productService->updateDistributorProductStatus($distributorId, $productId, $status);
            return api()->details("Successfully updated distributor product status")
                ->success('PRODUCT_STATUS_UPDATED');
        } catch(ValidationException $e) {
            return api($e->errors())->details($e->getMessage())
                ->fails('PRODUCT_STATUS_UPDATE_FAILED', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return api()->details("Failed to update distributor product status")
                ->fails('PRODUCT_STATUS_UPDATE_FAILED', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function delete($productId)
    {
        try {
            $distributorId = get_distributor_id();
            $this->productService->updateDistributorProductStatus($distributorId, $productId, DistributorProductStatus::DELETED);
            return api()->details("Product deleted successfully")->success('PRODUCT_DELETED');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return api()->details("Failed to update delete product")->fails('PRODUCT_DELETE_FAILED');
        }
    }
}
