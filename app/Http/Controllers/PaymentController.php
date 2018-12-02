<?php

namespace App\Http\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * 支付宝支付方法
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function payByAlipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单已支付或已关闭');
        }

        return app('alipay')->web([
            'out_trade_no' => $order->no,
            'total_amount' => $order->total_amount,
            'subject' => '支付LaraShop订单：' . $order->no,
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        $data = app('alipay')->verify();
        try {
            app('alipay')->verify();
        } catch (\Exception $exception) {
            return view('pages.error', ['msg' => '校验支付数据不正确']);
        }

        return view('pages.success', ['msg' => '支付成功']);
    }

    // 服务端回调页面
    public function alipayNotify()
    {
        $data = app('alipay')->verify();

        // 如果不在这两种状态内，则不走后续的逻辑
        // status: https://docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }

        $order = Order::query()->where('no', $data->out_trade_no)->first();
        if (!$order) {
            throw new InternalException('该订单不存在');
        }

        if ($order->paid_at) {
            return app('alipay')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no' => $data->trade_no,
        ]);

        //app('alipay')->success()会返回信息给支付宝，支付宝将不再返回回调，否则将每隔一段时间返回服务器回调
        return app('alipay')->success();
    }
}
