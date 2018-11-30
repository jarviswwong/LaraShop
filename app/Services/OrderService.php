<?php

namespace App\Services;

use App\Exceptions\InternalException;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
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
                'remark' => $remark,
                'total_amount' => 0,
            ]);

            // 插入对应用户ID
            $order->user()->associate($user);

            $order->save();

            // 接下来保存order_items
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
            $sku_id_collection = collect($items)->pluck('sku_id')->all();  // 这里别忘记转换成数组
            app(CartService::class)->remove($sku_id_collection);

            return $order;
        });
        // 分发'订单过期'任务，延迟时间从配置文件中获取
        CloseOrder::dispatch($order)
            ->delay(config('app.order_ttl'));

        return $order;
    }
}