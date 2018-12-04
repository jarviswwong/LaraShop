<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // 创建订单
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $userAddress = UserAddress::find($request->input('address_id'));

        $remark = $request->input('remark');
        $items = $request->input('items');

        return $this->orderService->store($user, $userAddress, $remark, $items);
    }

    // 用户订单列表
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(
                [
                    'items.product.skus_attributes.attr_values',
                    'items.product_sku',
                ]
            )->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orders.index', ['orders' => $orders]);
    }

    // 订单详细信息
    public function show(Order $order)
    {
        // 只有创建订单的人才能查看订单的详细信息
        $this->authorize('own', $order);

        $delay_load_relations = [
            'items.product.skus_attributes.attr_values',
            'items.product_sku',
        ];

        // 这里只是取出单条order数据，故使用延迟加载
        return view('orders.show', ['order' => $order->load($delay_load_relations)]);
    }

    /**
     * 用户收货
     *
     * @param \App\Models\Order $order
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function received(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('订单发货状态不正确，无法收货');
        }

        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED
        ]);

        return redirect()->back();
    }

    /**
     * 用户评价
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \App\Exceptions\InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function review(Order $order)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，请先支付');
        }
        $order->load([
            'items.product.skus_attributes.attr_values',
            'items.product_sku',
        ]);
        return view('orders.review', ['order' => $order]);
    }

    /**
     * @param \App\Models\Order $order
     * @param \App\Http\Requests\SendReviewRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，请先支付');
        }

        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价');
        }

        if ($order->ship_status !== Order::SHIP_STATUS_RECEIVED) {
            throw new InvalidRequestException('确认收货后方可评价');
        }

        $reviews = $request->input('reviews');
        // 开启数据库事务
        \DB::transaction(function () use ($order, $reviews) {
            foreach ($reviews as $key => $review) {
                $order->items()->find($review['id'])
                    ->update([
                        'rating' => $review['rating'],
                        'review' => $review['review'],
                        'reviewed_at' => Carbon::now(),
                    ]);
            }
            $order->update(['reviewed' => true]);
            event(new OrderReviewed($order));
        });
        return redirect()->back();
    }
}
