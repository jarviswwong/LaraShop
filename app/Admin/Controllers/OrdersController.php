<?php

namespace App\Admin\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->description('Orders List')
            ->body($this->grid());
    }

    public function show(Order $order, Content $content)
    {
        // 延迟预加载
        $order->load([
            'items.product',
            'items.product.skus_attributes',
            'items.product.skus_attributes.attr_values',
            'items.product_sku',
        ]);
        return $content
            ->header('订单详情')
            ->description('Order Details')
            ->body(view('admin.orders.show', ['order' => $order]));
    }

    protected function grid()
    {
        $grid = new Grid(new Order);
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('订单流水号');
        $grid->column('user.name', '买家');
        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();
        $grid->ship_status('物流状态')->display(function ($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款状态')->sortable()->display(function ($value) {
            $status = Order::$refundStatusMap[$value];
            $route = route('admin.order.refund.show', ['order' => $this->id]);
            return "<a href='$route'>$status</a>";
        });
        // 禁用创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }

    // 发货
    public function ship(Order $order, Request $request)
    {
        // 确保订单已付款
        if (!$order->paid_at)
            throw new InvalidRequestException('该订单未付款');
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING)
            throw new InvalidRequestException('该订单已发货');

        // $data is array
        $data = $this->validate($request, [
            'express_company' => 'required',
            'express_no' => 'required',
        ], [], [
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);

        // 'ship_data' field type is json
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => $data,
        ]);

        return redirect()->back();
    }

    public function refundShow(Order $order, Content $content)
    {
        $refund_count = $order->extra['refund_count'] ?: 0;
        $extra = $order->extra ?: [];
        return $content
            ->header('订单退款详情')
            ->description('Order Refund Details')
            ->body(view('admin.orders.refund', [
                'order' => $order,
                'refund_count' => $refund_count,
                'extra' => $extra,
            ]));
    }

    /**
     * 管理员处理退款
     *
     * @param \App\Models\Order $order
     * @param \App\Http\Requests\Admin\HandleRefundRequest $request
     * @return \App\Models\Order
     * @throws \App\Exceptions\InternalException
     * @throws \App\Exceptions\InvalidRequestException
     */
    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单退款状态不正确');
        }

        $extra = ($order->extra ?: []);
        $refund_count = $extra['refund_count'];
        if ($request->input('agree')) {
            // 同意退款
            // 先调用支付平台退款逻辑
            $this->_refundOrderFromPaymentInstrument($order);

            $extra['refund_index_' . $refund_count]['agree'] = true;
            $extra['refund_index_' . $refund_count]['refund_handle_at'] = Carbon::now()->toDateTimeString();

            $order->update([
                'extra' => $extra,
            ]);
        } else {
            // 不同意退款
            $extra['refund_index_' . $refund_count]['agree'] = false;
            $extra['refund_index_' . $refund_count]['refund_handle_reason'] = $request->input('reason');
            $extra['refund_index_' . $refund_count]['refund_handle_at'] = Carbon::now()->toDateTimeString();

            $order->update([
                // 注意，此处将状态改为未退款，方便用户再次申请
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra' => $extra,
            ]);
        }
        return $order;
    }

    /**
     * 调用支付接口进行退款
     *
     * @param \App\Models\Order $order
     * @throws \App\Exceptions\InternalException
     */
    protected function _refundOrderFromPaymentInstrument(Order $order)
    {
        switch ($order->payment_method) {
            case "wechat":
                // 微信支付暂留
                break;
            case "alipay":
                $refund_no = Order::getAvailableRefundNo();
                // 调用支付宝实例的refund方法
                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $refund_no,
                ]);

                if ($ret->sub_code) {
                    $extra = $order->extra;
                    $extra['refund_index_' . $extra['refund_count']]['refund_failed_code'] = $ret->sub_code;

                    $order->update([
                        'refund_no' => $refund_no,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    $order->update([
                        'refund_no' => $refund_no,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                throw new InternalException('未知订单支付方式：' . $order->payment_method);
                break;
        }
    }
}
