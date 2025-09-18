{{-- resources/views/checkout/create.blade.php --}}
@extends('layouts.app')
@section('title','Thanh toán')

@push('styles')
<style>
  .checkout-title{ font-weight:800; color:var(--text-900); }

  .checkout-card, .summary-card{
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: var(--shadow-1);
  }

  .form-control, .form-select, textarea{
    background: var(--surface);
    color: var(--text-900);
    border: 1px solid var(--border);
    border-radius: 10px;
  }
  .form-control:focus, .form-select:focus, textarea:focus{
    border-color: var(--primary-600);
    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
  }

  .list-group-item{
    background: var(--surface);
    color: var(--text-900);
    border-color: var(--border);
  }
  .list-group-item:nth-child(even){ background:#fafafa; }

  .coupon-wrap{
    background:#f9fafb; border:1px dashed var(--border);
    border-radius:10px; padding:12px;
  }
  .coupon-input{ text-transform:uppercase; letter-spacing:.5px }
  .hint{ color:var(--text-600); font-size:.9rem }
</style>
@endpush

@section('content')
<h2 class="checkout-title mb-3">🧾 Thanh toán</h2>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<div class="row g-3">
  <div class="col-lg-7">
    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" class="checkout-card" novalidate>
      @csrf
      <div class="card-body p-3">
        <div class="mb-3">
          <label class="form-label">Họ tên người nhận</label>
          <input type="text" name="shipping_name" class="form-control"
                 value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Số điện thoại</label>
          <input type="tel" name="shipping_phone" class="form-control"
                 value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Địa chỉ giao hàng</label>
          <input type="text" name="shipping_address" class="form-control"
                 value="{{ old('shipping_address') }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Phương thức thanh toán</label>
          <select name="payment_method" class="form-select">
            <option value="cod" {{ old('payment_method','cod')==='cod' ? 'selected' : '' }}>
              COD - Thanh toán khi nhận hàng
            </option>
            <option value="momo" {{ old('payment_method')==='momo' ? 'selected' : '' }}>
              MoMo (thử nghiệm)
            </option>
          </select>
        </div>

        {{-- ✅ Mã giảm giá: khớp OrderController => voucher_code --}}
        <div class="mb-3">
          <label class="form-label">Mã giảm giá (nếu có)</label>
          <div class="coupon-wrap">
            <input type="text" name="voucher_code"
                   class="form-control coupon-input"
                   placeholder="NHẬP MÃ (VD: TVSALE10)"
                   value="{{ old('voucher_code') }}">
            <div class="hint mt-2">
              Mã sẽ được kiểm tra & trừ thẳng vào tổng tiền khi bạn bấm <strong>Đặt hàng</strong>.
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Ghi chú</label>
          <textarea name="note" rows="3" class="form-control">{{ old('note') }}</textarea>
        </div>

        <button id="submitBtn" class="btn btn-gold btn-lg">Đặt hàng</button>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary ms-2">Quay lại giỏ</a>
      </div>
    </form>
  </div>

  <div class="col-lg-5">
    <div class="summary-card">
      <div class="card-body p-3">
        <h5 class="fw-bold mb-3">🛒 Tóm tắt đơn</h5>
        <ul class="list-group list-group-flush">
          @foreach($items as $it)
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <div class="fw-semibold">{{ $it->product->name ?? 'Sản phẩm' }}</div>
                <small>x{{ $it->quantity }} • {{ number_format($it->price,0,',','.') }} đ</small>
              </div>
              <div class="fw-bold">
                {{ number_format($it->quantity * $it->price,0,',','.') }} đ
              </div>
            </li>
          @endforeach

          <li class="list-group-item d-flex justify-content-between">
            <span class="fw-bold">Tạm tính</span>
            <span class="fw-bold">{{ number_format($total,0,',','.') }} đ</span>
          </li>

          <li class="list-group-item">
            <small class="hint">
              Giảm giá (nếu mã hợp lệ) sẽ được trừ ở bước tạo đơn.
            </small>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Uppercase mã tự động
  (function(){
    const code = document.querySelector('input[name="voucher_code"]');
    if(code){
      code.addEventListener('input', () => code.value = code.value.toUpperCase());
    }
  })();

  // Chặn submit 2 lần
  (function(){
    const form = document.getElementById('checkoutForm');
    const btn  = document.getElementById('submitBtn');
    if(form && btn){
      form.addEventListener('submit', function(){
        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';
      });
    }
  })();
</script>
@endpush
@endsection
