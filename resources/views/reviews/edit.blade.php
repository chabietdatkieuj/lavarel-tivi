@extends('layouts.app')
@section('title','Sửa đánh giá')

@push('styles')
<style>
  .page-title{ font-weight:800; color:var(--text-900); }
  .card-lite{
    background: var(--surface); border:1px solid var(--border);
    border-radius:14px; padding:16px;
  }
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

  /* Ảnh hiện có + preview */
  .rv-uploads{ display:grid; grid-template-columns: repeat(auto-fill, minmax(92px,1fr)); gap:.65rem; }
  .rv-thumb{
    position:relative; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#fafafa;
    aspect-ratio:1/1; display:grid; place-items:center; padding:0;
  }
  .rv-thumb img{ width:100%; height:100%; object-fit:cover; }
  .rv-del{
    position:absolute; left:6px; top:6px; background:rgba(255,255,255,.9); border-radius:8px;
    padding:.15rem .35rem; font-size:.76rem; border:1px solid var(--border);
  }
  .rv-name{
    position:absolute; left:0; right:0; bottom:0; font-size:.72rem; padding:.2rem .35rem;
    background:rgba(0,0,0,.45); color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
</style>
@endpush

@section('content')
<h2 class="page-title mb-3">✏️ Sửa đánh giá</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

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

<form method="POST"
      action="{{ route('reviews.update', $review->id) }}"
      class="card-lite mb-3"
      enctype="multipart/form-data">
  @csrf
  @method('PATCH')

  <div class="mb-3">
    <label class="form-label">Số sao</label><br>
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

  {{-- Ảnh hiện có --}}
  @if($review->images && $review->images->count())
  <div class="mb-3">
    <label class="form-label">Ảnh đã tải lên</label>
    <div class="rv-uploads">
      @foreach($review->images as $img)
        <label class="rv-thumb">
          <img src="{{ asset('storage/'.$img->path) }}" alt="">
          <span class="rv-name">#{{ $img->id }}</span>
          <span class="rv-del">
            <input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> Xoá
          </span>
        </label>
      @endforeach
    </div>
    <div class="form-text">Tick “Xoá” ảnh nào bạn muốn gỡ khỏi đánh giá.</div>
  </div>
  @endif

  {{-- Thêm ảnh mới --}}
  <div class="mb-3">
    <label class="form-label">Thêm ảnh mới (nếu cần)</label>
    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
    @error('photos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    @error('photos.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    <div id="rvPreview" class="rv-uploads mt-2"></div>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-gold">Lưu thay đổi</button>
    <a href="{{ route('products.show', $review->product_id) }}#reviews" class="btn btn-outline-secondary">
      Quay lại sản phẩm
    </a>
    @if($review->order_id)
    <a href="{{ route('orders.show', $review->order_id) }}" class="btn btn-outline-secondary">
      Về đơn #{{ $review->order_id }}
    </a>
    @endif
  </div>
</form>

<form action="{{ route('reviews.destroy',$review->id) }}" method="POST"
      onsubmit="return confirm('Xoá đánh giá này?')" class="text-end">
  @csrf @method('DELETE')
  <button class="btn btn-danger">Xoá đánh giá</button>
</form>
@endsection

@push('scripts')
<script>
(function(){
  // preview ảnh mới ở trang edit
  const fileInput = document.querySelector('input[name="photos[]"]');
  const preview   = document.getElementById('rvPreview');
  if (fileInput && preview){
    fileInput.addEventListener('change', () => {
      preview.innerHTML = '';
      const files = Array.from(fileInput.files || []);
      files.forEach(f => {
        if(!f.type.startsWith('image/')) return;
        const url = URL.createObjectURL(f);
        const wrap = document.createElement('div');
        wrap.className = 'rv-thumb';
        wrap.innerHTML = `<img src="${url}" alt=""><div class="rv-name">${f.name}</div>`;
        preview.appendChild(wrap);
      });
    });
  }
})();
</script>
@endpush
