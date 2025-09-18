@extends('layouts.app')
@section('title','Đơn #'.$order->id)

@push('styles')
<style>
  .orders-title{ font-weight:800; color:var(--text-900); }
  .pill{ border-radius:999px; padding:.2rem .55rem; font-weight:700; font-size:.8rem; }
  .stt-pending   { background:#fde68a; color:#78350f; }
  .stt-processing{ background:#bfdbfe; color:#1e3a8a; }
  .stt-shipping  { background:#bbf7d0; color:#065f46; }
  .stt-delivered { background:#86efac; color:#14532d; }
  .stt-cancelled { background:#fecaca; color:#7f1d1d; }
  .stt-failed    { background:#fecaca; color:#7f1d1d; }
  .stt-paid      { background:#d1fae5; color:#065f46; }
  .stt-unpaid    { background:#fee2e2; color:#7f1d1d; }

  .card-lite{
    background:var(--surface); border:1px solid var(--border);
    border-radius:14px; padding:16px; color:var(--text-900);
  }

  .orders-table thead th{
    background:#f9fafb; color:var(--text-900); border-bottom:1px solid var(--border);
    text-transform:uppercase; font-weight:700; letter-spacing:.3px;
  }
  .orders-table tbody td{
    background:var(--surface); color:var(--text-900); vertical-align:middle!important;
    border-color:var(--border);
  }
  .price{ font-weight:800; color:#b45309; }
  .muted{ color:var(--text-600); }

  .thumb{ width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid var(--border); background:#f3f4f6 }
  .btn-gold{ background:linear-gradient(90deg,#fcd34d,#f59e0b); border:none; color:#111827; font-weight:800; }

  /* Hiển thị mã giảm giá */
  .voucher-tag{
    display:inline-block; font-weight:800; font-size:.8rem;
    color:#065f46; background:#d1fae5; border:1px dashed #10b981;
    padding:.15rem .5rem; border-radius:8px; margin-left:.35rem;
  }
</style>
@endpush

@section('content')
<h2 class="orders-title mb-3">🧾 Chi tiết đơn #{{ $order->id }}</h2>

@if(session('success'))  <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))    <div class="alert alert-danger">{{ session('error') }}</div>   @endif
@if(session('info'))     <div class="alert alert-info">{{ session('info') }}</div>      @endif

@php
  $map = [
    'pending'    => 'stt-pending',
    'processing' => 'stt-processing',
    'shipping'   => 'stt-shipping',
    'delivered'  => 'stt-delivered',
    'cancelled'  => 'stt-cancelled',
    'failed'     => 'stt-failed',
    'paid'       => 'stt-paid',
    'unpaid'     => 'stt-unpaid',
  ];
  $cls = $map[$order->status] ?? 'stt-processing';
@endphp

<div class="row g-3">
  {{-- Thông tin giao hàng --}}
  <div class="col-lg-6">
    <div class="card-lite h-100">
      <div class="d-flex justify-content-between align-items-start">
        <h5 class="mb-2">📦 Thông tin giao hàng</h5>
        <span class="pill {{ $cls }}">{{ $order->status }}</span>
      </div>
      <div class="mb-1">👤 <strong>{{ $order->shipping_name }}</strong></div>
      <div class="mb-1">📞 {{ $order->shipping_phone }}</div>
      <div class="mb-1">📍 {{ $order->shipping_address }}</div>
      <div class="mb-1">💳 {{ strtoupper($order->payment_method) }}</div>
      <div class="muted">🗓 Đặt lúc: {{ $order->created_at->format('d/m/Y H:i') }}</div>

      @if(!empty($order->voucher_code))
        <div class="mt-2">
          🔖 Đã áp dụng: <span class="voucher-tag">{{ $order->voucher_code }}</span>
        </div>
      @endif

      @if($order->note)
        <hr>
        <div><span class="muted">Ghi chú:</span> {{ $order->note }}</div>
      @endif
    </div>
  </div>

  {{-- Tóm tắt & hành động thanh toán --}}
  <div class="col-lg-6">
    <div class="card-lite h-100">
      <h5 class="mb-2">💰 Tóm tắt thanh toán</h5>

      @php
        // Tính tạm tính từ items
        $subtotal = (float) $order->items->sum(fn($i) => (float)$i->price * (int)$i->quantity);
        $shipFee  = 0.0;

        // Lấy giảm giá: ưu tiên cột discount_amount nếu có, nếu không thì tính từ (subtotal + ship) - total_amount
        $discountCol = isset($order->discount_amount) ? (float)$order->discount_amount : null;
        $calcDiscount = $discountCol !== null
                        ? max(0.0, (float)$discountCol)
                        : max(0.0, ($subtotal + $shipFee) - (float)$order->total_amount);

        $voucherCode = $order->voucher_code ?? null;
        $percentOff  = $subtotal > 0 ? (int) round(($calcDiscount / $subtotal) * 100) : 0;
      @endphp

      <div class="d-flex justify-content-between">
        <span>Tạm tính</span>
        <span>{{ number_format($subtotal, 0, ',', '.') }} đ</span>
      </div>
      <div class="d-flex justify-content-between">
        <span>Phí vận chuyển</span>
        <span>{{ number_format($shipFee, 0, ',', '.') }} đ</span>
      </div>

      @if($calcDiscount > 0)
        <div class="d-flex justify-content-between mt-1">
          <span>
            Giảm giá
            @if(!empty($voucherCode))
              <span class="voucher-tag">Mã: {{ $voucherCode }}</span>
            @endif
            @if($percentOff > 0)
              <small class="muted">({{ $percentOff }}%)</small>
            @endif
          </span>
          <span>-{{ number_format($calcDiscount, 0, ',', '.') }} đ</span>
        </div>
      @endif

      <hr class="my-2">
      <div class="d-flex justify-content-between">
        <strong>Tổng</strong>
        <strong class="price">{{ number_format($order->total_amount, 0, ',', '.') }} đ</strong>
      </div>

      {{-- Thanh toán lại MoMo nếu cần --}}
      @if($order->payment_method === 'momo' && $order->user_id === auth()->id() && $order->status !== 'paid')
        <div class="mt-3">
          <a class="btn btn-success" href="{{ route('orders.momo.pay',$order) }}">Thanh toán lại MoMo</a>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Danh sách sản phẩm --}}
<div class="card-lite mt-3">
  <h5 class="mb-2">🧺 Sản phẩm trong đơn</h5>

  <div class="table-responsive">
    <table class="table orders-table table-hover align-middle">
      <thead>
        <tr>
          <th width="80">Ảnh</th>
          <th class="text-start">Sản phẩm</th>
          <th width="120">Đơn giá</th>
          <th width="90">SL</th>
          <th width="140">Thành tiền</th>
          <th width="260">Hành động</th>
        </tr>
      </thead>
      <tbody>
      @foreach($order->items as $it)
        @php
          $img = $it->product?->image ? asset('storage/'.$it->product->image) : 'https://via.placeholder.com/80x80?text=TV';
          $rev = \App\Models\Review::where([
            'order_id'   => $order->id,
            'product_id' => $it->product_id,
            'user_id'    => auth()->id(),
          ])->withCount('replies')->first();
        @endphp
        <tr>
          <td><img class="thumb" src="{{ $img }}" alt=""></td>
          <td class="text-start">
            <a href="{{ route('products.show', $it->product_id) }}" class="text-decoration-none">
              {{ $it->product->name ?? ('SP #'.$it->product_id) }}
            </a>
          </td>
          <td>{{ number_format($it->price,0,',','.') }} đ</td>
          <td>x {{ $it->quantity }}</td>
          <td class="price">{{ number_format($it->price * $it->quantity,0,',','.') }} đ</td>
          <td>
            @if($order->status === 'delivered')
              @if(!$rev)
                <a class="btn btn-gold btn-sm"
                   href="{{ route('reviews.create', ['order'=>$order->id,'product'=>$it->product_id]) }}">
                  ✍️ Đánh giá
                </a>
              @else
                <a class="btn btn-gold btn-sm" href="{{ route('reviews.edit', $rev->id) }}">✏️ Sửa đánh giá</a>
                <a class="btn btn-outline-secondary btn-sm"
                   href="{{ route('products.show', $it->product_id) }}#reviews">
                  💬 Xem phản hồi @if($rev->replies_count>0) ({{ $rev->replies_count }}) @endif
                </a>
              @endif
            @else
              <span class="muted">Đợi đơn “delivered” để đánh giá</span>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-between mt-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">← Quay lại lịch sử đơn</a>
    <div class="muted">Mã đơn: #{{ $order->id }}</div>
  </div>
</div>
@endsection
