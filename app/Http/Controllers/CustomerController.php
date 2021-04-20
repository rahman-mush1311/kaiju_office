<?php


namespace App\Http\Controllers;


use App\Filters\CustomerFilter;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Area;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;

class CustomerController extends Controller
{
    public function index(Request $request, CustomerFilter $filter)
    {
        
        
        $location = $request->get('location_id') ? Location::find($request->get('location_id')) : [];
        $area = $request->get('area_id') ? Area::find($request->get('area_id')) : [];

        $customer = new Customer;
  
        if($location){
            $customer = $customer->where('ecom_location_id',$location->ecom_location_id);
        }

        if($area){
            $customer = $customer->where('ecom_area_id',$location->ecom_area_id);
        }

        if (Gate::allows('isDistributor')) {
            $distributorAreas = Distributor::with(['area'])->where('user_id',$request->user()->id)->get()->map(function($row){
                return $row->area->first();
            })->pluck('ecom_area_id');
            $customer = $customer->whereIn('ecom_area_id',$distributorAreas);
        }
        elseif (Gate::allows('isSalesRepresentative')) {
            $distributorAreas = Distributor::with(['area'])->join('sales_representatives', 'distributors.id', '=', 'sales_representatives.distributor_id')->where('sales_representatives.user_id',$request->user()->id)->get()->map(function($row){
                return $row->area->first();
            })->pluck('ecom_area_id');
            $customer = $customer->whereIn('ecom_area_id',$distributorAreas);
        }

        $customers = $customer->filter($filter)->paginate();

        $input = $request->all();
        return view('customers.index', compact('customers', 'input', 'location', 'area'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        $customer->fill($request->except('_token', '_method'));
        if ($customer->save()) {
            return redirect()->route('customers.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Customer!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Customer Update Failed!']);
    }

    public function customerListSelect2(CustomerFilter $filter)
    {
        $customers = Customer::filter($filter)->limit(10)->get();
        return $customers->transform(function($item, $key){
            return [
                'id' => $item->id,
                'text' => $item->name,
            ];
        });
    }
}
