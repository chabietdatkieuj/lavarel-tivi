@extends('layouts.app')
@section('title','Thanh toán')

@section('content')
<h2 class="fw-bold text-white mb-3">🧾 Thanh toán</h2>

<div class="row g-3">
    <div class="col-lg-7">
        <form action="{{ route('checkout.store') }}" method="POST" class="card bg-transparent border-0">
            @csrf
            <div class="card-body p-3">
                <div class="mb-3">
                    <label class="form-label">Họ tên người nhận</label>
                    <input name="shipping_name" class="form-control bg-dark text-white border-0"
                           value="{{ old('shipping_name', auth()->user()->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input name="shipping_phone" class="form-control bg-dark text-white border-0"
                           value="{{ old('shipping_phone') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ giao hàng</label>
                    <input name="shipping_address" class="form-control bg-dark text-white border-0"
                           value="{{ old('shipping_address') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select bg-dark text-white border-0">
                        <option value="cod" selected>COD - Thanh toán khi nhận hàng</option>
                        <option value="momo">MoMo (thử nghiệm)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" rows="3" class="form-control bg-dark text-white border-0">{{ old('note') }}</textarea>
                </div>

                <button class="btn btn-gold btn-lg">Đặt hàng</button>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-light ms-2">Quay lại giỏ</a>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card bg-transparent border-0">
            <div class="card-body p-3">
                <h5 class="fw-bold text-white mb-3">🛒 Tóm tắt đơn</h5>
                <ul class="list-group">
                    @foreach($items as $it)
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                            <div>
                                <div class="fw-bold">{{ $it->product->name ?? 'Sản phẩm' }}</div>
                                <small>x{{ $it->quantity }} • {{ number_format($it->price,0,',','.') }} đ</small>
                            </div>
                            <div class="fw-bold">{{ number_format($it->quantity * $it->price,0,',','.') }} đ</div>
                        </li>
                    @endforeach
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                        <span class="fw-bold">Tổng</span>
                        <span class="fw-bold text-warning">{{ number_format($total,0,',','.') }} đ</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
