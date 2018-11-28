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
                                                    @if(in_array($attr_value->symbol, explode(';', $item->product_sku->attributes)))
                                                        {{$attr_value->value}}
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
                isConfirm: true
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
    </script>
@endsection