<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AgentProductStatus;
use App\Enums\VisibilityStatus;
use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentProduct;
use App\Presenters\CustomerPresenter;
use App\Presenters\DistributorProductPresenter;
use App\Presenters\AgentProfilePresenter;
use App\Presenters\PaginatorPresenter;
use App\Presenters\ProductPresenter;
use App\Services\AgentProductService;
use App\Services\AgentService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AgentController extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index( Request $request )
    {
        $data = app(AgentService::class)->all($request);

        if ( !blank($data) ) {
            $data = ( new CustomerPresenter($data) )->get();
        }

        return api($data)->success('Success!');
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
        if(blank($request->get('email'))) {
            $request->request->set('email', $request->get('mobile')."@dgflush.com");
        }

        $this->validate($request, [
            'name' => 'required',
            'mobile' => 'required|unique:agents,mobile',
            'email' => 'required|unique:agents,email',
            'auth_id' => 'required|unique:agents,auth_id',
        ]);

        DB::beginTransaction();
        try {
            $data = ( new CustomerPresenter(app(AgentService::class)->create($request)) )->get();
            DB::commit();

            debug_log("Agent created successfully !", $data);

            return api($data)->success('Agent Created successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Agent create failed !", $e->getTrace());
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
        $data = app(AgentService::class)->getById($id);

        $data = ( new CustomerPresenter($data) )->get();

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
            $data = ( new CustomerPresenter(app(AgentService::class)->update($id, $request)) )->get();

            DB::commit();

            debug_log("Agent updated successfully !", $data);

            return api($data)->success('Agent Updated successfully!', Response::HTTP_CREATED);
        } catch ( \Exception $e ) {
            debug_log("Agent update failed !", $e->getTrace());
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
        $data = app(AgentService::class)->delete($id);

        return api($data)->success('Agent Deleted Successfully!');
    }

    public function products($agentId)
    {
        $agentProducts = AgentProduct::with(['product'])
            ->join('products', 'products.id', '=', 'agent_products.product_id')
            ->where('products.status', VisibilityStatus::ACTIVE)
            ->where('agent_id', $agentId)
            ->where('agent_products.status', AgentProductStatus::ACTIVE);

        if (request()->filled('q')) {
            $agentProducts->where(DB::raw('LOWER(name_en)'), 'like', '%' .
                strtolower(request()->get('q')) . '%');
        }

        $agentProducts = $agentProducts->paginate();

        return (new PaginatorPresenter($agentProducts))
            ->presentBy(DistributorProductPresenter::class);
    }

    public function productDetails($agentId, $productId)
    {
        $agentProduct = AgentProduct::with('product')
            ->where('agent_id', $agentId)
            ->where('product_id', $productId)
            ->firstOrFail();

        return (new DistributorProductPresenter($agentProduct))();
    }

    public function findNearest()
    {
        /*$lat = request()->get('lat');
        $long = request()->get('long');

        $query = DB::table('agents')->selectRaw("*, ((ACOS(SIN(? * PI() / 180) * SIN(lat * PI() / 180) + COS(? * PI() / 180) * COS(lat * PI() / 180) * COS((? - `long`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) AS distance", [$lat, $lat, $long])
            ->having('distance', '<=', 3)
            ->orderBy('distance', 'asc');*/

        $areaId = \request()->get('area_id');

        $agents = Agent::where('area_id', $areaId)->where('status', VisibilityStatus::ACTIVE)->get();

        return (new CustomerPresenter($agents))();
    }

    public function addAgentProduct(Request $request)
    {
        $agentId = auth()->user()->id;
        DB::beginTransaction();
        try {
            $exists = AgentProduct::where('agent_id', $agentId)->where('product_id', $request->get('product_id'))->first();
            if($exists) {
                throw new BaseException('Product already exist!');
            }

            $requestData = array_merge($request->only(app(AgentProduct::class)->getFillable()), ['agent_id' => $agentId]);
            $agentProduct = app(AgentProductService::class)->create($requestData);
            DB::commit();
            $data = (new DistributorProductPresenter($agentProduct))();

            return api($data)->success('Product created successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();

            return api()->fails($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAgentProduct($productId)
    {
        $product = app(AgentProductService::class)->getById($productId, auth()->user()->id);
        $data = (new DistributorProductPresenter($product))();

        return api($data)->success('product fetched successfully.');
    }

    public function agentProducts(Request $request)
    {
        $products = app(AgentProductService::class)->all($request, auth()->user()->id);

        return (new PaginatorPresenter($products))->presentBy(DistributorProductPresenter::class);
    }

    public function updateAgentProduct($productId, Request $request)
    {
        $agentId = auth()->user()->id;
        DB::beginTransaction();
        try {
            $product = app(AgentProductService::class)->update($productId, $request, $agentId);
            DB::commit();
            $data = (new DistributorProductPresenter($product))();

            return api($data)->success('Product updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            return api()->fails($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateAgentProductBulk(Request $request)
    {
        $agentId = auth()->user()->id;
        DB::beginTransaction();
        try {
            $product = app(AgentProductService::class)->bulkUpdate(collect($request->all()), $agentId);
            DB::commit();
            $data = (new DistributorProductPresenter($product))();

            return api($data)->success('Products updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            return api()->fails($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Agent PROFILE api
     * @return mixed
     */
    public function profile()
    {
        $profile = app(AgentService::class)->profile(auth()->user()->id);
        $data = (new AgentProfilePresenter($profile))();

        return api($data)->success('Profile fetched successfully');
    }

    public function searchProducts(Request $request)
    {
        $excludeIds = AgentProduct::where('agent_id', auth()->user()->id)->get()->pluck('product_id');
        $products = app(ProductService::class)->all($request, $excludeIds);

        return (new PaginatorPresenter($products))->presentBy(ProductPresenter::class);
    }
}
