@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<style>
  .stars{ color:#f59e0b; }
  .review-item{ border-bottom:1px solid var(--border); padding:12px 0 }
  .review-item:last-child{ border-bottom:none }
  .star-lg{ font-size:1.15rem; color:#f59e0b; }
  .muted{ color: var(--text-600); }

  /* Admin replies */
  .admin-replies{ margin-top:.5rem; }
  .reply-item{ display:flex; gap:.5rem; align-items:flex-start; margin:.35rem 0; }
  .reply-badge{
    background:#1e3a8a; color:#fff; font-weight:800; font-size:.7rem;
    border-radius:999px; padding:.2rem .5rem; white-space:nowrap;
  }
  .reply-bubble{
    background:#f3f4f6; border:1px solid var(--border); color:var(--text-900);
    border-radius:10px; padding:.5rem .65rem; flex:1;
  }
  .reply-meta{ font-size:.8rem; color:var(--text-600); margin-bottom:.15rem; }

  /* Review images */
  .rv-photos{
    display:grid; grid-template-columns: repeat(auto-fill, minmax(92px,1fr));
    gap:.65rem; margin-top:.5rem;
  }
  .rv-thumb{
    border:1px solid var(--border); border-radius:10px; overflow:hidden;
    background:#fafafa; aspect-ratio:1/1; display:grid; place-items:center;
  }
  .rv-thumb img{ width:100%; height:100%; object-fit:cover; }
  .rv-filter .btn{ padding:.25rem .55rem; font-weight:700 }
</style>
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4">Chi tiết sản phẩm</h2>

    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4 text-center p-3">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         class="img-fluid rounded" alt="{{ $product->name }}">
                @else
                    <p class="text-muted mt-5">Không có hình ảnh</p>
                @endif
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h4 class="card-title">{{ $product->name }}</h4>
                    <p><strong>Mô tả:</strong> {{ $product->description ?? 'Không có' }}</p>
                    <p><strong>Số lượng:</strong> {{ $product->quantity }}</p>
                    <p><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                    <p><strong>Đặc điểm:</strong> {{ $product->features ?? 'Không có' }}</p>
                    <p><strong>Danh mục:</strong> {{ $product->category->name }}</p>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Quay lại</a>

                        {{-- Nút thêm giỏ cho khách --}}
                        @auth
                          @if(Auth::user()->role === 'customer')
                          <form action="{{ route('cart.add', $product->id) }}" method="POST">
                              @csrf
                              <button class="btn btn-success">🛒 Thêm vào giỏ</button>
                          </form>
                          @endif
                        @else
                          <a href="{{ route('login') }}" class="btn btn-warning">Đăng nhập để mua</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== KHỐI ĐÁNH GIÁ ========== --}}
    @php
        // $product->reviews nên đã được eager-load kèm user, replies.admin, images trong controller
        $reviews = $product->reviews;
        $avg     = (float) number_format($reviews->avg('rating'), 1);
        $count   = $reviews->count();
        $avgInt  = (int) round($avg);
        $curRating = (int) request('rating', 0);
    @endphp

    <div class="card mt-4" id="reviews">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0">⭐ Đánh giá từ khách hàng</h5>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                  <div class="d-flex align-items-center gap-2">
                      <div class="star-lg">
                          @for($i=1;$i<=5;$i++)
                            {!! $i <= $avgInt ? '★' : '☆' !!}
                          @endfor
                      </div>
                      <div class="muted">
                          {{ number_format($avg,1) }}/5 • {{ $count }} đánh giá
                      </div>
                  </div>

                  {{-- Bộ lọc theo số sao --}}
                  <div class="rv-filter">
                    <a class="btn btn-outline-secondary @if(!$curRating) active @endif"
                       href="{{ route('products.show', $product->id) }}#reviews">Tất cả</a>
                    @for($i=5;$i>=1;$i--)
                      <a class="btn btn-outline-primary @if($curRating===$i) active @endif"
                         href="{{ route('products.show', $product->id) }}?rating={{ $i }}#reviews">{{ $i }}★</a>
                    @endfor
                  </div>
                </div>
            </div>

            <div class="mt-3">
                @forelse($reviews as $rv)
                    <div class="review-item">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold">{{ $rv->user->name ?? 'Khách' }}</div>
                            <div class="stars">
                                @for($i=1;$i<=5;$i++)
                                  {!! $i <= ($rv->rating ?? 0) ? '★' : '☆' !!}
                                @endfor
                            </div>
                        </div>

                        {{-- Nội dung --}}
                        @if(!empty($rv->comment))
                          <div class="mt-1">{{ $rv->comment }}</div>
                        @endif
                        <div class="muted small mt-1">{{ optional($rv->created_at)->format('d/m/Y H:i') }}</div>

                        {{-- ẢNH CỦA ĐÁNH GIÁ --}}
                        @if(isset($rv->images) && $rv->images->count())
                          <div class="rv-photos">
                            @foreach($rv->images as $img)
                              @php
                                $src = isset($img->path) ? asset('storage/'.$img->path) : null;
                              @endphp
                              @if($src)
                                <a class="rv-thumb" href="{{ $src }}" target="_blank" rel="noopener">
                                  <img src="{{ $src }}" alt="review image">
                                </a>
                              @endif
                            @endforeach
                          </div>
                        @endif

                        {{-- PHẢN HỒI TỪ ADMIN --}}
                        @if(isset($rv->replies) && $rv->replies->count())
                          <div class="admin-replies ps-3 ms-1 border-start">
                            @foreach($rv->replies as $rep)
                              <div class="reply-item">
                                <span class="reply-badge">ADMIN</span>
                                <div class="reply-bubble">
                                  <div class="reply-meta">
                                    {{ $rep->admin->name ?? 'Quản trị viên' }} • {{ optional($rep->created_at)->format('d/m/Y H:i') }}
                                  </div>
                                  <div>{{ $rep->content }}</div>
                                </div>
                              </div>
                            @endforeach
                          </div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
