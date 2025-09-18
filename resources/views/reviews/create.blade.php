@extends('layouts.app')
@section('title','Đánh giá sản phẩm')

@push('styles')
<style>
  /* Khối chọn sao */
  .rating-wrap{
    display:flex; align-items:center; gap:.5rem;
    padding:.6rem .75rem; border:1px solid var(--border); border-radius:10px;
    background:var(--surface);
  }
  .rating-label{ font-weight:700; color:var(--text-900); margin-right:.25rem }
  .star{
    cursor:pointer; user-select:none; font-size:1.4rem; line-height:1;
    color:#d1d5db; transition:transform .06s ease, color .12s ease;
  }
  .star:hover{ transform:scale(1.06) }
  .star.filled{ color:#f59e0b; } /* vàng */
  .rating-hint{ color:var(--text-600); font-size:.9rem }

  /* Inputs khớp layout app */
  .form-control, .form-select{
    background: var(--surface); color: var(--text-900);
    border:1px solid var(--border); border-radius:10px;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--primary-600);
    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
  }
</style>
@endpush

@section('content')
<h2 class="fw-bold mb-3">⭐ Đánh giá: {{ $product->name }}</h2>

@if($existing)
  <div class="alert alert-info">Bạn đã đánh giá sản phẩm này trong đơn #{{ $order->id }}. Gửi lại sẽ cập nhật.</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

@php
  $currentRating = (int) old('rating', $existing->rating ?? 5);
  $currentComment = old('comment', $existing->comment ?? '');
@endphp

<form method="POST" action="{{ route('reviews.store', ['order'=>$order->id,'product'=>$product->id]) }}">
  @csrf

  {{-- Chọn sao trực quan (vẫn submit field "rating" như cũ) --}}
  <div class="mb-3">
    <label class="form-label d-block">Số sao (1–5)</label>

    <div class="rating-wrap" id="ratingWrap" aria-label="Chọn số sao">
      <span class="rating-label me-1">Sao:</span>
      <span class="star" data-value="1">★</span>
      <span class="star" data-value="2">★</span>
      <span class="star" data-value="3">★</span>
      <span class="star" data-value="4">★</span>
      <span class="star" data-value="5">★</span>
      <span class="ms-2 rating-hint" id="ratingHint"></span>
    </div>

    {{-- input số để server validate như cũ, nhưng ẩn đi (vẫn giữ name=rating) --}}
    <input type="number" name="rating" id="ratingInput" class="form-control d-none"
           min="1" max="5" value="{{ $currentRating }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Nhận xét</label>
    <textarea name="comment" rows="4" class="form-control"
      placeholder="Cảm nhận của bạn...">{{ $currentComment }}</textarea>
  </div>

  <button class="btn btn-gold">Gửi đánh giá</button>
  <a href="{{ route('orders.show',$order->id) }}" class="btn btn-outline-secondary ms-1">Quay lại đơn</a>
</form>
@endsection

@push('scripts')
<script>
(function(){
  const stars = Array.from(document.querySelectorAll('.star'));
  const input = document.getElementById('ratingInput');
  const hint  = document.getElementById('ratingHint');
  const words = {1:'Rất tệ',2:'Tệ',3:'Bình thường',4:'Tốt',5:'Tuyệt vời'};

  function paint(val){
    stars.forEach(s => s.classList.toggle('filled', Number(s.dataset.value) <= val));
    hint.textContent = val ? (val + '/5 • ' + words[val]) : '';
  }

  // init
  let cur = Number(input.value || 5);
  paint(cur);

  stars.forEach(s => {
    s.addEventListener('click', () => {
      input.value = s.dataset.value;
      paint(Number(input.value));
    });
    s.addEventListener('mouseenter', () => paint(Number(s.dataset.value)));
    s.addEventListener('mouseleave', () => paint(Number(input.value)));
  });
})();
</script>
@endpush
