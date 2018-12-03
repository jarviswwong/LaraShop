<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderPaidMail
{
    public function __construct()
    {
        //
    }

    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();
        $order->user->notify(new OrderPaidNotification($order));
    }
}
