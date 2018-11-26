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
                                        @foreach($attr_values as $attr_value)
                                            @if($attr_value['attr_id'] == $attribute->id)
                                                @foreach($attr_value['items'] as $item)
                                                    <label class="btn btn-default sku-btn"
                                                           data-symbol="{{$item['symbol']}}" data-toggle="tooltip">
                                                        <div class="wrapper">
                                                            <input type="radio" name="skus" autocomplete="off"
                                                                   value="{{ $item['symbol'] }}"> {{ $item['value'] }}
                                                        </div>
                                                    </label>
                                                @endforeach
                                                @break
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="cart_amount">
                                <label>数量</label>
                                <input type="text" class="form-control input-sm" value="1"><span>件</span>
                                <div class="stock">库存 <span>0</span> 件</div>
                            </div>
                            <div class="buttons">
                                <button class="btn btn-success btn-favor">❤ 收藏</button>
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
            var selected = [],
                myStock = {},
                myPrice = {};

            // 所有属性值的symbol组成的数组
            var keys = JSON.parse('{!! $symbolArr !!}');

            // SKU数组
            var sku_items = JSON.parse('{!! $sku_items !!}');

            // skuItem 点击事件
            $("label.sku-btn").on('click', function (e) {
                if (!$(this).hasClass('disabled')) {
                    var btnGroup = $(this).parent('.attr-group');
                    var id = $(btnGroup).data('id');
                    if (!$(this).hasClass('active')) {
                        selected[id] = $(this).data('symbol');
                    } else {
                        // 防止bootstrap自动添加active
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        $(this).removeClass('active').removeClass('focus');
                        selected[id] = '';
                    }
                    console.log(selected);
                    // 先重置其他不可点击按钮
                    resetSkuItemsStatus();
                    changeStockAndPrice(selected);
                    checkSkuItemsStatus(selected);
                }
                else {
                    // 防止防止bootstrap的btn-group事件
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            });

            // 先创建缓存数组
            function init() {
                keys.forEach(function (key) {
                    key.forEach(function (item) {
                        getStock(item + "");
                    });
                });
            }

            // 数组转成用';'分割的字符串
            function arrToStr(array) {
                var attributes = '';
                for (var i = 0; i < keys.length; ++i) {
                    if (array[i]) {
                        attributes += (array[i] + ';');
                    }
                }
                return attributes.slice(0, attributes.length - 1);
            }

            function changeStockAndPrice(selected) {
                var attributes = arrToStr(selected);
                $('span.price-value').text(myPrice.hasOwnProperty(attributes) ? myPrice[attributes] : $('span.price-value').data('origin'));
                $('.stock > span').text(myStock.hasOwnProperty(attributes) ? myStock[attributes] : 0);
            }

            // 检查所有skuItem的状态是否可被点击
            function checkSkuItemsStatus(selected) {
                var i, j;
                for (i = 0; i < keys.length; ++i) {
                    var checking = selected.slice();
                    for (j = 0; j < keys[i].length; ++j) {
                        var item = keys[i][j];
                        if (item === checking[i])
                            continue;
                        checking[i] = item;
                        if (getStock(arrToStr(checking)) === 0) {
                            changeSkuItemsStatus(item);
                        }
                    }
                }
            }

            // 改变相应skuItem的状态为不可点击
            function changeSkuItemsStatus(symbol) {
                $('[data-symbol = ' + symbol + ']')
                    .removeClass('active')
                    .addClass('disabled');
            }

            // 复原skuItem状态
            function resetSkuItemsStatus() {
                $('label[data-symbol]').each(function () {
                    $(this).hasClass('disabled') ? $(this).removeClass('disabled') : '';
                });
            }

            // SKU商品筛选算法
            function getStock(key) {
                var result = 0,
                    i, j, m,
                    items, n = [];

                //检查是否已计算过
                if (typeof myStock[key] !== 'undefined') {
                    return myStock[key];
                }

                // 分割字符串
                items = key.split(";").filter(function (item) {
                    return item !== '';
                });

                //已选择数据是最小路径，直接从已端数据获取
                if (items.length === keys.length) {
                    if (sku_items[key]) {
                        myPrice[key] = sku_items[key].price;
                        myStock[key] = sku_items[key].stock;
                        return sku_items[key].stock;
                    }
                    else
                        return 0;
                }

                //拼接子串
                for (i = 0; i < keys.length; i++) {
                    for (j = 0; j < keys[i].length && items.length > 0; j++) {
                        if (keys[i][j] == items[0]) {
                            break;
                        }
                    }

                    if (j < keys[i].length && items.length > 0) {
                        //找到该项，跳过
                        n.push(items.shift());
                    } else {
                        //分解求值
                        for (m = 0; m < keys[i].length; m++) {
                            result += getStock(n.concat(keys[i][m], items).join(";"));
                        }
                        break;
                    }
                }

                //缓存
                if (result !== 0)
                    myStock[key] = result;
                return result;
            }

            // 运行初始化
            init();
        });
    </script>
@endsection