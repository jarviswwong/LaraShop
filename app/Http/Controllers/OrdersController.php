<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $userAddress = UserAddress::find($request->input('address_id'));

        $remark = $request->input('remark');
        $items = $request->input('items');

        return $this->orderService->store($user, $userAddress, $remark, $items);
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(
                [
                    'items.product',
                    'items.product.skus_attributes',
                    'items.product.skus_attributes.attr_values',
                    'items.product_sku',
                ]
            )->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order)
    {
        // 只有创建订单的人才能查看订单的详细信息
        $this->authorize('own', $order);

        $delay_load_relations = [
            'items.product',
            'items.product.skus_attributes',
            'items.product.skus_attributes.attr_values',
            'items.product_sku',
        ];

        // 这里只是取出单条order数据，故使用延迟加载
        return view('orders.show', ['order' => $order->load($delay_load_relations)]);
    }
}
