<?php

namespace App\Services;

use App\Models\CartItem;

class CartService
{
    /**
     * 获取购物车模型实例
     * @return mixed
     */
    public function get()
    {
        // 此处的'.'是嵌套预加载
        $relations_array = [
            'product_sku.product',
            'product_sku.product.skus_attributes',
            'product_sku.product.skus_attributes.attr_values'
        ];
        return \Auth::user()->cartItems()->with($relations_array)->get();
    }

    /**
     * 添加商品入购物车
     * @param $sku_id
     * @param $amount
     * @return CartItem
     */
    public function add($sku_id, $amount)
    {
        $user = \Auth::user();

        if ($cart = $user->cartItems()->where('product_sku_id', $sku_id)->first()) {
            $cart->update(['amount' => $cart->amount + $amount]);
        } else {
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->product_sku()->associate($sku_id);
            $cart->save();
        }

        return $cart;
    }

    /**
     * 删除购物车中的商品，支持批量删除
     * @param $product_sku_ids
     */
    public function remove($product_sku_ids)
    {
        if (!is_array($product_sku_ids)) {
            $product_sku_ids = [$product_sku_ids];
        }

        \Auth::user()->cartItems()->whereIn('product_sku_id', $product_sku_ids)->delete();
    }
}