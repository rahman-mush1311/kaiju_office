<?php


namespace App\Http\Controllers;


use App\Enums\DeliveryChargeRuleStatus;
use App\Enums\DistributorProductStatus;
use App\Enums\DistributorStatus;
use App\Enums\Role;
use App\Enums\UserStatus;
use App\Exports\DistributorProductExport;
use App\Filters\DistributorFilter;
use App\Http\Requests\DistributorRequest;
use App\Imports\DistributorProductImport;
use App\Models\Area;
use App\Models\Brand;
use App\Models\DeliveryChargeRule;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class DistributorController extends Controller
{
    public function index(Request $request, DistributorFilter $filter)
    {
        $query = new Distributor;
        if (Gate::allows('isDistributor')) {
            $query = $query->where('user_id',$request->user()->id);
        }
        $distributors = $query->filter($filter)->paginate();
        $input = $request->all();
        return view('distributors.index', compact('distributors', 'input'));
    }

    public function create()
    {
        if (!Gate::allows('isAdmin')) {
            abort(403);
        }

        $locations = Location::all();
        $areas = Area::all();
        $deliveryChargeRules = DeliveryChargeRule::where('status', DeliveryChargeRuleStatus::ACTIVE)->get();
        return view('distributors.create', compact('locations', 'areas', 'deliveryChargeRules'));
    }

    public function store(DistributorRequest $request)
    {
        if (!Gate::allows('isAdmin')) {
            abort(403);
        }

        $data = $this->uploadImage($request);
        $data = array_merge($data, $request->except('profile_image','banner_image', 'name_en', 'name_bn'));

        try {
            DB::beginTransaction();

            $request->validate([
                'password' => 'required'
            ]);

            $user = User::where('email',$request->input('email'))->first();

            if($user){
                $user->password = Hash::make($request->input('password'));
                $user->save();
            }
            else {
                $user = new User();
                $user->fill([
                    'name' => $request->input('name.en'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'roles' => [Role::DISTRIBUTOR],
                ]);
                $user->save();
            }

            $data['user_id'] = $user->id;

            $distributor = new Distributor();
            $distributor->fill($data);
            $distributor->save();

            $this->syncAreas($distributor, $request);
            $this->syncDeliveryChargeRules($distributor, $request);

            DB::commit();

            return redirect()->route('distributors.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Distributor!']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Distributor Creation Failed!']);
        }
    }

    public function getAssignProductForm($distributorId)
    {
        $distributorProducts = DistributorProduct::where('status', '<>', DistributorProductStatus::DELETED)
            ->where('distributor_id', $distributorId)
            ->with('product')
            ->get();
        $brands = Brand::all();

        return view('distributors.assign-product', compact("distributorId", "distributorProducts", "brands"));
    }

    public function assignProduct($distributorId, Request $request)
    {
        $distributor = Distributor::findOrFail($distributorId);
        $distributorProduct = DistributorProduct::where('distributor_id', $distributorId)
            ->where('product_id', $request->input('product_id'))
            ->first();

        if (!$distributorProduct) {
            $distributorProduct = new DistributorProduct();
        }


        $data = $request->except('_token');
        $data['distributor_id'] = $distributor->id;
        $data['status'] = $request->input('status');

        $distributorProduct->fill($data);
        $distributorProduct->save();

        return redirect()->back()->with([
            '_status' => 'success',
            '_msg' => 'Successfully assigned product to agent ' . $distributor->name
        ]);
    }

    public function detachProduct($distributorId, $productId)
    {
        $distributor = Distributor::find($distributorId);
        $distributorProduct = DistributorProduct::where('distributor_id', $distributorId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $distributorProduct->status = DistributorProductStatus::DELETED;
        $distributorProduct->save();

        return redirect()->back()->with([
            '_status' => 'success',
            '_msg' => 'Successfully remove product from agent ' . $distributor->name
        ]);
    }

    public function edit($id)
    {
        $distributor = Distributor::with(['area', 'delivery_charge_rules'])->find($id);
        $locations = Location::select('id', 'name')->get();
        $areas = Area::select('id', 'name')->get();
        $deliveryChargeRules = DeliveryChargeRule::where('status', DeliveryChargeRuleStatus::ACTIVE)->get();
        $assignedRules = data_get($distributor, 'delivery_charge_rules', collect([]))->pluck('id')->toArray();
        return view('distributors.edit', compact('distributor', 'locations', 'areas', 'deliveryChargeRules', 'assignedRules'));
    }

    private function syncAreas($distributor, $request)
    {
        $areas = [];
        $selectedAreas = Area::whereIn('id', $request->get('areas'))->get();
        foreach ($selectedAreas as $selectedArea) {
            $areas[$selectedArea->id] = ['location_id' => $selectedArea->location_id];
        }
        $distributor->area()->sync($areas);
    }

    private function syncDeliveryChargeRules($distributor, $request)
    {
        $distributor->delivery_charge_rules()->sync($request->get('delivery_charge_rules'));
    }

    private function uploadImage($request)
    {
        $data = [];

        if ($request->hasFile('profile_image')) {
            $profileImage = md5(Str::random(8) . time()) . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->storeAs('kaiju', $profileImage);
            $data['profile_image'] = 'kaiju/' . $profileImage;
        }

        if ($request->hasFile('banner_image')) {
            $bannerImage = md5(Str::random(8) . time()) . '.' . $request->file('banner_image')->getClientOriginalExtension();
            $request->file('banner_image')->storeAs('kaiju', $bannerImage);
            $data['banner_image'] = 'kaiju/' . $bannerImage;
        }

        return $data;
    }

    public function update($id, DistributorRequest $request)
    {
        $data = $this->uploadImage($request);
        $data = array_merge($data, $request->except('profile_image','banner_image', 'name_en', 'name_bn', 'user_id'));

        try {
            DB::beginTransaction();

            $distributor = Distributor::findOrFail($id);

            if(!blank($request->get('password'))) {
                $user['password'] = Hash::make($request->get('password'));
            }

            $user = User::where('email',$request->input('email'))->first();
            $new_user_status = $data['status'] == DistributorStatus::ACTIVE? UserStatus::ACTIVE: UserStatus::INACTIVE;

            if($user){
                // update existing user password
                if(!blank($request->get('password'))) {
                    $user->password = Hash::make($request->get('password'));
                }
                $user->status = $new_user_status;
                $user->save();
            }
            else {
                // if no existing user matched & password is blank
                if(!blank($request->get('password'))) {
                    throw ValidationException::withMessages(['Password can not be blank for new user!']);
                }

                $user = new User();
                $user->fill([
                    'name' => $request->input('name.en'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'status' => $new_user_status,
                    'roles' => [Role::DISTRIBUTOR],
                ]);
                $user->save();
            }


            $data['user_id'] = $user->id;

            $distributor->update($data);

            $this->syncAreas($distributor, $request);
            $this->syncDeliveryChargeRules($distributor, $request);

            DB::commit();

            return redirect()->route('distributors.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Distributor!']);

        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Distributor Not Updated!']);
        }
    }

    public function distributorListSelect2(DistributorFilter $filter)
    {
        $query = new Distributor;
        if (Gate::allows('isDistributor')) {
            $query = $query->where('user_id',auth()->user()->id);
        }
        $agents = $query->filter($filter)->limit(10)->get();
        return $agents->transform(function($item, $key){
            return [
                'id' => $item->id,
                'text' => str_replace('"', '', $item->name_en),
            ];
        });
    }

    public function exportProduct($distributorId, Request $request)
    {
        $filters = array_merge($request->all(), ['distributor_id' => $distributorId]);

        return (new DistributorProductExport($filters))
            ->download('distributor-products@'. date('Y:m:d h:i:s') .'.xlsx');
    }

    public function getImportProduct($distributorId = null, Request $request)
    {
        $distributor = null;
        if($distributorId) {
            $distributor = Distributor::select(['id', 'name_en'])->find($distributorId);
        }

        return view('distributors.import-product', compact('distributor'));
    }

    public function importProducts(Request $request)
    {
        $request->validate(['products' => 'file|mimes:xlsx']);

        DB::beginTransaction();
        try {
            if(!$request->hasFile('products')) {
                throw ValidationException::withMessages(['Invalid File']);
            }
            DistributorProduct::where('distributor_id', $request->distributor_id)->delete();
            Excel::import(new DistributorProductImport($request->distributor_id), $request->file('products'));
            DB::commit();

            return redirect()->back()->with([
                '_status' => 'success',
                '_msg' => 'Product imported successfully!'
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
    }
}
