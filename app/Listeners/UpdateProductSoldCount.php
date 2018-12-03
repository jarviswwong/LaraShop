<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use function foo\func;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductSoldCount implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();
        // 延迟预加载
        $order->load(['items.product']);
        foreach ($order->items as $item) {
            $product = $item->product;
            $totalSoldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })
                ->sum('amount');
            $product->update([
                'sold_count' => $totalSoldCount
            ]);
        }
    }
}
