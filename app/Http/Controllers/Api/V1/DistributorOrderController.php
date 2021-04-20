<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\SalesRepresentative;
use App\Presenters\OrderPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\NotificationService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributorOrderController extends Controller
{
    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function newOrder(Request $request, $id = null)
    {
        $items = $request->input('items');
        $customerId = $request->input('customer_id');
        $distributorId = get_distributor_id();
        $itemsCollection = collect($items);
        $itemIds = $itemsCollection->pluck('product_id')->toArray();
        $srId = $request->input('sales_representative_id') ?? get_sr_id();

        foreach ($items as $item) {
            if (($item['discounted_price'] * $item['qty']) != $item['item_total']) {
                return api($item)->details("Item total mismatch")->fails('ITEM_TOTAL_MISMATCH');
            }
        }

        $customer = Customer::find($customerId);
        if (blank($customer)) {
            return api()->details("Customer not found")->fails('CUSTOMER_NOT_FOUND');
        }

        $distributorProducts = DistributorProduct::where('distributor_id', $distributorId)
            ->whereIn('product_id', $itemIds)
            ->with('product')
            ->get();

        $distributorProductsById = $distributorProducts->keyBy('product_id')->toArray();

        foreach ($distributorProducts as $product) {
            if ($product->distributor_id != $distributorId) {
                return api()->details("Products must be from same distributor")->fails('NOT_SAME_DISTRIBUTOR_PRODUCTS');
            }
        }

        $subTotal = $itemsCollection->pluck('item_total')->sum();

        if ($subTotal != $request->input('subtotal')) {
            return api()->details("The subtotal amount mismatch")->fails('SUBTOTAL_MISMATCH');
        }

        $deliveryChargeRules = $this->getDeliveryChargeRules();
        if (!blank($deliveryChargeRules)) {
            $deliveryCharge = app(OrderService::class)->getDeliveryCharge($deliveryChargeRules, $subTotal);
            if ($deliveryCharge != $request->input('delivery_charge')) {
                return api()->details("Invalid delivery charge")->fails('INVALID_DELIVERY_CHARGE');
            }
        }

        $total = $subTotal + $request->input('delivery_charge', 0);
        if ($total != $request->input('total')) {
            return api()->details("The total amount mismatch")->fails('TOTAL_MISMATCH');
        }

        if (!blank($srId)) {
            $sr = SalesRepresentative::find($srId);
            if (blank($sr) || $sr->distributor_id != $distributorId) {
                return api()->details("Selected sr invalid")->fails('INVALID_SR');
            }
        }

        $orderData = [
            'customer_id' => $customerId,
            'distributor_id' => $distributorId,
            'customer_mobile' => $customer->mobile,
            'status' => OrderStatus::CREATED,
            'payment_status' => OrderPaymentStatus::PENDING,
            'address' => $request->input('delivery_address'),
            'delivery_charge' => $request->input('delivery_charge', 0),
            'sub_total' => $subTotal,
            'total' => $total,
            'remarks' => $request->input('remarks'),
            'tracking_id' => $this->generateTrackingId(),
            'misc' => json_encode($request->all()),
            'sales_representative_id' => $srId
        ];

        DB::beginTransaction();
        try {

            if (!is_null($id)) {
              $order = Order::findOrFail($id);

              if ($order->distributor_id != $distributorId) {
                  return api()->details("Invalid Order")->fails('INVALID_ORDER');
              }

              OrderLineItem::where('order_id', $id)->delete();
            } else {
                $order = new Order();
            }

            $order->fill($orderData);
            $order->save();
            $order->fresh();

            $itemData = [];
            foreach ($items as $item) {
                $itemData[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'item_total' => $item['discounted_price'] * $item['qty'],
                    'qty' => $item['qty'],
                    'unit_price' => data_get($distributorProductsById, $item['product_id'].'.product.trade_price'),
                    'discounted_price' => $item['discounted_price'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $orderLineItem = OrderLineItem::insert($itemData);
            DB::commit();

            if (is_null($id)) {
                $notification = new NotificationService();
                $notification->sendNewOrderNotification($order);
            }

            return api($order->toArray())
                ->details('Successfully placed order!')
                ->success('ORDER_PLACED_SUCCESSFUL');

        } catch (\Exception $e) {
            DB::rollBack();
            return api()->details("Failed to place order")->fails('ORDER_PLACEMENT_FAILED');
        }
    }

    private function getDeliveryChargeRules()
    {
        if (!auth('distributor')->guest()) {
            return data_get(auth('distributor')->user(), 'delivery_charge_rules');
        } else {
            return data_get(auth('sr')->user(), 'distributor.delivery_charge_rules');
        }
    }

    private function generateTrackingId()
    {
        return strtoupper(substr(md5(microtime()), 0, 8));
    }



    public function orders(Request $request)
    {
        $filter['distributor_id'] = get_distributor_id();
        $filter['sr_id'] = get_sr_id();
        $request = new Request(array_merge($request->toArray(), $filter));
        $orders = $this->orderService->all($request);
        return (new PaginatorPresenter($orders))->presentBy(OrderPresenter::class);
    }

    public function orderDetails($id, Request $request)
    {
        $distributorId = get_distributor_id();
        if (!$distributorId) {
            throw new BaseException('Invalid Token !');
        }

        $order = $this->orderService->getById($id, $distributorId);

        if (blank($order)) {
            return api()->details("Order not found!")->fails('ORDER_NOT_FOUND');
        }

        $data = ( new OrderPresenter($order->toArray()) )->get();

        return api($data)->details('Order fetched successfully')
            ->success('ORDER_DETAILS');
    }

    public function updateOrder($id, Request $request)
    {
        // TODO:: Order Update functionality goes here
    }

    public function confirmOrder($id)
    {
        $order = $this->orderService->updateOrderStatus($id, OrderStatus::CONFIRMED);

        if ($order) {
           return api()->details("Order confirmed Successfully!")
                ->success('ORDER_CONFIRMED');
        }

        return api()->details("Failed to confirm order!")
            ->fails('ORDER_CONFIRMATION_FAILED');
    }

    public function cancelOrder($id)
    {
        $order = $this->orderService->updateOrderStatus($id, OrderStatus::CANCELLED);

        if ($order) {
            return api()->details("Order cancelled Successfully!")
                ->success('ORDER_CANCELLED');
        }

        return api()->details("Failed to cancel order!")
            ->fails('ORDER_CANCELLATION_FAILED');
    }

    public function markAsDelivered($id)
    {
        $order = $this->orderService->updateOrderStatus($id, OrderStatus::DELIVERED);

        if ($order) {
            return api()->details("Order marked as delivered!")
                ->success('ORDER_DELIVERED');
        }

        return api()->details("Failed to mark as delivered!")
            ->fails('FAILED_TO_MARK_DELIVERED');
    }

    public function assignSr($id, Request $request)
    {
        $order = Order::findOrFail($id);

        if (data_get($order, 'distributor_id') != get_distributor_id()) {
            return api()->details("Unauthorised action")
                ->fails('UNAUTHORISED_ACTION');
        }

        $srId = $request->get('sales_representative_id');
        $sr = SalesRepresentative::where('distributor_id', get_distributor_id())
        ->where('id', $srId)->get();

        if (blank($srId) || blank($sr)) {
            return api()->details("Invalid sales representative")
                ->fails('INVALID_SALES_REPRESENTATIVE');
        }

        $order->sales_representative_id = $srId;
        $order->save();

        return api()->details("Sales Representative Assigned Successfully!")
            ->success('SR_ASSIGNED');
    }
}
