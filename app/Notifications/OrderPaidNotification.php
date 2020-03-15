<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $order = $this->order;
        return (new MailMessage)
            ->greeting('尊敬的 ' . $order->user->name . ' ' . '您好：')
            ->subject('订单支付成功')
            ->line('您于 ' . $order->created_at->format('Y-m-d H:i:s') . ' 创建的订单已经支付成功')
            ->line('订单号：' . $order->no)
            ->line('支付金额：' . $order->total_amount . ' 元')
            ->line('支付方式：' . $order->payment_method)
            ->line('支付流水号：' . $order->payment_no)
            ->action('查看订单详细信息', route('orders.show', ['order' => $order->id]))
            ->success();
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
