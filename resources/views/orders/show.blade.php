@extends('layouts.app')
@section('title', '查看订单')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>订单详情</h4>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>商品信息</th>
                            <th class="text-center">单价</th>
                            <th class="text-center">数量</th>
                            <th class="text-right item-amount">小计</th>
                        </tr>
                        </thead>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="product-info" @if($index == 0) style="border-top: 0px;" @endif>
                                    <div class="preview">
                                        <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                                            <img src="{{ $item->product->image_url }}">
                                        </a>
                                    </div>
                                    <div>
                                        <span class="product-title">
                                           <a target="_blank"
                                              href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                        </span>
                                        <div class="sku-attrs">
                                            @foreach($item->product->skus_attributes as $attribute)
                                                <div class="attributes">
                                                    <span class="title">{{$attribute->name}}：</span>
                                                    <span class="value">
                                                        @foreach($attribute->attr_values as $attr_value)
                                                            @if(in_array($attr_value->symbol, $item->product_sku->attr_array))
                                                                {{$attr_value->value}}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
                                <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                                <td class="item-amount text-right vertical-middle">
                                    ￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line">
                                <div class="line-label">收货地址：</div>
                                <div class="line-value">
                                    {{ $order->address['contact_name'] }}, {{ $order->address['contact_phone'] }}
                                    , {{ $order->address['full_address'] }}, {{ $order->address['zip'] }}
                                </div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单备注：</div>
                                <div class="line-value">{{ $order->remark ?: '-' }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单编号：</div>
                                <div class="line-value">{{ $order->no }}</div>
                            </div>
                        </div>
                        <div class="order-summary text-right">
                            <div class="total-amount">
                                <span>订单总价：</span>
                                <div class="value">￥{{ $order->total_amount }}</div>
                            </div>
                            {{--优惠信息显示--}}
                            @if ($order->coupon_code_id)
                                <div class="coupon-info">
                                    <div class="value">
                                        {{ $order->couponCode->coupon_rule }}
                                    </div>
                                </div>
                            @endif
                            <div class="order-status">
                                <span>订单状态：</span>
                                <div class="value">
                                    @if($order->paid_at)
                                        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
                                                已支付
                                            @else
                                                {{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}
                                            @endif
                                        @else
                                            {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                        @endif
                                    @elseif($order->closed)
                                        已关闭
                                    @else
                                        未支付
                                    @endif
                                </div>
                            </div>
                            @if($order->ship_status !== \App\Models\Order::SHIP_STATUS_PENDING)
                                <div class="ship_status">
                                    <div>
                                        <span>物流：</span>
                                        <div class="value">{{ $order->ship_data['express_company'] }}</div>
                                    </div>
                                    <div>
                                        <span>运单号：</span>
                                        <div class="value">{{ $order->ship_data['express_no'] }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="order-actions-buttons">
                                @if(!$order->paid_at && !$order->closed)
                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
                                    <a class="btn btn-success btn-sm"
                                       href="#">微信支付</a>
                                @endif
                                @if($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                    <button type="button" class="btn btn-danger btn-apply-refund">申请退款</button>
                                    @if ($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                                        <button type="button" class="btn btn-success btn-received">确认收货</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($order->extra && $order->extra['refund_count'] > 0)
        {{--退款记录--}}
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>退款记录</h5>
                    </div>
                    <table class="table refund-record-table">
                        <tr>
                            <th>#</th>
                            <th>申请退款时间</th>
                            <th>退款理由</th>
                            <th>退款结果</th>
                        </tr>
                        @for($index = 1; $index <= $order->extra['refund_count']; ++$index)
                            <tr>
                                <th>
                                    {{ $index }}
                                </th>
                                <td>
                                    {{ $order->extra['refund_index_'.$index]['refund_at'] }}
                                </td>
                                <td>
                                    {{ $order->extra['refund_index_'.$index]['refund_reason'] }}
                                </td>
                                @if(array_key_exists('agree', $order->extra['refund_index_'.$index]))
                                    <td class="{{ $order->extra['refund_index_'.$index]['agree'] ? 'success' : 'danger' }}">
                                        <span style="font-weight: 600;">
                                            {{ $order->extra['refund_index_'.$index]['agree'] ? '已同意，请等待退款到账' : '已拒绝' }}
                                        </span>
                                        @if(array_key_exists('refund_handle_reason', $order->extra['refund_index_'.$index]))
                                            <br/>{{ $order->extra['refund_index_'.$index]['refund_handle_reason'] }}
                                        @endif
                                        {{--有退款单号且卖家同意退款的退款批次--}}
                                        @if($order->refund_no && $order->extra['refund_index_'.$index]['agree'] === true)
                                            <br/>退款订单号：{{ $order->refund_no }}
                                        @endif
                                        <br/>处理时间：{{ $order->extra['refund_index_'.$index]['refund_handle_at'] }}
                                    </td>
                                @else
                                    <td>
                                        正在处理中，请稍后
                                    </td>
                                @endif
                            </tr>
                        @endfor
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            $('.btn-received').on('click', function () {
                swal({
                    title: '确认收货吗？',
                    text: '请收到商品后再确认收货，否则可能人财两空！',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    focusCancel: true,
                }).then(function (result) {
                    if (result.value) {
                        axios.post('{{ route('orders.received', ['order' => $order->id]) }}')
                            .then(function () {
                                location.reload();
                            });
                    }
                });
            });

            $('.btn-apply-refund').on('click', function () {
                swal({
                    title: '请输入退款原因',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    focusCancel: true,
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        return !value && '必须填写退款原因';
                    }
                }).then((result) => {
                    if (result.value) {
                        axios.post('{{ route('orders.refund.apply', ['order' => $order]) }}',
                            {reason: result.value})
                            .then(() => {
                                swal('申请退款成功', '', 'success')
                                    .then(() => location.reload());
                            })
                            .catch((error) => {
                                if (error.response) {
                                    swal('退款失败', error.response.data.msg, 'error')
                                        .then(() => location.reload());
                                }
                            });
                    }
                });
            });
        });
    </script>
@endsection