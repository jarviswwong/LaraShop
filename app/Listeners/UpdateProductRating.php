<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// 异步更新评分
class UpdateProductRating implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(OrderReviewed $event)
    {
        $items = $event->getOrder()->items()->with(['product'])->get();
        foreach ($items as $item) {
            $product = $item->product;

            $rating = (($product->rating * $product->review_count) + $item->rating) / ($product->review_count + 1);
            $product->update([
                'rating' => $rating,
                'review_count' => $product->review_count + 1,
            ]);
        }
    }
}
