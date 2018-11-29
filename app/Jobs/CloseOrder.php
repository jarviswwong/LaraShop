<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * 从队列中调出任务，会执行handle方法
     */
    public function handle()
    {
        // 如果订单在任务取出队列前已经支付
        if ($this->order->paid_at) {
            return;
        }

        // 用数据库事务 执行sql
        \DB::transaction(function () {
            $this->order->update(['closed' => true]);
            $this->order->items()
                ->each(function ($item) {
                    $item->product_sku->addStock($item->amount);
                });
        });
    }
}
