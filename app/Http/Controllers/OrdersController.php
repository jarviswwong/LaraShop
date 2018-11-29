<?php

namespace App\Http\Controllers;

use App\Exceptions\InternalException;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;

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
        });

        return $order;
    }
}
