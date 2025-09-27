{{-- resources/views/reviews/edit.blade.php --}}
@extends('layouts.app')
@section('title','Sửa đánh giá')

@push('styles')
<style>
  .page-title{ font-weight:800; color:var(--text-900); }
  .card-lite{
    background: var(--surface); border:1px solid var(--border);
    border-radius:14px; padding:16px;
  }
  /* Stars input (radio + label) */
  .rating-group{ display:inline-flex; flex-direction: row-reverse; gap:.25rem; }
  .rating-group input{ display:none; }
  .rating-group label{
    font-size:1.5rem; cursor:pointer; color:#d1d5db; user-select:none;
    transition:transform .05s ease;
  }
  .rating-group input:checked ~ label,
  .rating-group label:hover,
  .rating-group label:hover ~ label{ color:#f59e0b; }
  .rating-info{ color:var(--text-600); }
</style>
@endpush

@section('content')
<h2 class="page-title mb-3">✏️ Sửa đánh giá</h2>




<div class="card-lite mb-3">
  <div class="d-flex justify-content-between flex-wrap gap-2">
    <div>
      <div class="fw-bold">Sản phẩm:</div>
      <div>
        <a href="{{ route('products.show', $review->product_id) }}" class="text-decoration-none">
          {{ $review->product->name ?? ('#'.$review->product_id) }}
        </a>
      </div>
    </div>
    <div class="text-end">
      <div class="rating-info small">Tạo lúc {{ $review->created_at->format('d/m/Y H:i') }}</div>
      @if($review->updated_at && $review->updated_at->ne($review->created_at))
        <div class="rating-info small">Cập nhật {{ $review->updated_at->format('d/m/Y H:i') }}</div>
      @endif
    </div>
  </div>
</div>

{{-- FORM CẬP NHẬT --}}
<form method="POST" action="{{ route('reviews.update', $review->id) }}" class="card-lite mb-3">
  @csrf
  @method('PATCH')

  <div class="mb-3">
    <label class="form-label">Số sao</label><br>
    {{-- 5→1 để dùng selector ~ và flex row-reverse --}}
    <div class="rating-group" aria-label="Chọn số sao">
      @for($i=5;$i>=1;$i--)
        <input type="radio" id="rate-{{ $i }}" name="rating" value="{{ $i }}"
               @checked(old('rating', (int)$review->rating) == $i)>
        <label for="rate-{{ $i }}" title="{{ $i }} sao">★</label>
      @endfor
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Nhận xét</label>
    <textarea name="comment" class="form-control" rows="4"
              placeholder="Cảm nhận của bạn...">{{ old('comment', $review->comment) }}</textarea>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-gold">Lưu thay đổi</button>
    <a href="{{ route('products.show', $review->product_id) }}#reviews" class="btn btn-outline-secondary">
      Quay lại sản phẩm
    </a>
    <a href="{{ route('orders.show', $review->order_id) }}" class="btn btn-outline-secondary">
      Về đơn #{{ $review->order_id }}
    </a>
  </div>
</form>

{{-- FORM XOÁ (TÁCH RIÊNG, KHÔNG LỒNG TRONG FORM TRÊN) --}}
<form action="{{ route('reviews.destroy',$review->id) }}" method="POST"
      onsubmit="return confirm('Xoá đánh giá này?')" class="text-end">
  @csrf @method('DELETE')
  <button class="btn btn-danger">Xoá đánh giá</button>
  
</form>
@endsection
