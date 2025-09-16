@extends('layouts.app')
@section('title','Quản lý đơn hàng')

@section('content')
<h2 class="fw-bold text-white mb-3">📦 Quản lý đơn hàng</h2>

@if($orders->isEmpty())
    <div class="alert alert-info">Chưa có đơn hàng nào.</div>
@else
<div class="table-responsive">
<table class="table table-hover align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $o)
        <tr>
            <td>{{ $o->id }}</td>
            <td>{{ $o->user->name ?? 'N/A' }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td class="fw-bold text-warning">{{ number_format($o->total_amount,0,',','.') }} đ</td>
            <td class="text-uppercase">{{ $o->payment_method }}</td>
            <td>{{ $o->status }}</td>
            <td>
                <a href="{{ route('admin.orders.show',$o->id) }}" class="btn btn-info btn-sm">Xem</a>
                <form action="{{ route('admin.orders.destroy',$o->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endif
@endsection