@extends('layouts.app')
@section('title', $product->title)

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body product-info">
                    <div class="row">
                        <div class="col-sm-5">
                            <img class="cover" src="{{ $product->image_url }}" alt="">
                        </div>
                        <div class="col-sm-7">
                            <div class="title">{{ $product->title }}</div>
                            <div class="price">
                                <label>价格</label>
                                <em>￥</em>
                                @if($product->price == $product->max_price)
                                    <span class="price-value" data-origin="{{ $product->price }}">
                                        {{ $product->price }}
                                    </span>
                                @else
                                    <span class="price-value"
                                          data-origin="{{ $product->price }}-{{ $product->max_price }}">
                                        {{ $product->price }}-{{$product->max_price}}
                                    </span>
                                @endif
                            </div>
                            <div class="sales_and_reviews">
                                <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
                                <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span>
                                </div>
                                <div class="rating" title="评分 {{ $product->rating }}">评分 <span
                                            class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span>
                                </div>
                            </div>
                            @foreach($product->skus_attributes as $key => $attribute)
                                <div class="skus">
                                    <label>{{$attribute->name}}</label>
                                    <div class="btn-group attr-group" data-id="{{$key}}" data-toggle="buttons">
                                        @foreach($attribute->attr_values()->orderBy('order')->get() as $attr_value)
                                            <label class="btn btn-default sku-btn"
                                                   data-symbol="{{ $attr_value->symbol }}" data-toggle="tooltip">
                                                <div class="wrapper">
                                                    <input type="radio" name="skus" autocomplete="off"
                                                           value="{{ $attr_value->symbol }}"> {{ $attr_value->value }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="cart_amount">
                                <label>数量</label>
                                <input type="text" class="form-control input-sm" value="1"><span>件</span>
                                <div class="stock">库存 <span>0</span> 件</div>
                            </div>
                            <div class="buttons product-actions">
                                @if($favored)
                                    <button class="btn btn-danger btn-disfavor">取消收藏</button>
                                @else
                                    <button class="btn btn-success btn-favor">❤ 收藏</button>
                                @endif
                                <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                            </div>
                        </div>
                    </div>
                    <div class="product-detail">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#product-detail-tab" aria-controls="product-detail-tab" role="tab"
                                   data-toggle="tab">商品详情</a>
                            </li>
                            <li role="presentation">
                                <a href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab"
                                   data-toggle="tab">用户评价</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                                {!! $product->description !!}
                            </div>
                            <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td>用户</td>
                                        <td>商品</td>
                                        <td>评分</td>
                                        <td>评价</td>
                                        <td>时间</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{ $review->order->user->name }}</td>
                                            <td>{{ $review->product_sku->title }}</td>
                                            <td class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</td>
                                            <td>{{ $review->review }}</td>
                                            <td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            // myData用于缓存
            let selected = [],
                myData = {};

            // 所有属性值的symbol组成的对象
            let keys = JSON.parse('{!! $symbolArr !!}');

            // console.log(keys);

            // skuItems对象
            let sku_items = JSON.parse('{!! $sku_items !!}');

            // console.log(sku_items);

            // 加入购物车按钮点击事件
            $('.btn-add-to-cart').on('click', function () {
                let selectedStr = selected.join(";");
                // 判断用户是否完整选择了商品信息
                if (selected.length === keys.length
                    &&
                    sku_items.hasOwnProperty(selectedStr)
                ) {
                    axios.post('{{route('cart.add')}}',
                        {
                            'sku_id': sku_items[selectedStr]['sku_id'],
                            'amount': parseInt($('.cart_amount input').val(), 10),
                        }
                    ).then(function () {
                        swal('加入购物车成功', '', 'success')
                            .then(function () {
                                location.href = '{{route('cart.index')}}';
                            })
                    }, function (error) {
                        if (error.response.status === 401) {
                            swal('请先登录', '', 'error')
                                .then(function () {
                                    window.location.href = '{{route('login')}}';
                                });
                        } else if (error.response.status === 400) {
                            swal(error.response.data.msg, '', 'error');
                        }
                        // '422'代表表单校验错误
                        else if (error.response.status === 422) {
                            let html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error + '<br/>';
                                });
                            });
                            html += '</div>';
                            swal({
                                html: html,
                                type: 'error',
                            });
                        } else {
                            swal('服务器错误', error.response.data.msg, 'error');
                        }
                    });
                } else {
                    swal('请选择您要的商品信息', '', 'info');
                }
            });

            // 收藏按钮点击事件
            $(document).on('click', '.btn-favor', function () {
                axios.post('{{route('products.favor', ['product' => $product->id])}}')
                    .then(function () {
                        swal('收藏成功', '', 'success')
                            .then(function () {
                                let actions = $('.product-actions');
                                actions.children('.btn-favor').remove();
                                actions.prepend('<button class="btn btn-danger btn-disfavor">取消收藏</button>');

                            });
                    }, function (error) {
                        if (error.response && error.response.status === 401) {
                            swal('请先登录', '', 'error')
                                .then(function () {
                                    window.location.href = '{{route('login')}}';
                                });
                        } else if (error.response && error.response.data.msg) {
                            swal(error.response.data.msg, '', 'error');
                        } else {
                            swal('服务器错误', '', 'error');
                        }
                    })
            });

            // 取消收藏点击事件
            $(document).on('click', '.btn-disfavor', function () {
                axios.delete('{{route('products.disfavor', ['product' => $product->id])}}')
                    .then(function () {
                        swal('取消收藏成功', '', 'success')
                            .then(function () {
                                let actions = $('.product-actions')
                                actions.children('.btn-disfavor').remove();
                                actions.prepend('<button class="btn btn-success btn-favor">❤ 收藏</button>');
                            })
                    })
            });

            // 每个skuItem的点击事件
            $("label.sku-btn").on('click', function (e) {
                selected = [];
                if (!$(this).hasClass('disabled')) {
                    // 阻止bootstrap的默认方法
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    $(this).toggleClass("active focus").siblings().removeClass("active focus");
                    $("div.attr-group").each(function (index, el) {
                        let value = $(el).children(".active").data("symbol");
                        selected.push(value || "");
                    });
                    console.log(selected);
                    checkSkuItemsStatus(selected);
                }
            });

            // 改变库存数与售价
            function changeStockAndPrice(selected) {
                let key = selected.join(";");
                _sku = getSingleSkuMessages(key);
                $('span.price-value').text(_sku.maxPrice > _sku.minPrice ? (_sku.minPrice + "-" + _sku.maxPrice) : _sku.maxPrice);
                $('.stock > span').text(_sku.stock);
            }

            // 检查所有skuItem的状态是否可被点击
            function checkSkuItemsStatus(selected) {
                let i, j;
                for (i = 0; i < keys.length; ++i) {
                    let checking = selected.slice();
                    for (j = 0; j < keys[i].length; ++j) {
                        if (checking[i] === keys[i][j]) continue;
                        checking[i] = keys[i][j];
                        if (getSingleSkuMessages(checking.join(";")).stock === 0)
                            changeSkuItemStatus(checking[i], true);
                        else changeSkuItemStatus(checking[i], false);
                    }
                }
                changeStockAndPrice(selected);
            }

            // 改变相应skuItem的状态为不可点击
            function changeSkuItemStatus(symbol, toDisabled) {
                if (toDisabled)
                    $('[data-symbol = ' + symbol + ']')
                        .addClass('disabled');
                else
                    $('[data-symbol = ' + symbol + ']')
                        .removeClass('disabled')
            }

            // 获取特定SKU商品的库存以及价格
            function getSingleSkuMessages(key) {
                let single_sku = {
                    stock: 0,
                    maxPrice: -1.00,
                    minPrice: Number.MAX_SAFE_INTEGER.toFixed(2)
                };

                let i, j, m,
                    items, n = [];

                //检查是否已计算过
                if (typeof myData[key] !== 'undefined') {
                    return myData[key];
                }

                // 分割字符串
                items = key.split(";")
                    .filter(function (item) {
                        return item !== "";
                    }).map(function (item) {
                        return parseInt(item);
                    });

                //已选择数据是最小路径，直接从已端数据获取
                if (items.length === keys.length) {
                    if (sku_items.hasOwnProperty(key)) {
                        single_sku.stock = sku_items[key].stock > 0 ? sku_items[key].stock : 0;
                        single_sku.maxPrice = single_sku.minPrice = parseFloat(sku_items[key].price).toFixed(2);
                    }
                    return single_sku;
                }

                //拼接子串
                for (i = 0; i < keys.length; ++i) {
                    for (j = 0; j < keys[i].length && items.length > 0; ++j) {
                        if (keys[i][j] === items[0]) break;
                    }

                    if (j < keys[i].length && items.length > 0) {
                        //找到该项，跳过
                        n.push(items.shift());
                    } else {
                        //分解求值
                        for (m = 0; m < keys[i].length; ++m) {
                            result = getSingleSkuMessages(n.concat(keys[i][m], items).join(";"));
                            single_sku.stock += result.stock;
                            if (result.maxPrice > single_sku.maxPrice) single_sku.maxPrice = result.maxPrice;
                            if (result.minPrice < single_sku.minPrice) single_sku.minPrice = result.minPrice;
                        }
                        break;
                    }
                }

                //缓存
                if (single_sku.stock !== 0) {
                    myData[key] = single_sku;
                    console.log(myData);
                }
                return single_sku;
            }

            // 初始化缓存，并更新skuBtn状态以及sku的库存和售价
            function init() {
                console.log("初始化SKU缓存数组以及库存和价格");
                let initArray = [];
                keys.forEach(function () {
                    initArray.push("");
                });
                if (initArray.length === keys.length) {
                    checkSkuItemsStatus(initArray);
                }
            }

            // 运行初始化
            init();
        });
    </script>
@endsection