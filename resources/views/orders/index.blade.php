@extends('layouts.app')
@section('title','Đơn hàng của tôi')

@section('content')
<h2 class="fw-bold text-white mb-3">📦 Lịch sử đơn hàng</h2>

@if($orders->isEmpty())
    <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
@else
<div class="table-responsive">
<table class="table table-hover align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Ngày</th>
            <th>Tổng</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $o)
        <tr>
            <td>{{ $o->id }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td class="fw-bold text-warning">{{ number_format($o->total_amount,0,',','.') }} đ</td>
            <td class="text-uppercase">{{ $o->payment_method }}</td>
            <td>{{ $o->status }}</td>
            <td>
                <a class="btn btn-info btn-sm" href="{{ route('orders.show',$o->id) }}">Xem</a>
                @if($o->payment_method === 'momo' && $o->status !== 'paid')
                    <a class="btn btn-success btn-sm" href="{{ route('orders.momo.pay',$o) }}">Thanh toán lại MoMo</a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $orders->links() }}
@endif
@endsection
