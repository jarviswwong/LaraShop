<div class="row">
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header">
                <div class="btn-group">
                    <a class="btn btn-success btn-sm btn-saving">保存</a>
                </div>
                <div class="btn-group">
                    <a class="btn btn-primary btn-sm btn-reload">刷新</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="dd attr-value-nestable">
                    <ol class="dd-list">
                        @foreach($product->skus_attributes as $sku_attr)
                            <li class="dd-item" data-id="{{$sku_attr->id}}">
                                <div class="dd-handle" style="font-weight: 600;">
                                    {{ $sku_attr->name }}
                                    &nbsp;&nbsp;&nbsp;
                                    id:{{ $sku_attr->id }}
                                </div>
                                <ol class="dd-list">
                                    @foreach($sku_attr->attr_values()->orderBy('order')->get() as $attr_value)
                                        <li class="dd-item" data-symbol="{{ $attr_value->symbol }}">
                                            <div class="dd-handle">
                                                {{ $attr_value->value }}
                                                &nbsp;&nbsp;&nbsp;
                                                symbol:{{ $attr_value->symbol }}
                                                &nbsp;&nbsp;&nbsp;
                                                order:{{ $attr_value->order }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header">
                <h4>新增商品属性</h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('admin.productAttrValues.create') }}"
                  accept-charset="UTF-8" pjax-container="1">
                {{ csrf_field() }}
                <div class="box-body">
                    <input name="product_id" value="{{ $product->id }}" style="display: none;"/>
                    <div class="form-group @if ($errors->has('attr_id')) has-error @endif">
                        <label class="col-sm-2 control-label">父属性</label>
                        <div class="col-sm-10">
                            @include('admin.form.error', ['errorKey' => 'attr_id'])
                            <select name="attr_id" id="attribute_selection" style="width: 100%;">
                                @foreach($product->skus_attributes as $sku_attr)
                                    <option value="{{ $sku_attr->id }}">{{ $sku_attr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group @if ($errors->has('value')) has-error @endif">
                        <label class="col-sm-2 control-label">属性值</label>
                        <div class="col-sm-10">
                            @include('admin.form.error', ['errorKey' => 'value'])
                            <input type="text" id="input-value" name="value" class="form-control" placeholder="请输入value"
                                   autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group @if ($errors->has('order')) has-error @endif">
                        <label class="col-sm-2 control-label">排序</label>
                        <div class="col-sm-10">
                            @include('admin.form.error', ['errorKey' => 'order'])
                            <input type="text" id="input-order" name="order" class="form-control" placeholder="请输入order"
                                   autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <div class="form-group pull-right">
                            <button type="submit" class="btn btn-info btn-submit">提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.attr-value-nestable').nestable({});
        $('#attribute_selection').select2({});

        $('.btn-saving').on('click', function () {
            let $that = $(this).button('loading');
            let data = $('.attr-value-nestable').nestable('serialize');
            $.post('{{ route('admin.productAttrValues.changeOrder', ['product' => $product->id]) }}',
                {
                    _token: LA.token,
                    _order: JSON.stringify(data),
                },
                function (data) {
                    $that.button('reset');
                    $.pjax.reload('#pjax-container');
                    toastr.success('商品属性顺序保存成功');
                });
        });

        $('.btn-reload').on('click', function () {
            $.pjax.reload('#pjax-container');
            toastr.success('刷新成功');
        });
    });
</script>
