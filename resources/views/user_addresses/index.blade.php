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
                    type: 'warning',
                    title: '确认删除该地址？',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    showLoaderOnConfirm: true,
                    focusCancel: true,
                    preConfirm: () => {
                        return axios.delete('/user_addresses/destroy/' + id)
                            .then((res) => {
                                return res.data;
                            })
                            .catch((error) => {
                                swal({
                                    type: error,
                                    title: '未知错误：删除失败'
                                })
                            })
                    },
                    allowOutsideClick: () => !swal.isLoading(),
                }).then(function (result) {
                    if (result.value) {
                        var data = result.value;
                        if (parseInt(data.status, 10) === 1) {
                            swal({
                                title: data.msg,
                                type: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(that).parents('tr').remove();
                        }
                        else {
                            swal({
                                type: error,
                                title: data.msg,
                            });
                        }
                    }
                })
            })
        })
    </script>
@endsection