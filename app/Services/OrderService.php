<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\Distributor;
use App\Models\OrderStatusHistory;
use App\Models\SalesRepresentative;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderService extends BaseService
{
    public function all(Request $request)
    {
        $query = Order::with([
            'distributor',
            'sales_representative.user',
            'customer.area',
            'customer.location',
        ]);

        if ($search = $request->get('search')) {
            $query->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->leftJoin('distributors', 'distributors.id', '=', 'orders.distributor_id');
            $query->where(function($query) use($search) {
                $query->where('customers.mobile', 'like', '%'.$search.'%')
                    ->orWhere('distributors.mobile', 'like', '%'.$search.'%')
                    ->orWhere('orders.tracking_id', 'like', '%'.$search.'%');
            });
        }

        if ($distributorId = $request->get('distributor_id')) {
            $query->where('orders.distributor_id', '=', $distributorId);
        }

        if ($srId = $request->get('sr_id')) {
            $query->where('sales_representative_id', $srId);
        }

        if ($status = $request->get('status')) {
            if (is_array($status)) {
                $query->whereIn('orders.status', $status);
            } else {
                $query->where('orders.status', '=', $status);
            }
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('orders.payment_status', '=', $paymentStatus);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('orders.customer_id', '=', $customerId);
        }

        if ($dateFilter = $request->get('order_date')) {
            $dateRange = explode(' - ', $dateFilter);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            if (!empty($startDate) && !empty($endDate) && ($startDate == $endDate)) {
                $query->whereDate('orders.created_at', $startDate);
            }elseif (!empty($startDate) && !empty($endDate) && ($startDate != $endDate)) {
                $query->whereDate('orders.created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
                $query->whereDate('orders.created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
            }elseif(!empty($startDate)) {
                $query->whereDate('orders.created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
            }elseif(!empty($endDate)){
                $query->whereDate('orders.created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
            }

        }

        if (Gate::allows('isDistributor')) {
            $distributorIds = Distributor::where('user_id',$request->user()->id)->pluck('id');
            $query->whereIn('distributor_id', $distributorIds);
        }elseif (Gate::allows('isSalesRepresentative')) {
            $srId = SalesRepresentative::where('user_id',$request->user()->id)->first()->id;
            $query->where('sales_representative_id', $srId);
        }

        $query->orderBy('orders.id', $request->get('sort', 'desc'));

        return $query->paginate(env('PER_PAGE_PAGINATION'));
    }

    public function create(Array $data)
    {
        $order = app(Order::class);
        $order = $order->create($data);

        return $order;
    }

    public function getById($id, $distributorId = null, $customerId = null)
    {
        $query =  Order::with([
            'line_items.product',
            'line_items.distributor_product',
            'customer',
            'distributor',
            'status_history.creator',
            'sales_representative.user',
        ]);

        if ($distributorId) {
            $query->where('distributor_id', $distributorId);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return $query->find($id);
    }

    public function getByTrackingId($id)
    {
        $order = Order::with(['line_items.product', 'customer', 'distributor'])->where('tracking_id',$id);

        if (Gate::allows('isDistributor')) {
            $distributorId = Distributor::where('user_id',auth()->user()->id)->first()->id;
            $order->where('distributor_id', '=', $distributorId);
        }

        return $order->first();
    }

    private function saveRemarks($order, $data)
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $order->status,
            'current_status' => $data['status'],
            'remarks' => $data['remarks'],
            'created_by' => auth()->user()->id,
        ]);
    }

    public function update($orderOrId, $data, $items, $distributorProductsById)
    {
        if ($orderOrId instanceof Order) {
            $order = $orderOrId;
        } else {
            $order = Order::findOrFail($orderOrId);
        }

        if (!empty($data['remarks'])) {
            $this->saveRemarks($order, $data);
        }

        if (Gate::allows('isDistributor')) {
            $distributorId = Distributor::where('user_id',auth()->user()->id)->first()->id;
            $data['distributor_id'] = $distributorId;
        }

        $order = tap($order)->update($data);

        $oldLineItemsData = OrderLineItem::query()
            ->select([
                'order_id',
                'product_id',
                'item_total',
                'qty',
                'unit_price',
                'discounted_price',
            ])
            ->where('order_id', $order->id)
            ->get()
            ->toArray();

        $newLineItemsData = [];
        foreach ($items as $key => $item) {
            $productId = ($item['product_id'] ?? $key);
            $newLineItemsData[] = [
                'order_id' => $order->id,
                'product_id' => $productId,
                'item_total' => (float) ($item['discounted_price'] * $item['qty']),
                'qty' => (int) $item['qty'],
                'unit_price' => data_get($distributorProductsById, $productId . '.product.mrp', 0.00),
                'discounted_price' => (float) $item['discounted_price'],
            ];
        }

        if($this->orderLineItemDiffCheck($oldLineItemsData, $newLineItemsData)) {
            OrderLineItem::where('order_id', $order->id)->delete();
            OrderLineItem::insert($newLineItemsData);
        }

        return $order->load(['line_items.product', 'customer', 'distributor']);
    }

    /**
     * @param $oldItems
     * @param $newItems
     * @return bool - true if there any changes between old and new line items, otherwise false
     */
    public function orderLineItemDiffCheck($oldItems, $newItems)
    {
        if(count($oldItems) != count($newItems)) {
            return true;
        }

        $hasDiff = false;
        foreach ($oldItems as $oldItem) {
            $lineItemFound = false;
            foreach ($newItems as $key => $newItem) {
                if(empty(array_diff_assoc($oldItem, $newItem))) {
                    $lineItemFound = true;
                    unset($newItems[$key]);
                    break;
                }
            }
            if($lineItemFound == false) {
                $hasDiff = true;
                break;
            }
        }

        return $hasDiff;
    }

    public function getDeliveryCharge($deliveryChargeRules, $subTotal)
    {
        $rule = $deliveryChargeRules->filter(function($value, $key) use ($subTotal){
            $max = data_get($value, 'max_basket_size');
            $min = data_get($value, 'min_basket_size');

            return ($subTotal <= $max  && $subTotal >= $min);
        })->sortByDesc('delivery_charge');

        return data_get($rule->first(), 'delivery_charge', 0.00);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = Order::find($orderId);

        if (blank($order)) {
            return false;
        }

        $order->status = $status;
        $order->save();

        return $order;
    }
}
