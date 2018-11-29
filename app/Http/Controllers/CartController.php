<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // 这里的'.'是嵌套预加载
        $cartItems = $request->user()->cartItems()
            ->with(
                [
                    'product_sku.product',
                    'product_sku.product.skus_attributes',
                    'product_sku.product.skus_attributes.attr_values'
                ]
            )->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $sku_id = $request->input('sku_id');
        $amount = $request->input('amount');

        if ($cart = $user->cartItems()->where('product_sku_id', $sku_id)->first()) {
            $cart->update(['amount' => $cart->amount + $amount]);
        } else {
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->product_sku()->associate($sku_id);
            $cart->save();
        }

        return [];
    }

    public function remove(ProductSku $productSku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id', $productSku->id)->delete();
        return [];
    }
}
