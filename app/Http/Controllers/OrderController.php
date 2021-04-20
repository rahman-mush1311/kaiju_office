<?php


namespace App\Http\Controllers;


use App\Enums\OrderStatus;
use App\Http\Requests\OrderRequest;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Customer;
use App\Models\Order;
use App\Models\SalesRepresentative;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = app(OrderService::class)->all($request);
        $customer = $request->get('customer_id') ? Customer::find($request->get('customer_id')) : [];
        $distributor = $request->get('distributor_id') ? Distributor::find($request->get('distributor_id')) : [];
        $input = $request->all();

        return view('orders.index', compact('orders', 'input', 'customer', 'distributor'));
    }

    public function edit($id)
    {
        $order = app(OrderService::class)->getById($id);
        $statuses = Order::NEXT_STATUSES[$order->status];
        $salesRepresentatives = SalesRepresentative::with(['user'])
            ->where('distributor_id', data_get($order, 'distributor_id'))->get();

        return view('orders.edit', compact('order', 'statuses', 'salesRepresentatives'));
    }

    public function show($trackingId)
    {
        $order = app(OrderService::class)->getByTrackingId($trackingId);

        if(blank($order)) {
            abort(404);
        }

        $statuses = Order::NEXT_STATUSES[$order->status];

        return view('orders.show', compact('order', 'statuses'));
    }

    public function update($id, OrderRequest $request)
    {
        try {
            $orderData = $request->only(app(Order::class)->getFillable());
            $items = $request->input('items');
            $distributorId = $request->input('distributor_id');
            $deliveryCharge = $request->input('delivery_charge', 0);
            $itemsCollection = collect($items);
            $itemIds = array_keys($items);

            $distributor = Distributor::find($distributorId);
            if (!$distributor) {
                return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Distributor not found!']);
            }

            $distributorProducts = DistributorProduct::whereIn('product_id', $itemIds)
                ->where('distributor_id', $distributorId)
                ->with('product')
                ->get();

            $distributorProductsById = $distributorProducts->keyBy('product_id')->toArray();

            foreach ($distributorProducts as $product) {
                if ($product->distributor_id != $distributorId) {
                    return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Products must be from same distributor!']);
                }
            }

            $total = floatval(number_format($itemsCollection->pluck('item_total')->sum(), 2, '.', ''));

            if ($total != $request->input('sub_total') || $total == 0 || ($total + $deliveryCharge) != $request->input('total')) {
                return redirect()->back()->with(['_status' => 'fails', '_msg' => 'The total amount mismatch!']);
            }

            $order = Order::findOrFail($id);
            if($order->status > OrderStatus::CONFIRMED) {
                return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Invalid status!']);
            }

            if ($order->status != $request->get('status') && empty($request->get('remarks'))) {
                return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Order remarks required !']);
            }

            DB::beginTransaction();
            app(OrderService::class)->update($order, $orderData, $items, $distributorProductsById);
            DB::commit();

            return redirect()->route('order.index')->with(['_status' => 'success', '_msg' => 'Successfully Updated Order!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['_status' => 'fails', '_msg' => 'Order Update Failed!']);
        }
    }

    public function getDeliveryChargeRuleByOrder($orderId, Request $request)
    {
        $order = Order::find($orderId);
        $subTotal = $request->get('sub_total') ?? $order->sub_total;
        $deliveryChargeRules = data_get($order, 'distributor.delivery_charge_rules');
        if (!blank($deliveryChargeRules)) {
           return app(OrderService::class)->getDeliveryCharge($deliveryChargeRules, $subTotal);
        }
        return 0.00;
    }
}
