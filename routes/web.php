<?php

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
Auth::routes();

Route::redirect('/', 'products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');
Route::get('products/{product}', 'ProductsController@show')->name('products.show');

Route::group(['middleware' => 'auth'], function () {
    // 邮箱验证路由
    Route::get('/email_verify_notice', 'PagesController@emailVerifiedNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');

    Route::group(['middleware' => 'email_verified'], function () {
        // 收货地址路由
        Route::get('/user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
        Route::get('/user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
        Route::get('/user_addresses/edit/{userAddress}', 'UserAddressesController@edit')->name('user_addresses.edit');
        Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
        Route::put('/user_addresses/update/{userAddress}', 'UserAddressesController@update')->name('user_addresses.update');
        Route::delete('/user_addresses/destroy/{userAddress}', 'UserAddressesController@destroy')->name('user_addresses.destroy');

        // 收藏商品路由
        Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
        Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
        Route::get('products/favorites/list', 'ProductsController@favorites')->name('products.favorites');

        // 购物车路由
        Route::post('cart', 'CartController@add')->name('cart.add');
        Route::get('cart', 'CartController@index')->name('cart.index');
        Route::delete('cart/{productSku}', 'CartController@remove')->name('cart.remove');

        // 订单路由
        Route::post('orders', 'OrdersController@store')->name('orders.store');
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
        // 收货路由
        Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
        // 评价路由
        Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
        Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
        // 退款路由
        Route::post('orders/{order}/refund/apply', 'OrdersController@applyRefund')->name('orders.refund.apply');

        // Alipay支付路由
        Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
        Route::get('payment/alipay/{order}', 'PaymentController@payByAlipay')->name('payment.alipay');

        // 优惠券路由
        Route::post('coupon_codes/verify', 'CouponCodesController@verify')->name('coupon.verify');
    });
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');

// Test Route