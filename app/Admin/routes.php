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

    // Products route
    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->delete('products/{id}', 'ProductsController@destroy');
    // Products SKU
    $router->get('product_skus/{product_id}', 'ProductSkusController@index')->name('admin.product_skus.index');
    $router->get('product_skus/{product_id}/create', 'ProductSkusController@create');
    $router->post('product_skus/{product_id}', 'ProductSkusController@store');
    $router->get('product_skus/{product_id}/{id}/edit', 'ProductSkusController@edit');
    $router->put('product_skus/{product_id}/{id}', 'ProductSkusController@update');
    $router->delete('product_skus/{product_id}/{id}', 'ProductSkusController@destroy');
    // Orders
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.order.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.order.ship');
});
