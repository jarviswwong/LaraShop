<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    // Laravel的自动解析会注入CartService类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        // 这里的'.'是嵌套预加载
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function add(AddCartRequest $request)
    {
        $sku_id = $request->input('sku_id');
        $amount = $request->input('amount');

        $this->cartService->add($sku_id, $amount);

        return [];
    }

    public function remove(ProductSku $productSku, Request $request)
    {
        $this->cartService->remove($productSku->id);
        return [];
    }
}
