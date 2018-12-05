@extends('layouts.app')

@section('title', '购物车')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">我的购物车</div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>商品信息</th>
                            <th></th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="product_list">
                        @foreach($cartItems as $item)
                            <tr data-id="{{ $item->product_sku->id }}">
                                <td>
                                    <input type="checkbox" name="select"
                                           value="{{ $item->product_sku->id }}" {{ $item->product_sku->product->on_sale ? 'checked' : 'disabled' }}>
                                </td>
                                <td class="product_info">
                                    <div class="preview">
                                        <a target="_blank"
                                           href="{{ route('products.show', [$item->product_sku->product_id]) }}">
                                            <img src="{{ $item->product_sku->product->image_url }}">
                                        </a>
                                    </div>
                                    <div @if(!$item->product_sku->product->on_sale) class="not_on_sale" @endif>
                                    <span class="product_title">
                                        <a target="_blank"
                                           href="{{ route('products.show', [$item->product_sku->product_id]) }}">{{ $item->product_sku->product->title }}</a>
                                        </span>
                                        <span class="sku_title">{{ $item->product_sku->title }}</span>
                                        @if(!$item->product_sku->product->on_sale)
                                            <span class="warning">该商品已下架</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @foreach($item->product_sku->product->skus_attributes as $attribute)
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
                                </td>
                                <td>
                                    <span class="price">￥{{ $item->product_sku->price }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control input-sm amount"
                                           data-id='{{ $item->product_sku->id }}'
                                           @if(!$item->product_sku->product->on_sale) disabled @endif name="amount"
                                           value="{{ $item->amount }}">
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-danger btn-remove">移除</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--订单额外信息--}}
                    <div>
                        <form class="form-horizontal" role="form" id="order-form">
                            <div class="form-group">
                                <label class="control-label col-sm-3">选择收货地址</label>
                                <div class="col-sm-9 col-md-7">
                                    <select class="form-control" name="address">
                                        @foreach($addresses as $address)
                                            <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">备注</label>
                                <div class="col-sm-9 col-md-7">
                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">优惠码</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="coupon_code">
                                    <span class="help-block" id="coupon_desc"></span>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-success" id="btn-check-coupon">检查</button>
                                    <button type="button" class="btn btn-danger" style="display: none;"
                                            id="btn-cancel-coupon">取消
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-3">
                                    <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        // 删除购物车按钮点击事件
        $('.btn-remove').on('click', function () {
            let id = $(this).closest('tr').data('id');
            let that = $(this);
            swal({
                title: '确定从购物车中删除此商品吗？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(function (result) {
                if (result.value) {
                    axios.delete('/cart/' + id)
                        .then(function () {
                            swal('删除成功', '', 'success');
                            that.closest('tr').remove();
                        }, function (error) {
                            swal('服务器错误', '', 'error');
                        });
                }
            });
        });

        // 全选事件
        $('#select-all').on('change', function () {
            // 让下面的所有勾选框和总的勾选框联动
            let checked = $(this).prop('checked');
            // 此处可用:not([disabled])  也可以用:enabled
            $('input[name=select][type=checkbox]:enabled').each(function () {
                $(this).prop('checked', checked);
            });
        });

        // 提交订单事件
        $('.btn-create-order').on('click', function () {
            let orders = $('input[name=select]:checked');
            let address = $('#order-form').find('select[name=address]');

            // 前端校验
            if (orders.length === 0) {
                swal('错误', '请先勾选需要下单商品', 'error');
                return;
            }
            if (!address.val()) {
                swal('错误', '请勾选收货地址', 'error');
                return;
            }

            let data = {
                'address_id': address.val(),
                'remark': $('#order-form').find('textarea[name=remark]').val(),
                'items': [],
                'coupon_code': $('input[name=coupon_code]').val(),  // 获取优惠码
            };
            _.each(orders, function (order) {
                let sku_id = $(order).val();
                let amount = $('input.amount[data-id=' + sku_id + ']').val();
                data.items.push({
                    'sku_id': sku_id,
                    'amount': amount,
                });
            });
            axios.post('{{route('orders.store')}}', data)
                .then(function (res) {
                    swal('订单提交成功', '', 'success')
                        .then(function () {
                            location.href = '{{route('orders.index')}}';
                        });
                }, function (error) {
                    if (error.response.status === 422) {
                        let html = '<div>';
                        _.each(error.response.data.errors, function (errors) {
                            _.each(errors, function (error) {
                                html += error + '<br>';
                            })
                        });
                        html += '</div>';
                        swal({title: $(html)[0], type: 'error'});
                    } else if (error.response.status === 403) {
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        swal('服务器错误', '', 'error');
                    }
                });
        });

        // 检查优惠码
        $('#btn-check-coupon').on('click', function () {
            let code = $('input[name=coupon_code]').val();
            if (!code) {
                swal('请输入优惠码', '', 'warning');
                return;
            }

            axios.post('{{ route('coupon.verify') }}', {code: code})
                .then((res) => {
                    $('input[name=coupon_code]').prop('readonly', true);
                    $('#coupon_desc').text(res.data.coupon_rule); // 输出优惠信息
                    $('#btn-cancel-coupon').show();
                    $('#btn-check-coupon').hide();
                }, (error) => {
                    $('input[name=coupon_code]').val('');
                    if (error.response.status === 404) {
                        swal('该优惠码不存在', '', 'error');
                    } else if (error.response.status === 403) {
                        swal(error.response.data.msg, '', 'error');
                    } else {
                        swal('服务器错误', '', 'error');
                        s
                    }
                });
        });

        $('#btn-cancel-coupon').on('click', function () {
            $('input[name=coupon_code]').prop('readonly', false).val('');
            $('#coupon_desc').text(''); // 输出优惠信息
            $('#btn-cancel-coupon').hide();
            $('#btn-check-coupon').show();
        });
    </script>
@endsection