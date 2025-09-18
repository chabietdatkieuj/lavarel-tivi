{{-- resources/views/admin/orders/index.blade.php --}}
@extends('layouts.app')
@section('title','Quản lý đơn hàng')

@push('styles')
<style>
  /* Nhẹ, hợp layout app */
  .orders-title{ font-weight:800; color:var(--text-900); }
  .filter-card{
    background:var(--surface); border:1px solid var(--border);
    border-radius:12px; padding:12px;
  }
  .table thead th{
    background:#f9fafb; color:var(--text-900);
    border-bottom:1px solid var(--border);
    text-transform:uppercase; font-weight:700; letter-spacing:.25px;
  }
  .table tbody td{
    background:var(--surface); color:var(--text-900);
    vertical-align:middle!important; border-color:var(--border);
  }
  .price{ font-weight:800; color:#b45309; }

  /* Badge trạng thái */
  .pill{ border-radius:999px; padding:.2rem .55rem; font-weight:700; font-size:.8rem; }
  .stt-pending    { background:#fde68a; color:#78350f; }
  .stt-processing { background:#bfdbfe; color:#1e3a8a; }
  .stt-shipping   { background:#bbf7d0; color:#065f46; }
  .stt-delivered  { background:#86efac; color:#14532d; }
  .stt-cancelled  { background:#fecaca; color:#7f1d1d; }
  .stt-paid       { background:#d1fae5; color:#065f46; }
  .stt-unpaid     { background:#fee2e2; color:#7f1d1d; }
  .stt-failed     { background:#ffe4e6; color:#9f1239; }
</style>
@endpush

@section('content')
<h2 class="orders-title mb-3">📦 Quản lý đơn hàng</h2>

{{-- BỘ LỌC --}}
<form method="GET" class="filter-card mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-sm-4 col-lg-3">
      <label class="form-label">Trạng thái</label>
      <select name="status" class="form-select">
        @php $cur = $status ?? request('status'); @endphp
        <option value="">— Tất cả —</option>
        @foreach(['pending'=>'pending','processing'=>'processing','shipping'=>'shipping','delivered'=>'delivered','paid'=>'paid','unpaid'=>'unpaid','failed'=>'failed','cancelled'=>'cancelled'] as $val => $label)
          <option value="{{ $val }}" @selected($cur===$val)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-6 col-lg-4">
      <label class="form-label">Tìm kiếm (ID/Người nhận/Điện thoại)</label>
      <input type="text" name="s" value="{{ $search ?? request('s') }}" class="form-control" placeholder="VD: 123, Nguyễn Văn A, 090...">
    </div>
    <div class="col-auto">
      <button class="btn btn-gold">Lọc</button>
      <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
  </div>
</form>

@if(($orders->count() ?? 0) === 0)
  <div class="alert alert-info">Chưa có đơn hàng nào.</div>
@else
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center">
      <thead>
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
        @php
          $map = [
            'pending'    => 'stt-pending',
            'processing' => 'stt-processing',
            'shipping'   => 'stt-shipping',
            'delivered'  => 'stt-delivered',
            'cancelled'  => 'stt-cancelled',
            'paid'       => 'stt-paid',
            'unpaid'     => 'stt-unpaid',
            'failed'     => 'stt-failed',
          ];
          $cls = $map[$o->status] ?? 'stt-processing';
        @endphp
        <tr>
          <td>{{ $o->id }}</td>
          <td class="text-start">{{ $o->user->name ?? 'N/A' }}</td>
          <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
          <td class="price">{{ number_format($o->total_amount,0,',','.') }} đ</td>
          <td class="text-uppercase">{{ $o->payment_method }}</td>
          <td><span class="pill {{ $cls }}">{{ $o->status }}</span></td>
          <td>
            <a href="{{ route('admin.orders.show',$o->id) }}" class="btn btn-info btn-sm">Xem</a>
            <form action="{{ route('admin.orders.destroy',$o->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Xóa đơn #{{ $o->id }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Xóa</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{-- PHÂN TRANG (10 đơn/trang trong Controller) --}}
  <div class="mt-3">
    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
@endif
@endsection
