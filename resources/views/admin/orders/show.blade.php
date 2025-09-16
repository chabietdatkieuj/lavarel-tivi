@extends('layouts.app')
@section('title','Chi tiết đơn #'.$order->id)

@section('content')
<h2 class="fw-bold text-white mb-3">🧾 Chi tiết đơn #{{ $order->id }}</h2>

<div class="card bg-transparent border-0">
  <div class="card-body">
    <p><strong>Khách:</strong> {{ $order->user->name ?? 'N/A' }}</p>
    <p><strong>Ngày:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount,0,',','.') }} đ</p>
    <p><strong>Thanh toán:</strong> {{ strtoupper($order->payment_method) }}</p>
    <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
  </div>
</div>

{{-- ✅ Danh sách sản phẩm trong đơn --}}
<div class="card bg-transparent border-0 mt-3">
  <div class="card-body">
    <h5 class="text-white">🛒 Sản phẩm</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Sản phẩm</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $idx => $it)
            <tr>
              <td>{{ $idx+1 }}</td>
              <td>{{ $it->product->name ?? ('SP#'.$it->product_id) }}</td>
              <td>{{ number_format($it->price,0,',','.') }} đ</td>
              <td>{{ $it->quantity }}</td>
              <td class="fw-bold text-warning">{{ number_format($it->price * $it->quantity,0,',','.') }} đ</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ✅ Form cập nhật trạng thái --}}
<form action="{{ route('admin.orders.updateStatus',$order->id) }}" method="POST" class="mt-3">
  @csrf @method('PATCH')

  @php
    // Mã -> Nhãn TV
    $statusOptions = [
      'pending'    => 'Chờ xác nhận',
      'processing' => 'Đang xử lý',
      'shipping'   => 'Đang giao hàng',
      'delivered'  => 'Đã giao hàng',
      'cancelled'  => 'Huỷ bỏ',
      'paid'       => 'Đã thanh toán',
      'unpaid'     => 'Chưa thanh toán',
    ];
  @endphp

  <div class="row g-2">
    <div class="col-md-4">
      <select name="status" class="form-select bg-dark text-white">
        @foreach($statusOptions as $value => $label)
          <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-warning">Cập nhật trạng thái</button>
    </div>
  </div>
</form>

@endsection