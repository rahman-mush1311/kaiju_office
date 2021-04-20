<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DeliveryChargeRule;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Presenters\OrderPresenter;
use App\Presenters\PaginatorPresenter;
use App\Services\CartService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Traits\WrapInTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Apis\Ecom\EcomApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    use WrapInTransaction;

    const ORDER_CREATE_FAILED = "ORDER_CREATE_FAILED";
    /**
     * @var CartService
     */
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function store(Request $request)
    {
        $items = $request->input('items');
        $distributorId = $request->input('distributor_id');
        $itemsCollection = collect($items);
        $itemIds = $itemsCollection->pluck('product_id')->toArray();
        $mobile = $request->input('customer_mobile', auth('retailer')->user()->mobile);

        if (empty($mobile)) {
            throw new BaseException('Customer not found!');
        }

        $distributor = Distributor::with(['delivery_charge_rules'])->find($distributorId);
        if (blank($distributor)) {
            throw new BaseException('Distributor not found!');
        }

        $distributorProducts = DistributorProduct::where('distributor_id', $distributorId)
            ->whereIn('product_id', $itemIds)
            ->with('product')
            ->get();

        $distributorProductsById = $distributorProducts->keyBy('product_id')->toArray();

        foreach ($distributorProducts as $product) {
            if ($product->distributor_id != $distributorId) {
                throw new BaseException('Products must be from same distributor');
            }
        }

        $subTotal = floatval(number_format($itemsCollection->pluck('item_total')->sum(), 2, '.', ''));

        if ($subTotal != $request->input('subtotal')) {
            Log::error("subtotal mismatch", [
                'input_subtotal' => $request->input('subtotal'),
                'collection_subtotal' => $subTotal,
            ]);
            throw new BaseException('The subtotal amount missmatch');
        }

        $deliveryChargeRules = data_get($distributor, 'delivery_charge_rules');
        if (!blank($deliveryChargeRules)) {
            $deliveryCharge = app(OrderService::class)->getDeliveryCharge($deliveryChargeRules, $subTotal);
            if ($deliveryCharge != $request->input('delivery_charge')) {
                throw new BaseException('Invalid delivery charge');
            }
        }

        $total = $subTotal + $request->input('delivery_charge', 0);
        if ($total != $request->input('total')) {
            throw new BaseException('The total amount missmatch');
        }

        $orderData = [
            'customer_id' => auth('retailer')->user()->id,
            'distributor_id' => $distributorId,
            'customer_mobile' => $mobile,
            'status' => OrderStatus::CREATED,
            'payment_status' => OrderPaymentStatus::PENDING,
            'address' => $request->input('delivery_address'),
            'delivery_charge' => $request->input('delivery_charge', 0),
            'sub_total' => $subTotal,
            'total' => $total,
            'remarks' => $request->input('remarks'),
            'tracking_id' => $this->generateTrackingId(),
            'misc' => json_encode($request->all()),
        ];

        DB::beginTransaction();
        try {

            $order = new Order();
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
                    'unit_price' => data_get($distributorProductsById, $item['product_id'] . '.product.trade_price'),
                    'discounted_price' => $item['discounted_price'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $orderLineItem = OrderLineItem::insert($itemData);
            $this->cartService->refreshCart();
            DB::commit();

            $notification = new NotificationService();
            $notification->sendNewOrderNotification($order);

            return api($order->toArray())
                ->details('Successfully placed order!')
                ->success('ORDER_PLACED_SUCCESSFUL');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
    }

    private function generateTrackingId()
    {
        return strtoupper(substr(md5(microtime()), 0, 8));
    }

    public function retailerOrders()
    {
        $sort = request()->get('sort', 'desc');
        $orders = Order::where('customer_id', auth('retailer')->user()->id)
            ->with('customer', 'line_items.product');

        if (request()->has('status')) {
            $orders->where('status', request()->get('status', OrderStatus::CREATED));
        }

        $orders = $orders->orderBy('id', $sort)->paginate();

        return (new PaginatorPresenter($orders))->presentBy(OrderPresenter::class);
    }


    public function showRetailerOrder($id)
    {
        $retailer = auth('retailer')->user();
        if (!$retailer) {
            throw new BaseException('Invalid Token !');
        }

        $order = Order::with('customer', 'line_items.product', 'distributor')
            ->where('customer_id', auth('retailer')->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $data = (new OrderPresenter($order->toArray()))->get();

        return api($data)->details('Order fetched successfully')
            ->success('ORDER_FOUND');
    }

    public function confirmOrder(Request $request)
    {
        try {
            $cart = $this->cartService->cartDetails();
            $items = data_get($cart, 'items');
            if (blank($items)) {
                throw new \Exception("There is not item in cart!");
            }

            $data = [
                "customer_mobile" => $request->get('customer_mobile'),
                "delivery_address" => $request->get('delivery_address'),
                "customer_lat" => $request->get('customer_lat'),
                "customer_long" => $request->get('customer_long'),
                "remarks" => $request->get('remarks'),
                "subtotal" => $cart->sub_total,
                "delivery_charge" => $cart->delivery_charge,
                "total" => $cart->total,
                "distributor_id" => $cart->distributor_id,
            ];

            foreach ($items as $key => $item) {
                $data["items"][$key] = [
                    "product_id" => $item->product_id,
                    "discounted_price" => $item->discounted_price,
                    "qty" => $item->qty,
                    "item_total" => $item->item_total,
                ];
            }

            $orderRequest = new Request($data);
            return $this->store($orderRequest);
        } catch (\Exception $e) {

            return api()->details($e->getMessage())
                ->fails(self::ORDER_CREATE_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY);

        }
    }
}
