<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Presenters\ProductPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index( Request $request )
    {
        $data = app(ProductService::class)->all($request);

        if ( !blank($data) ) {
            $data = ( new ProductPresenter($data) )->get();
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
            $data = ( new ProductPresenter(app(ProductService::class)->create($request)) )->get();
            DB::commit();

            debug_log("Product created successfully !", $data);

            return api($data)
                ->details('Product Created successfully!')
                ->success('PRODUCT_CREATED', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Product create failed !", $e->getTrace());
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
        $data = app(ProductService::class)->getById($id);

        $data = ( new ProductPresenter($data) )->get();

        return api($data)
            ->details('Product found!')
            ->success('PRODUCT_FOUND');
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
            $data = ( new ProductPresenter(app(ProductService::class)->update($id, $request)) )->get();

            DB::commit();

            debug_log("Product updated successfully !", $data);

            return api($data)->success('Product Updated successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Product update failed !", $e->getTrace());
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
        $data = app(ProductService::class)->delete($id);

        return api($data)->success('Product Deleted Successfully!');
    }

}
