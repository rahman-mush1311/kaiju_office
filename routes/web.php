<?php

use App\Http\Controllers\SalesRepresentativeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'AuthController@loginPage')->name('auth.login.page');
Route::post('login', 'AuthController@login')->name('auth.login');
Route::get('logout', 'AuthController@logout')->name('auth.logout');

Route::get('order/{id}', 'OrderController@show')->name('order.show');

Route::group(['middleware' => ['auth','admin']], function() {
    Route::get('/', 'DashboardController@index')->name('home');

    Route::get('/products', 'ProductController@index')->name('product.index');
    Route::get('/products/export', 'ProductController@exportExcel')->name('product.export');
    //Route::get('/products/search', 'ProductController@searchProduct')->name('product.search'); // moved to distributor ACL
    Route::get('/products/create', 'ProductController@create')->name('product.create');
    Route::post('/products', 'ProductController@store')->name('product.store');
    Route::post('/products', 'ProductController@store')->name('product.store');
    Route::get('/products/{id}/edit', 'ProductController@edit')->name('product.edit');
    Route::put('/products/{id}', 'ProductController@update')->name('product.update');
    Route::get('/products/export-import', 'ProductController@exportImportView')->name('product.export-import');
    Route::get('/products/export', 'ProductController@exportExcel')->name('product.export');
    Route::post('/products/import', 'ProductController@importExcel')->name('product.import.save');
    Route::get('/distributor-products', 'ProductController@distributorProducts')->name('distributor.products');

    Route::get('/delivery-charge-rules', 'DeliveryChargeRuleController@index')->name('delivery.charge.rules.index');
    Route::get('/delivery-charge-rules/create', 'DeliveryChargeRuleController@create')->name('delivery.charge.rules.create');
    Route::post('/delivery-charge-rules', 'DeliveryChargeRuleController@store')->name('delivery.charge.rules.store');
    Route::get('/delivery-charge-rules/{id}/edit', 'DeliveryChargeRuleController@edit')->name('delivery.charge.rules.edit');
    Route::put('/delivery-charge-rules/{id}', 'DeliveryChargeRuleController@update')->name('delivery.charge.rules.update');

    Route::get('/brands', 'BrandController@index')->name('brands.index');
    Route::get('/brands/create', 'BrandController@create')->name('brands.create');
    Route::post('/brands', 'BrandController@store')->name('brands.store');
    Route::get('/brands/{id}/edit', 'BrandController@edit')->name('brands.edit');
    Route::put('/brands/{id}', 'BrandController@update')->name('brands.update');

    Route::get('locations', 'LocationController@index')->name('location.index');
    Route::get('locations/sync', 'LocationController@syncLocation')->name('location.sync');
    Route::get('locations/create', 'LocationController@create')->name('location.create');
    Route::get('locations/{id}/edit', 'LocationController@edit')->name('location.edit');
    Route::post('locations', 'LocationController@store')->name('location.store');
    Route::put('locations/{id}', 'LocationController@update')->name('location.update');
    Route::delete('locations/{id}', 'LocationController@destroy')->name('location.delete');
    Route::get('locations/search/select2', 'LocationController@locationListSelect2')->name('locations.search.select2');

    Route::get('areas', 'AreaController@index')->name('area.index');
    Route::get('areas/sync', 'AreaController@syncArea')->name('area.sync');
    Route::get('areas/search', 'AreaController@searchArea')->name('area.search');
    Route::get('areas/create', 'AreaController@create')->name('area.create');
    Route::get('areas/{id}/edit', 'AreaController@edit')->name('area.edit');
    Route::post('areas', 'AreaController@store')->name('area.store');
    Route::put('areas/{id}', 'AreaController@update')->name('area.update');
    Route::delete('areas/{id}', 'AreaController@destroy')->name('area.delete');
    Route::get('areas/search/select2/{locationId}', 'AreaController@areaListSelect2')->name('areas.search.select2');

    //Route::get('customers', 'CustomerController@index')->name('customers.index');  // moved to distributor ACL
    Route::get('customers/search/select2', 'CustomerController@customerListSelect2')->name('customers.search.select2');
    Route::get('customers/{id}/edit', 'CustomerController@edit')->name('customers.edit');
    Route::put('customers/{id}/edit', 'CustomerController@update')->name('customers.update');
});

Route::group(['middleware' => ['auth','distributor']], function() {
    Route::get('/', 'DashboardController@index')->name('home');

    Route::get('/products/search', 'ProductController@searchProduct')->name('product.search');
    Route::get('/distributor-products', 'ProductController@distributorProducts')->name('distributor.products');

    Route::get('distributors', 'DistributorController@index')->name('distributors.index');
    Route::get('distributors/create', 'DistributorController@create')->name('distributors.create');
    Route::post('distributors', 'DistributorController@store')->name('distributors.store');
    Route::get('distributors/{id}/edit', 'DistributorController@edit')->name('distributors.edit');
    Route::put('distributors/{id}', 'DistributorController@update')->name('distributors.update');
    Route::get('distributors/search/select2', 'DistributorController@distributorListSelect2')->name('distributors.search.select2');
    Route::get('distributors/{id}/assign-product', 'DistributorController@getAssignProductForm')->name('distributors.assign-product');
    Route::post('distributors/{id}/assign-product', 'DistributorController@assignProduct')->name('distributors.assign-product');
    Route::get('distributors/{id}/detach-product/{productId}', 'DistributorController@detachProduct')->name('distributors.detach-product');
    Route::get('distributors/{id}/export-products', 'DistributorController@exportProduct')->name('distributors.export-products');
    Route::post('distributors/import-products', 'DistributorController@importProducts')->name('distributors.import-products');
    Route::get('distributors/import-products/{id?}', 'DistributorController@getImportProduct')->name('distributors.import-products');

    Route::get('orders', 'OrderController@index')->name('order.index');
    Route::get('orders/{id}/edit', 'OrderController@edit')->name('order.edit');
    Route::put('orders/{id}', 'OrderController@update')
        ->middleware(['rem-index'])
        ->name('order.update');
    Route::get('order/{orderId}/delivery-charge', 'OrderController@getDeliveryChargeRuleByOrder')->name('order.delivery-charge');

    Route::group(['prefix' => 'sr'], function () {
        Route::get('/', [SalesRepresentativeController::class, 'index'])->name('sr.index');
        Route::get('create', [SalesRepresentativeController::class, 'create'])->name('sr.create');
        Route::post('create', [SalesRepresentativeController::class, 'store'])->name('sr.store');
        Route::get('{sr:id}/edit', [SalesRepresentativeController::class, 'edit'])->name('sr.edit');
        Route::put('{sr:id}/edit', [SalesRepresentativeController::class, 'update'])->name('sr.update');
    });

    Route::get('customers', 'CustomerController@index')->name('customers.index');
});


Route::group(['middleware' => ['auth','sales_representative']], function() {
    Route::get('/', 'DashboardController@index')->name('home');

    Route::get('/products/search', 'ProductController@searchProduct')->name('product.search');
    Route::get('/distributor-products', 'ProductController@distributorProducts')->name('distributor.products');

    Route::get('orders', 'OrderController@index')->name('order.index');
    Route::get('orders/{id}/edit', 'OrderController@edit')->name('order.edit');
    Route::put('orders/{id}', 'OrderController@update')
        ->middleware(['rem-index'])
        ->name('order.update');

    Route::get('customers', 'CustomerController@index')->name('customers.index');
});


Route::get('password', function () {
    return bcrypt("123456");
});
