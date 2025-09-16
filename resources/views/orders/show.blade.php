@extends('layouts.app')
@section('title','Chi tiết đơn')

@section('content')
<h2 class="fw-bold text-white mb-3">🧾 Đơn #{{ $order->id }}</h2>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card bg-transparent border-0">
            <div class="card-body">
                <h5 class="text-white">Thông tin giao hàng</h5>
                <div>👤 {{ $order->shipping_name }}</div>
                <div>📞 {{ $order->shipping_phone }}</div>
                <div>📍 {{ $order->shipping_address }}</div>
                <div>💳 {{ strtoupper($order->payment_method) }}</div>
                <div>🧾 Trạng thái: <strong>{{ $order->status }}</strong></div>
                @if($order->note)
                    <div class="mt-2"><em>Ghi chú: {{ $order->note }}</em></div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card bg-transparent border-0">
            <div class="card-body">
                <h5 class="text-white">Sản phẩm</h5>
                <ul class="list-group">
                    @foreach($order->items as $it)
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                            <span>{{ $it->product->name ?? ('SP#'.$it->product_id) }} x {{ $it->quantity }}</span>
                            <span>{{ number_format($it->price * $it->quantity,0,',','.') }} đ</span>
                        </li>
                    @endforeach
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <strong>Tổng</strong>
                        <strong class="text-warning">{{ number_format($order->total_amount,0,',','.') }} đ</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
