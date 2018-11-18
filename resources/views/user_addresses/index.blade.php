@extends('layouts.app')
@section('title', '收货地址列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    收货地址列表
                    <a href="{{ route('user_addresses.create') }}" class="pull-right">新增收货地址</a>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>收货人</th>
                            <th>地址</th>
                            <th>邮编</th>
                            <th>电话</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($addresses as $address)
                            <tr>
                                <td>{{ $address->contact_name }}</td>
                                <td>{{ $address->full_address }}</td>
                                <td>{{ $address->zip }}</td>
                                <td>{{ $address->contact_phone }}</td>
                                <td>
                                    <a href="{{ route('user_addresses.edit', ['userAddress' => $address->id]) }}"
                                       class="btn btn-primary">修改</a>
                                    {{--<form action="{{route('user_addresses.destroy', ['userAddress' => $address->id])}}"--}}
                                    {{--method="POST" style="display: inline-block;">--}}
                                    {{--{{csrf_field()}}--}}
                                    {{--{{method_field('DELETE')}}--}}
                                    {{--<button class="btn btn-danger" tyle="submit">删除</button>--}}
                                    {{--</form>--}}
                                    <button class="btn btn-danger btn-delete-address" data-id="{{$address->id}}">删除
                                    </button>
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
        $(document).ready(function () {
            $('.btn-delete-address').on('click', function () {
                var id = $(this).data('id');
                var that = this;

                swal({
                    title: "确认删除该地址？",
                    icon: "warning",
                    buttons: ['取消', '确定'],
                    dangerMode: true,
                }).then(function (willDelete) {
                    if (willDelete) {
                        axios.delete('/user_addresses/destroy/' + id)
                            .then((res) => {
                                if (parseInt(res.data.status, 10) === 1) {
                                    swal(res.data.msg, {
                                        icon: "success",
                                    });
                                    $(that).parents('tr').remove();
                                }
                                else {
                                    swal(res.data.msg, {
                                        icon: "error",
                                    });
                                }
                            })
                    }
                    else {
                        return;
                    }
                })
            })
        })
    </script>
@endsection