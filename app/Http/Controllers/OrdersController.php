<?php

namespace App\Http\Controllers;

use App\Exceptions\InternalException;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $order = \DB::transaction(function () use ($request, $user) {
            $address = UserAddress::query()->where('id', $request->input('address_id'))->first();
            // 记录本次地址使用的时间
            $address->update(['last_used_at' => Carbon::now()]);

            // 生成订单数据
            $order = new Order([
                'address' => [
                    'full_address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $request->input('remark'),
                'total_amount' => 0,
            ]);

            // 插入对应用户ID
            $order->user()->associate($user);

            $order->save();

            // 接下来保存order_items
            $items = $request->input('items');
            $total_amount = 0;
            foreach ($items as $item) {
                $sku = ProductSku::query()->find($item['sku_id']);
                $orderItem = $order->items()->make([
                    'amount' => $item['amount'],
                    'price' => $sku->price,
                ]);
                $orderItem->product_sku()->associate($sku);
                $orderItem->product()->associate($sku->product_id);
                $orderItem->save();
                $total_amount += $item['amount'] * ($sku->price);
                // 因为用的是数据库事务，抛出异常后，前面的保存操作均会回滚
                if ($sku->decreaseStock($item['amount']) <= 0) {
                    throw new InternalException('该商品库存不足');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $total_amount]);

            // 从购物车中删除商品
            $sku_id_collection = collect($items)->pluck('sku_id');
            $user->cartItems()
                ->whereIn('product_sku_id', $sku_id_collection)
                ->delete();

            // 分发'订单过期'任务，延迟时间从配置文件中获取
            CloseOrder::dispatch($order)
                ->delay(config('app.order_ttl'));
        });

        return $order;
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
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order)
    {
        // 只有创建订单的人才能查看订单的详细信息
        $this->authorize('own', $order);

        $delay_load_relations = [
            'items.product',
            'items.product.skus_attributes',
            'items.product.attr_values',
            'items.product_sku',
        ];

        // 这里只是取出单条order数据，故使用延迟加载
        return view('orders.show', ['order' => $order->load($delay_load_relations)]);
    }
}
