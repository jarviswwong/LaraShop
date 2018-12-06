@if($refund_count && $refund_count > 0)
    <ul class="timeline">
        @for($index = 1; $index <= $refund_count; ++$index)
            <li class="time-label">
                <span class="bg-red">
                    第 {{ $index }} 次发起退款
                </span>
            </li>
            <li>
                <i class="fa fa-user bg-aqua"></i>
                <div class="timeline-item">
                    <span class="time">
                        <i class="fa fa-clock-o"></i>
                        {{ $extra['refund_index_'.$index]['refund_at'] }}
                    </span>
                    <h3 class="timeline-header">
                        <a href="#">客户 {{ $order->user->name }}</a> 申请退款
                    </h3>

                    <div class="timeline-body">
                        退款理由：<span style="font-weight: 600;">{{ $extra['refund_index_'.$index]['refund_reason']}}</span>
                        <div style="margin-top: 5px;">退款凭证：</div>
                    </div>
                    @if(!array_key_exists('agree',  $extra['refund_index_'.$index]))
                        <div class="timeline-footer">
                            <button class="btn btn-success btn-xs btn-agree-refund">同意退款</button>
                            <button class="btn btn-danger btn-xs btn-disagree-refund">拒绝退款</button>
                        </div>
                    @endif
                </div>
            </li>
            @if(array_key_exists('agree',  $extra['refund_index_'.$index]))
                <li>
                    <i class="fa fa-user-secret bg-green"></i>
                    <div class="timeline-item">
                        <span class="time">
                            <i class="fa fa-clock-o"></i>
                            {{ $extra['refund_index_'.$index]['refund_handle_at'] }}
                        </span>
                        <h3 class="timeline-header">
                            <a href="#">{{ Admin::user()->username }}</a>
                        </h3>

                        <div class="timeline-body">
                            <span style="{{  $extra['refund_index_'.$index]['agree'] ? 'color: green;' : 'color: red;' }}
                                    font-weight: 600; font-size: 16px;">
                                {{ $extra['refund_index_'.$index]['agree'] ? '已同意退款' : '已拒绝退款' }}
                            </span>
                            @if(array_key_exists('refund_handle_reason', $extra['refund_index_'.$index]))
                                <div class="refund_handle_reason">
                                    {{ $extra['refund_index_'.$index]['refund_handle_reason'] }}
                                </div>
                            @endif
                            @if($order->refund_no && $extra['refund_index_'.$index]['agree'] === true)
                                <div class="refund_no">
                                    退款订单号：{{ $order->refund_no }}
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
            @endif
        @endfor
    </ul>
@else
    <div class="callout callout-success">
        <h4>
            <i class="icon fa fa-check"></i>
            暂无退款记录
        </h4>
    </div>
@endif

<script>
    $(document).ready(function () {
        $('.btn-disagree-refund').on('click', function () {
            swal({
                title: '请输入拒绝退款原因？',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                focusCancel: true,
                inputValidator: (value) => {
                    return !value && '必须填写退款原因';
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('admin.orders.refund.handle', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({   // 将请求变成 JSON 字符串
                            agree: false,  // 拒绝申请
                            reason: result.value,
                            _token: LA.token,
                        }),
                        contentType: 'application/json',  // 请求的数据格式为 JSON
                        success: function () {  // 返回成功时会调用这个函数
                            swal({
                                title: '已拒绝该退款申请',
                                type: 'success'
                            }).then(function () {
                                // 用户点击 swal 上的 按钮时刷新页面
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        $('.btn-agree-refund').on('click', function () {
            swal({
                title: '确定要将款项退还给用户？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                focusCancel: true,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('admin.orders.refund.handle', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({   // 将请求变成 JSON 字符串
                            agree: true,  // 拒绝申请
                            _token: LA.token,
                        }),
                        contentType: 'application/json',  // 请求的数据格式为 JSON
                        success: function () {  // 返回成功时会调用这个函数
                            swal({
                                title: '已经将退款申请提交给支付平台接口',
                                type: 'success'
                            }).then(function () {
                                location.reload();
                            });
                        },
                        error: function (error) {
                            swal({
                                title: '支付平台服务器故障',
                                text: error.responseJSON.message,
                                type: 'error',
                            });
                        },
                    });
                }
            });
        });
    });
</script>