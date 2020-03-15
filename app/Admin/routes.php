<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->get('users', 'UsersController@index');

    // 商品
    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->delete('products/{id}', 'ProductsController@destroy');

    // 秒杀商品
    $router->get('seckill_products', 'SeckillProductsController@index');
    $router->get('seckill_products/create', 'SeckillProductsController@create');
    $router->post('seckill_products', 'SeckillProductsController@store');
    $router->get('seckill_products/{id}/edit', 'SeckillProductsController@edit');
    $router->put('seckill_products/{id}', 'SeckillProductsController@update');

    // 商品 SKU
    $router->get('product_skus/{product_id}', 'ProductSkusController@index')->name('admin.product_skus.index');
    $router->get('product_skus/{product_id}/create', 'ProductSkusController@create');
    $router->post('product_skus/{product_id}', 'ProductSkusController@store');
    $router->get('product_skus/{product_id}/{id}/edit', 'ProductSkusController@edit');
    $router->put('product_skus/{product_id}/{id}', 'ProductSkusController@update');
    $router->delete('product_skus/{product_id}/{id}', 'ProductSkusController@destroy');

    // 商品属性
    $router->get('product_attr_values/{product}/show', 'ProductAttrValuesController@index')->name('admin.productAttrValues.index');
    $router->post('product_attr_values/change_order/{product}', 'ProductAttrValuesController@changeOrder')->name('admin.productAttrValues.changeOrder');
    $router->post('product_attr_values/create', 'ProductAttrValuesController@create')->name('admin.productAttrValues.create');
    $router->delete('product_attr_values/{attrValue}/destroy', 'ProductAttrValuesController@destroy')->name('admin.productAttrValues.destroy');

    // 订单
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.order.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.order.ship');
    $router->get('orders/{order}/refund', 'OrdersController@refundShow')->name('admin.order.refund.show');
    // 管理员处理退款
    $router->post('orders/{order}/refund/handle', 'OrdersController@handleRefund')->name('admin.orders.refund.handle');

    // 优惠券
    $router->get('coupon_codes', 'CouponCodesController@index')->name('admin.coupon.index');
    $router->get('coupon_codes/create', 'CouponCodesController@create')->name('admin.coupon.create');
    $router->post('coupon_codes', 'CouponCodesController@store');
    $router->get('coupon_codes/{id}/edit', 'CouponCodesController@edit')->name('admin.coupon.edit');
    $router->put('coupon_codes/{id}', 'CouponCodesController@update');
    $router->delete('coupon_codes/{id}', 'CouponCodesController@destroy');

    // 商品类目
    $router->get('categories', 'CategoriesController@index');
    $router->get('categories/create', 'CategoriesController@create');
    $router->get('categories/{id}/edit', 'CategoriesController@edit');
    $router->post('categories', 'CategoriesController@store');
    $router->put('categories/{id}', 'CategoriesController@update');
    $router->delete('categories/{id}', 'CategoriesController@destroy');
    $router->get('api/categories', 'CategoriesController@apiIndex');
});
