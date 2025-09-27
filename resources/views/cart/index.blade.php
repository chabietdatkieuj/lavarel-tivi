@extends('layouts.app')
@section('title', 'Giỏ hàng')

@push('styles')
<style>
  /* ---------- THEME (khớp layout app) ---------- */
  .cart-title{ font-weight:800; color: var(--text-900); }

  .cart-table{
    border:1px solid var(--border); border-radius:12px; overflow:hidden;
    background: var(--surface);
  }
  .cart-table thead th{
    background:#f9fafb; color:var(--text-900); border-bottom:1px solid var(--border);
    font-weight:700; text-transform:uppercase; letter-spacing:.3px;
  }
  .cart-table tbody td{
    background: var(--surface); color: var(--text-900);
    vertical-align: middle !important; border-color: var(--border);
  }
  .cart-table tbody tr:nth-child(even) td{ background:#fafafa; }

  .img-thumb{
    width:88px; height:60px; object-fit:cover; border-radius:10px;
    border:1px solid var(--border);
    box-shadow:0 4px 10px rgba(0,0,0,.05);
  }

  .price{ font-weight:800; color:#111827; }
  .price.highlight{ color:#0f172a; }

  .summary-row td{
    font-weight:800; background:#f3f4f6 !important; color:var(--text-900) !important;
    border-top:1px solid var(--border);
  }

  /* Buttons (giữ class cũ, chỉ style lại cho hợp layout) */
  .btn-update{ background:var(--primary-600); color:#fff; font-weight:600; border:none; }
  .btn-update:hover{ filter:brightness(.96) }
  .btn-del{ background:#ef4444; color:#fff; font-weight:600; border:none; }
  .btn-del:hover{ filter:brightness(.95) }
  .btn-clear{ background:#111827; color:#fff; font-weight:700; border:none; }
  .btn-clear:hover{ filter:brightness(.95) }

  /* Inputs */
  .cart-qty{ max-width:84px; }
  .form-control{
    background: var(--surface); color: var(--text-900);
    border:1px solid var(--border); border-radius:10px;
  }
  .form-control:focus{
    border-color: var(--primary-600);
    box-shadow:0 0 0 .2rem rgba(37,99,235,.15);
  }

  /* Empty state */
  .empty-card{
    background: var(--surface);
    border:1px solid var(--border);
    border-radius:12px;
    color: var(--text-600);
  }

  @media (max-width:576px){ .img-thumb{ width:70px; height:48px } }
</style>
@endpush

@section('content')
<div class="container my-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="cart-title">🛒 Giỏ hàng</h2>

    @if(count($cart->items) > 0)
      <form action="{{ route('cart.clear') }}" method="POST"
            onsubmit="return confirm('Xóa tất cả sản phẩm trong giỏ?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-clear btn-sm">Xóa tất cả</button>
      </form>
    @endif
  </div>

  {{-- Flash message --}}
 

  @if(count($cart->items) === 0)
    <div class="p-4 text-center empty-card">
      <p class="mb-2">Giỏ hàng của bạn đang trống.</p>
      <a href="{{ route('products.index') }}" class="fw-bold" style="color:var(--primary-600)">Xem sản phẩm</a>
    </div>
  @else
    <div class="table-responsive">
      <table class="table cart-table table-hover align-middle text-center mb-0">
        <thead>
          <tr>
            <th>SP</th>
            <th class="text-start">Tên</th>
            <th>Giá</th>
            <th>SL</th>
            <th>Thành tiền</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart->items as $item)
            <tr>
              <td>
                @if($item->product->image)
                  <img src="{{ asset('storage/'.$item->product->image) }}" class="img-thumb" alt="">
                @endif
              </td>
              <td class="text-start fw-semibold">{{ $item->product->name }}</td>
              <td class="price">{{ number_format($item->price, 0, ',', '.') }} đ</td>
              <td>
                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex justify-content-center">
                  @csrf @method('PATCH')
                  <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                         class="form-control form-control-sm cart-qty me-2" aria-label="Số lượng">
                  <button type="submit" class="btn btn-update btn-sm">Cập nhật</button>
                </form>
              </td>
              <td class="price highlight">
                {{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ
              </td>
              <td>
                <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                      onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-del btn-sm">Xóa</button>
                </form>
              </td>
            </tr>
          @endforeach

          <tr class="summary-row">
            <td colspan="3" class="text-end">Tổng:</td>
            <td>{{ $totalQty }}</td>
            <td colspan="2">{{ number_format($totalCost, 0, ',', '.') }} đ</td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- CTA Thanh toán (giữ nguyên route/logic) --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
      <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">↩ Tiếp tục mua sắm</a>
      <a href="{{ route('checkout.create') }}" class="btn btn-gold btn-lg">💳 Thanh toán</a>
    </div>

    {{-- Lịch sử mua gần đây (giữ nguyên include) --}}
    @include('orders._mini_history', ['orders' => $recentOrders ?? collect()])
  @endif
</div>
@endsection
