<?php


namespace App\Services;


use App\Models\Cart;
use App\Models\CartLineItem;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartService extends BaseService
{
    private function getDeliveryCharge($distributorId, $subTotal)
    {
        $distributor = Distributor::with(['delivery_charge_rules'])->find($distributorId);
        if (blank($distributor)) {
            throw new \Exception('Distributor not found!');
        }

        $deliveryChargeRules = data_get($distributor, 'delivery_charge_rules');

        $rule = $deliveryChargeRules->filter(function ($value, $key) use ($subTotal) {
            $max = data_get($value, 'max_basket_size');
            $min = data_get($value, 'min_basket_size');

            return ($subTotal <= $max && $subTotal >= $min);
        })->sortByDesc('delivery_charge');

        return data_get($rule->first(), 'delivery_charge', 0.00);
    }

    private function updateCart($distributorId = null)
    {
        $distributorId = $distributorId ?? request()->get('distributor_id');
        $cart = $this->cartDetails();
        $items = data_get($cart, 'items');

        if (blank($items)) {
            $this->refreshCart();
            return;
        }

        $subTotal = 0.00;
        foreach ($items as $item) {
            $subTotal += $item->item_total;
        }

        $deliveryCharge = $this->getDeliveryCharge($distributorId, $subTotal);
        $total = ($subTotal + $deliveryCharge);

        $data = [
            'distributor_id' => $distributorId,
            'sub_total' => $subTotal,
            'total' => $total,
            'delivery_charge' => $deliveryCharge,
        ];

        $cart->fill($data);
        $cart->save();
    }

    public function addNewCart($distributorId)
    {
        $data = [
            'customer_id' => auth('retailer')->user()->id,
            'sub_total' => 0.00,
            'total' => 0.00,
            'delivery_charge' => 0.00,
            'distributor_id' => $distributorId
        ];

        $cart = new Cart();
        $cart->fill($data);
        $cart->save();

        return $cart;
    }

    public function addOrUpdateCartItem(Cart $cart, Request $request)
    {
        $distributorId = $request->get('distributor_id');
        $productId = $request->get('product_id');

        $distributorProduct = DistributorProduct::with(['product'])
            ->where('distributor_id', $distributorId)
            ->where('product_id', $productId)
            ->first();

        if (blank($distributorProduct)) {
            throw new \Exception("Invalid distributor product!");
        }

        $unitPrice = data_get($distributorProduct, 'product.trade_price');
        $discountedPrice = data_get($distributorProduct, 'distributor_price');

        $qty = $request->get('qty');
        if ($qty < $distributorProduct->min_order_qty) {
            throw new \Exception("Minimum order quantity " . $distributorProduct->min_order_qty);
        }

        $itemTotal = ($qty * $discountedPrice);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $request->get('product_id'),
            'qty' => $request->get('qty'),
            'unit_price' => $unitPrice,
            'discounted_price' => $discountedPrice,
            'item_total' => $itemTotal,
        ];

        $cartItem = CartLineItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if (blank($cartItem)) {
            $cartItem = new CartLineItem();
        }

        $cartItem->fill($data);
        $cartItem->save();

        $this->updateCart();

        return $this->cartDetails();
    }

    public function deleteCartItem($cart, $productId)
    {
        CartLineItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->delete();

        $this->updateCart($cart->distributor_id);

        return $this->cartDetails();
    }

    public function cartDetails()
    {
        $customerId = auth('retailer')->user()->id;
        $cart = Cart::where('customer_id', $customerId)->first();

        if (blank($cart)) {
            return $this->addNewCart(0);
        }

        $distributorId = data_get($cart, 'distributor_id');

        return $cart->load([
            'customer',
            'distributor',
            'items.product',
            'items.distributor_product' => function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            }
        ]);
    }

    public function refreshCart()
    {
        $data = [
            'sub_total' => 0.00,
            'total' => 0.00,
            'delivery_charge' => 0.00,
            'distributor_id' => 0,
        ];

        $cart = Cart::where('customer_id', auth('retailer')->user()->id)->first();

        if (blank($cart)) {
            $cart = new Cart();
            $data['customer_id'] = auth('retailer')->user()->id;
        }

        $cart->fill($data);
        $cart->save();

        CartLineItem::where('cart_id', $cart->id)->delete();
    }
}
