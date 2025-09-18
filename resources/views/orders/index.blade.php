@extends('layouts.app')
@section('title','Đơn hàng của tôi')

@push('styles')
<style>
  /* Bảng sáng gọn khớp layout */
  .orders-title{ font-weight:800; color:var(--text-900); }
  .orders-table thead th{
    background:#f9fafb; color:var(--text-900); border-bottom:1px solid var(--border);
    text-transform:uppercase; font-weight:700; letter-spacing:.3px;
  }
  .orders-table tbody td{
    background:var(--surface); color:var(--text-900); vertical-align:middle!important;
    border-color:var(--border);
  }
  .price{ font-weight:800; color:#b45309; } /* nâu vàng dịu hơn để nổi bật tổng */

  /* Badge trạng thái */
  .pill{ border-radius:999px; padding:.2rem .55rem; font-weight:700; font-size:.8rem; }
  .stt-pending   { background:#fde68a; color:#78350f; }
  .stt-processing{ background:#bfdbfe; color:#1e3a8a; }
  .stt-shipping  { background:#bbf7d0; color:#065f46; }
  .stt-delivered { background:#86efac; color:#14532d; }
  .stt-cancelled { background:#fecaca; color:#7f1d1d; }

  /* Review helpers */
  .badge-replied{
    background:#fee2e2; color:#7f1d1d; border-radius:999px; font-weight:800;
    padding:.1rem .45rem; font-size:.75rem;
  }
  .dropdown-item .mini{
    font-size:.82rem; color:var(--text-600);
  }
</style>
@endpush

@section('content')
<h2 class="orders-title mb-3">📦 Lịch sử đơn hàng</h2>

@if($orders->isEmpty())
  <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
@else
  <div class="table-responsive">
    <table class="table orders-table table-hover align-middle text-center">
      <thead>
        <tr>
          <th>#</th>
          <th>Ngày</th>
          <th>Tổng</th>
          <th>Thanh toán</th>
          <th>Trạng thái</th>
          <th width="340">Hành động</th>
        </tr>
      </thead>
      <tbody>
      @foreach($orders as $o)
        <tr>
          <td>{{ $o->id }}</td>
          <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
          <td class="price">{{ number_format($o->total_amount,0,',','.') }} đ</td>
          <td class="text-uppercase">{{ $o->payment_method }}</td>
          <td>
            @php
              $map = [
                'pending'    => 'stt-pending',
                'processing' => 'stt-processing',
                'shipping'   => 'stt-shipping',
                'delivered'  => 'stt-delivered',
                'cancelled'  => 'stt-cancelled',
              ];
              $cls = $map[$o->status] ?? 'stt-processing';
            @endphp
            <span class="pill {{ $cls }}">{{ $o->status }}</span>
          </td>
          <td class="text-center">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('orders.show',$o->id) }}">Xem</a>

            @if($o->payment_method === 'momo' && $o->status !== 'paid')
              <a class="btn btn-success btn-sm" href="{{ route('orders.momo.pay',$o) }}">Thanh toán lại MoMo</a>
            @endif

            {{-- Đánh giá/Chỉnh sửa/Xem phản hồi: chỉ khi đơn đã giao --}}
            @if($o->status === 'delivered')
              @php
                // Cố gắng dùng items nếu controller đã load; nếu không, để trống (fallback link “Đánh giá” về trang đơn)
                $items = $o->items ?? collect();
              @endphp

              @if($items && $items->count() > 1)
                {{-- Nhiều sản phẩm: hiển thị dropdown từng item kèm trạng thái review --}}
                <div class="btn-group">
                  <button type="button" class="btn btn-gold btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    ✍️ Đánh giá / chỉnh sửa
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    @foreach($items as $it)
                      @php
                        $rev = \App\Models\Review::where([
                          'order_id'   => $o->id,
                          'product_id' => $it->product_id,
                          'user_id'    => auth()->id(),
                        ])->withCount('replies')->first();
                      @endphp

                      @if(!$rev)
                        {{-- Chưa có đánh giá --}}
                        <li>
                          <a class="dropdown-item"
                             href="{{ route('reviews.create', ['order' => $o->id, 'product' => $it->product_id]) }}">
                            ✍️ {{ $it->product->name ?? ('SP #'.$it->product_id) }}
                            <div class="mini">Chưa đánh giá</div>
                          </a>
                        </li>
                      @else
                        {{-- Đã đánh giá: cho phép SỬA; nếu có phản hồi → badge --}}
                        <li>
                          <a class="dropdown-item"
                             href="{{ route('reviews.edit', $rev->id) }}">
                            ✏️ {{ $it->product->name ?? ('SP #'.$it->product_id) }}
                            <div class="mini">
                              Đã đánh giá
                              @if($rev->replies_count > 0)
                                • <span class="badge-replied">💬 {{ $rev->replies_count }}</span>
                              @endif
                            </div>
                          </a>
                        </li>
                        @if($rev->replies_count > 0)
                          <li>
                            <a class="dropdown-item"
                               href="{{ route('products.show', $it->product_id) }}#reviews">
                              💬 Xem phản hồi (admin)
                            </a>
                          </li>
                        @endif
                      @endif

                      @if(!$loop->last) <li><hr class="dropdown-divider"></li> @endif
                    @endforeach
                  </ul>
                </div>
              @elseif($items && $items->count() === 1)
                @php
                  $only = $items->first();
                  $rev  = \App\Models\Review::where([
                    'order_id'   => $o->id,
                    'product_id' => $only->product_id,
                    'user_id'    => auth()->id(),
                  ])->withCount('replies')->first();
                @endphp

                @if(!$rev)
                  <a class="btn btn-gold btn-sm"
                     href="{{ route('reviews.create', ['order' => $o->id, 'product' => $only->product_id]) }}">
                    ✍️ Đánh giá
                  </a>
                @else
                  <a class="btn btn-gold btn-sm" href="{{ route('reviews.edit', $rev->id) }}">
                    ✏️ Sửa đánh giá
                  </a>
                  @if($rev->replies_count > 0)
                    <a class="btn btn-outline-secondary btn-sm"
                       href="{{ route('products.show', $only->product_id) }}#reviews">
                      💬 Xem phản hồi ({{ $rev->replies_count }})
                    </a>
                  @endif
                @endif
              @else
                {{-- Không chắc có items: fallback về trang chi tiết đơn --}}
                <a class="btn btn-gold btn-sm" href="{{ route('orders.show',$o->id) }}">
                  ✍️ Đánh giá
                </a>
              @endif
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{-- Phân trang --}}
  <div class="mt-2">
    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
@endif
@endsection
