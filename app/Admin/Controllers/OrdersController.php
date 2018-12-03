<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Http\Controllers\Controller;
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
        $grid->refund_status('退款状态')->display(function ($value) {
            return Order::$refundStatusMap[$value];
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
}
