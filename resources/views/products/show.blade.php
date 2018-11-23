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
                            <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
                            <div class="sales_and_reviews">
                                <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
                                <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span>
                                </div>
                                <div class="rating" title="评分 {{ $product->rating }}">评分 <span
                                            class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span>
                                </div>
                            </div>
                            @foreach($product->skus_attributes as $attribute)
                                <div class="skus">
                                    <label>{{$attribute->name}}</label>
                                    <div class="btn-group" data-toggle="buttons">
                                        @foreach($product->skus as $sku)
                                            @foreach($sku->attributes as $item)
                                                @if($item['id'] == $attribute->id)
                                                    <label class="btn btn-default sku-btn" data-toggle="tooltip"
                                                           title="{{ $sku->description }}">
                                                        <div class="wrapper">
                                                            <input type="radio" name="skus" autocomplete="off"
                                                                   value="{{ $sku->id }}"> {{ $item['value'] }}
                                                        </div>
                                                    </label>
                                                    @break
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="cart_amount">
                                <label>数量</label>
                                <input type="text" class="form-control input-sm" value="1"><span>件</span>
                                <div class="stock">库存 <span>0</span> 件</div></div>
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
            // $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
        });
    </script>
@endsection