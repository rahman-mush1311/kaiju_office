<?php


namespace App\Http\Controllers;


use App\Filters\DeliveryChargeRuleFilter;
use App\Http\Requests\BrandRequest;
use App\Http\Requests\DeliveryChargeRuleRequest;
use App\Models\Brand;
use App\Models\DeliveryChargeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeliveryChargeRuleController extends Controller
{
    public function index(Request $request, DeliveryChargeRuleFilter $filter)
    {
        $rules = DeliveryChargeRule::filter($filter)->paginate();
        $input = $request->all();
        return view('delivery-charge-rule.index', compact('rules', 'input'));
    }


    public function create()
    {
        return view('delivery-charge-rule.create');
    }

    public function store(DeliveryChargeRuleRequest $request)
    {
        $data = $request->all();

        $rule = new DeliveryChargeRule();
        $rule->fill($data);
        if ($rule->save()) {
            return redirect()->route('delivery.charge.rules.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Delivery Charge!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Delivery Charge Creation Failed!']);
    }

    public function edit($id)
    {
        $rule = DeliveryChargeRule::findOrFail($id);
        return view('delivery-charge-rule.edit', compact( 'rule'));
    }

    public function update($id, DeliveryChargeRuleRequest $request)
    {
        $data = $request->all();
        $rule = DeliveryChargeRule::findOrFail($id);
        $rule->fill($data);
        if ($rule->save()) {
            return redirect()->route('delivery.charge.rules.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Delivery Charge Rule!']);
        }

        return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Delivery Charge Rule Update Failed!']);
    }
}
