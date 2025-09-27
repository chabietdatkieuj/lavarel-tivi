@extends('layouts.app')
@section('title', 'Chi tiết đơn #'.$order->id)

@push('styles')
<style>
  .page-title{font-weight:800;color:var(--text-900)}
  .card-lite{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px;color:var(--text-900)}
  .pill{border-radius:999px;padding:.2rem .6rem;font-weight:800;font-size:.8rem}
  .stt-pending{background:#fde68a;color:#78350f}
  .stt-processing{background:#bfdbfe;color:#1e3a8a}
  .stt-shipping{background:#bbf7d0;color:#065f46}
  .stt-delivered{background:#86efac;color:#14532d}
  .stt-cancelled{background:#fecaca;color:#7f1d1d}
  .stt-paid{background:#d1fae5;color:#065f46}
  .stt-unpaid{background:#fee2e2;color:#7f1d1d}
  .stt-failed{background:#ffe4e6;color:#9f1239}
  .table thead th{background:#f9fafb;color:var(--text-900);border-bottom:1px solid var(--border);text-transform:uppercase;font-weight:700;letter-spacing:.25px}
  .table tbody td{background:var(--surface);color:var(--text-900);border-color:var(--border)}
  .price{color:#b45309;font-weight:800}
  .muted{color:var(--text-600)}
</style>
@endpush

@section('content')
@php
  $classMap = [
    'pending'=>'stt-pending','processing'=>'stt-processing','shipping'=>'stt-shipping',
    'delivered'=>'stt-delivered','cancelled'=>'stt-cancelled','paid'=>'stt-paid',
    'unpaid'=>'stt-unpaid','failed'=>'stt-failed',
  ];
  $pill = $classMap[$order->status] ?? 'stt-processing';
  $subtotal = $order->items->sum(fn($i)=>$i->price*$i->quantity);
@endphp

<h2 class="page-title mb-3">🧾 Chi tiết đơn #{{ $order->id }}</h2>



<div class="row g-3">
  {{-- Thông tin giao hàng --}}
  <div class="col-lg-7">
    <div class="card-lite h-100">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <h5 class="mb-1">📦 Thông tin giao hàng</h5>
        <span class="pill {{ $pill }}">{{ $order->status }}</span>
      </div>

      <div class="mb-1"><strong>Người nhận:</strong> {{ $order->shipping_name ?: ($order->user->name ?? 'N/A') }}</div>
      <div class="mb-1"><strong>Điện thoại:</strong> {{ $order->shipping_phone }}</div>
      <div class="mb-1"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</div>
      <div class="mb-1"><strong>Thanh toán:</strong> {{ strtoupper($order->payment_method) }}</div>
      <div class="mb-1"><strong>Đặt lúc:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
      @if($order->user && $order->user->name && $order->user->name !== $order->shipping_name)
        <div class="muted"><em>Tài khoản đặt hàng:</em> {{ $order->user->name }}</div>
      @endif
      @if($order->note)
        <div class="mt-2"><em>Ghi chú: {{ $order->note }}</em></div>
      @endif
    </div>
  </div>

  {{-- Tóm tắt thanh toán --}}
  <div class="col-lg-5">
    <div class="card-lite h-100">
      <h5 class="mb-2">💰 Tóm tắt thanh toán</h5>
      <div class="d-flex justify-content-between"><span>Tạm tính</span><span>{{ number_format($subtotal,0,',','.') }} đ</span></div>
      <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><span>0 đ</span></div>
      <hr class="my-2">
      <div class="d-flex justify-content-between"><strong>Tổng</strong><strong class="price">{{ number_format($order->total_amount,0,',','.') }} đ</strong></div>
    </div>
  </div>
</div>

{{-- Sản phẩm --}}
<div class="card-lite mt-3">
  <h5 class="mb-2">🛒 Sản phẩm trong đơn</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th class="text-start">Sản phẩm</th>
          <th>Đơn giá</th>
          <th>SL</th>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $idx => $it)
          @php
            $p = $it->product;
            $img = $p && $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/60x40?text=TV';
          @endphp
          <tr>
            <td>{{ $idx+1 }}</td>
            <td class="text-start">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ $img }}" alt="" width="60" height="40" style="object-fit:cover;border-radius:6px;border:1px solid var(--border)">
                <a href="{{ route('products.show',$it->product_id) }}" target="_blank">
                  {{ $p->name ?? ('SP#'.$it->product_id) }}
                </a>
              </div>
            </td>
            <td>{{ number_format($it->price,0,',','.') }} đ</td>
            <td>{{ $it->quantity }}</td>
            <td class="price">{{ number_format($it->price*$it->quantity,0,',','.') }} đ</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Form cập nhật trạng thái (KHÔNG lồng vào bảng/khối khác) --}}
<div class="card-lite mt-3">
  <h5 class="mb-2">⚙️ Cập nhật trạng thái</h5>
  <form action="{{ route('admin.orders.updateStatus',$order->id) }}" method="POST" class="row g-2">
    @csrf
    @method('PATCH')
    @php
      $statusOptions = [
        'pending'=>'Chờ xác nhận','processing'=>'Đang xử lý','shipping'=>'Đang giao hàng',
        'delivered'=>'Đã giao hàng','cancelled'=>'Huỷ bỏ','failed'=>'Thanh toán lỗi',
        'paid'=>'Đã thanh toán','unpaid'=>'Chưa thanh toán',
      ];
    @endphp
    <div class="col-md-4">
      <select name="status" class="form-select">
        @foreach($statusOptions as $val=>$label)
          <option value="{{ $val }}" @selected($order->status === $val)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-warning">Cập nhật trạng thái</button>
    </div>
    <div class="col-md-5 text-md-end">
      <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">← Quay lại danh sách</a>
    </div>
  </form>
</div>
@endsection
