<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\DeliveryChargeRule;
use App\Models\Distributor;
use App\Models\DistributorProduct;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Presenters\CartPresenter;
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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    use WrapInTransaction;

    const MISMATCH_DISTRIBUTOR = 'MISMATCH_DISTRIBUTOR';
    const CART_ITEM_ADD_FAILED = 'CART_ITEM_ADD_FAILED';
    const CART_ITEM_ADDED_SUCCESSFULLY = 'CART_ITEM_ADDED_SUCCESSFULLY';
    const CART_ITEM_DELETED_SUCCESSFULLY = 'CART_ITEM_DELETED_SUCCESSFULLY';
    const CART_ITEM_DELETE_FAILED = 'CART_ITEM_DELETE_FAILED';
    const CART_DETAILS_RETRIEVED_SUCCESSFULLY = 'CART_DETAILS_RETRIEVED_SUCCESSFULLY';
    const CART_DETAILS_REFRESHED_SUCCESSFULLY = 'CART_DETAILS_REFRESHED_SUCCESSFULLY';
    /**
     * @var CartService
     */
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addOrUpdateItem(Request $request)
    {
        return $this->wrapInTransaction(function ($request) {
            try {
                $cart = $this->cartService->cartDetails();
                $distributorId = $request->get('distributor_id');

                if (blank($cart)) {
                    $cart = $this->cartService->addNewCart($distributorId);
                }

                if (!blank($cart)&& !empty($cart->distributor_id) && $distributorId != $cart->distributor_id) {
                    return api()->details("Item from multiple distributor is not allowed!")
                        ->fails(self::MISMATCH_DISTRIBUTOR, Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $cart = $this->cartService->addOrUpdateCartItem($cart, $request);

                return api((new CartPresenter($cart->toArray()))->get())
                    ->details('Cart item added successfully')
                    ->success(self::CART_ITEM_ADDED_SUCCESSFULLY);

            } catch (\Exception $e) {

                return api()->details($e->getMessage())
                    ->fails(self::CART_ITEM_ADD_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }, $request);
    }

    public function deleteItem(Request $request)
    {
        return $this->wrapInTransaction(function($request){
            try {
                $productId = $request->get('product_id');
                if (blank($productId)) {
                    throw new \Exception("Invalid cart item!");
                }

                $existingCart = $this->cartService->cartDetails();
                $cart = $this->cartService->deleteCartItem($existingCart, $productId);

                return api((new CartPresenter($cart->toArray()))->get())
                    ->details('Cart item deleted successfully')
                    ->success(self::CART_ITEM_DELETED_SUCCESSFULLY);
            } catch (\Exception $e) {
                return api()->details($e->getMessage())
                    ->fails(self::CART_ITEM_DELETE_FAILED, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }, $request);
    }

    public function getDetails()
    {
        $cart = $this->cartService->cartDetails();

        return api((new CartPresenter($cart->toArray()))->get())
            ->details('Cart details retrieved successfully')
            ->success(self::CART_DETAILS_RETRIEVED_SUCCESSFULLY);
    }

    public function refreshCart()
    {
        return $this->wrapInTransaction(function (){
            $this->cartService->refreshCart();

            return api([])
                ->details('Cart refreshed successfully')
                ->success(self::CART_DETAILS_REFRESHED_SUCCESSFULLY);
        });
    }
}
