<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AgentProductStatus;
use App\Enums\VisibilityStatus;
use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Presenters\DistributorProductPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\AgentProductService;
use App\Services\CategoryService;
use App\Presenters\CategoryPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function index( Request $request )
    {
        $data = app(CategoryService::class)->all($request, VisibilityStatus::ACTIVE, true);

        if ( !blank($data) ) {
            $data = (new PaginatorPresenter($data))->presentBy(CategoryPresenter::class);
        }

        return $data;
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store( Request $request )
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = ( new CategoryPresenter(app(CategoryService::class)->create($request)) )->get();
            DB::commit();

            debug_log("Category created successfully !", $data);

            return api($data)->success('Category Created successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Category create failed !", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     *
     * @param $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show( $id, Request $request )
    {
        $data = app(CategoryService::class)->getById($id);

        $data = ( new CategoryPresenter($data) )->get();

        return api($data)->success('Success!');
    }

    /**
     *
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update( $id, Request $request )
    {
        DB::beginTransaction();
        try {
            $data = ( new CategoryPresenter(app(CategoryService::class)->update($id, $request)) )->get();

            DB::commit();

            debug_log("Category updated successfully !", $data);

            return api($data)->success('Category Updated successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Category update failed !", $e->getTrace());
            DB::rollback();

            return api()->fails($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy( $id )
    {
        $data = app(CategoryService::class)->delete($id);

        return api($data)->success('Category Deleted Successfully!');
    }

    public function categoryProducts($id, Request $request)
    {
        $productIds = CategoryProduct::where('category_id', $id)->get()->pluck('product_id');
        $products = app(AgentProductService::class)->all($request, auth()->user()->id, $productIds);

        return (new PaginatorPresenter($products))->presentBy(DistributorProductPresenter::class);
    }

    public function customerCategoryProducts($agentId, $id, Request $request)
    {
        $productIds = CategoryProduct::where('category_id', $id)->get()->pluck('product_id');
        $products = app(AgentProductService::class)->all($request, $agentId, $productIds, AgentProductStatus::ACTIVE);

        return (new PaginatorPresenter($products))->presentBy(DistributorProductPresenter::class);
    }
}
