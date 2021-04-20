<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('test', function () {
    echo 'OK';
});

Route::post('login', 'AuthController@login');

Route::group(['middleware' => 'api'], function () {
    Route::post('distributors/login', 'DistributorAuthController@login');
});

Route::prefix('retailer')->middleware(['kaiju-auth:retailer'])->group(function () {
    /*----------------------------------------------------------------------------------
     * ------ Distributor related endpoints
     * ---------------------------------------------------------------------------------
     * {GET}    /distributors                           Get distributors list
     * {GET}    /distributors/{id}                      Get distributors details
     * {GET}    /distributors/{id}/products             Get distributors products
     * ---------------------------------------------------------------------------------
     */
    Route::get('distributors', 'DistributorController@index');
    Route::get('distributors/{id}', 'DistributorController@show');
    Route::get('distributors/{id}/products', 'DistributorController@products');

    /*----------------------------------------------------------------------------------
     * ------ Cart related endpoints
     * ---------------------------------------------------------------------------------
     * {POST}    /cart/item                         Add or update cart item
     * {DELETE}  /cart/item                         Delete cart item
     * {GET}     /cart/item                         Get cart details
     * {PATCH}   /cart/refresh                      Refresh all cart item
     * {POST}    /confirm/order                     Confirm order based on cart
     * ---------------------------------------------------------------------------------
     */
    Route::post('cart/item', 'CartController@addOrUpdateItem');
    Route::delete('cart/item', 'CartController@deleteItem');
    Route::get('cart/details', 'CartController@getDetails');
    Route::put('cart/refresh', 'CartController@refreshCart');
    Route::post('cart/confirm/order', 'OrderController@confirmOrder');

    /*----------------------------------------------------------------------------------
     * ------ Order related endpoints
     * ---------------------------------------------------------------------------------
     * {GET}    /orders                           Get order list
     * {GET}    /orders/{id}                      Get order details
     * {POST}    /orders                          Create new order
     * ---------------------------------------------------------------------------------
     */
    Route::get('/orders', 'OrderController@retailerOrders');
    Route::get('/orders/{id}', 'OrderController@showRetailerOrder');
    Route::post('/orders', 'OrderController@store');
});

Route::prefix('distributor')->middleware(['distributor-app'])->group(function () {

    /*----------------------------------------------------------------------------------
     * ------ Order related endpoints
     * ---------------------------------------------------------------------------------
     * {GET}    /details                           Get distributors details
     * ---------------------------------------------------------------------------------
     */
    Route::get('/details', 'DistributorController@getDetails')->name('distributor.details');

    /*----------------------------------------------------------------------------------
     * ------ Order related endpoints
     * ---------------------------------------------------------------------------------
     * {GET}    /orders                            Get distributor's order list/ filter list by status / search order
     * {POST}   /orders                            Create new order for distributor
     * {PUT}    /orders/{id}                       Update distributor's order by id
     * {GET}    /orders/{id}                       Get order details by order id
     * {PATCH}  /orders/{id}/confirm               Update order status to confirmed
     * {PATCH}  /orders/{id}/cancel                Update order status to cancelled
     * ---------------------------------------------------------------------------------
     */
    Route::get('/orders', 'DistributorOrderController@orders')->name('distributor.order.list');
    Route::post('/orders/{id?}', 'DistributorOrderController@newOrder')->name('distributor.order.new');
    Route::get('/orders/{id}', 'DistributorOrderController@orderDetails')->name('distributor.order.details');
    Route::patch('/orders/{id}/confirm', 'DistributorOrderController@confirmOrder')->name('distributor.order.confirm');
    Route::patch('/orders/{id}/cancel', 'DistributorOrderController@cancelOrder')->name('distributor.order.cancel');
    Route::patch('/orders/{id}/delivered', 'DistributorOrderController@markAsDelivered')->name('distributor.order.delivered');
    Route::put('/orders/{id}/assign-sr', 'DistributorOrderController@assignSr')->name('distributor.order.assign-sr');

    /*----------------------------------------------------------------------------------
     * ------ Product related endpoints
     * ---------------------------------------------------------------------------------
     * {GET}    /products                       Get distributor's product list / filter by brand
     * {GET}    /products/all                   Get all product list / filter by brand
     * {POST}   /products                       Associate new product with distributor or updated distributor product
     * {PUT}    /products/{id}/update           Update associated product status (Active/Out of stock)
     * {PUT}    /products/{id}/delete           Update associated product status to deleted
     * ---------------------------------------------------------------------------------
     */
    Route::get('/products', 'DistributorProductController@list')->name('distributor.product.list');
    Route::get('/products/all', 'DistributorProductController@allProducts')->name('distributor.product.all');
    Route::post('/products', 'DistributorProductController@addOrUpdateDistributorProduct')->name('distributor.product.save');
    Route::patch('/products/{id}/update/status', 'DistributorProductController@updateStatus')->name('distributor.product.status.update');
    Route::delete('/products/{id}/delete', 'DistributorProductController@delete')->name('distributor.product.delete');

    /*----------------------------------------------------------------------------------
    * ------ Sales Representative related endpoints
    * ---------------------------------------------------------------------------------
    * {GET}    /srs                            Get the list of SR
    * ---------------------------------------------------------------------------------
    */
    Route::get('/srs', 'SalesRepresentativeController@list')->name('distributor.sr.list');

    /*----------------------------------------------------------------------------------
    * ------ Retailer/Customer Related Endpoints
    * ---------------------------------------------------------------------------------
    * {GET}    /customers                       Get the list of Customers
    * ---------------------------------------------------------------------------------
    */
    Route::get('/customers', 'CustomerController@list')->name('distributor.customer.list');
    Route::get('/customers/{authId}/address', 'CustomerController@getAddress')->name('distributor.customer.address');

    /*----------------------------------------------------------------------------------
    * ------ Retailer/Customer Related Endpoints
    * ---------------------------------------------------------------------------------
    * {GET}    /customers                       Get the list of Customers
    * ---------------------------------------------------------------------------------
    */
    Route::get('/profiles', 'ProfileController@loggedUserProfile')->name('logged.user.profile');
});
