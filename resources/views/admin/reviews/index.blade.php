@extends('layouts.app')
@section('title','Quản lý đánh giá')

@push('styles')
<style>
  .page-title{ font-weight:800; color:var(--text-900); }
  .filter-card{
    background: var(--surface);
    border:1px solid var(--border);
    border-radius:12px; padding:12px 12px;
  }
  .stars{ color:#f59e0b; letter-spacing:.5px; }
  .table thead th{
    background:#f9fafb; color:var(--text-900); border-bottom:1px solid var(--border);
    text-transform:uppercase; font-weight:700; letter-spacing:.3px;
  }
  .table tbody td{
    background:var(--surface); color:var(--text-900); vertical-align:middle!important;
    border-color:var(--border);
  }
  .cell-comment{ max-width: 520px; }
  .badge-id{ background:#eef2ff; color:#3730a3; font-weight:700; }
  .btn-danger.btn-sm{ font-weight:600 }

  /* replies block */
  .reply-wrap{ background:#f8fafc; border-top:1px dashed var(--border); }
  .reply-item{ background:#ffffff; border:1px solid var(--border); border-radius:10px; padding:.65rem .75rem; }
  .reply-meta{ color:var(--text-600); font-size:.82rem }
  .reply-actions .btn{ padding:.15rem .5rem; font-size:.775rem }
</style>
@endpush

@section('content')
<h2 class="page-title mb-3">📝 Quản lý đánh giá</h2>

{{-- ====== BỘ LỌC ====== --}}
<form method="GET" class="filter-card mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-4">
      <label class="form-label">Sản phẩm</label>
      <select name="product_id" class="form-select">
        <option value="">— Tất cả —</option>
        @foreach($products as $p)
          <option value="{{ $p->id }}" @selected(($productId ?? null)==$p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-6 col-md-2">
      <label class="form-label">Số sao</label>
      @php $rating = request('rating'); @endphp
      <select name="rating" class="form-select">
        <option value="">— Tất cả —</option>
        @for($i=5;$i>=1;$i--)
          <option value="{{ $i }}" @selected((string)$rating===(string)$i)>{{ $i }} sao</option>
        @endfor
      </select>
    </div>

    <div class="col-auto">
      <button class="btn btn-gold">Lọc</button>
      <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
  </div>
</form>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- ====== BẢNG ĐÁNH GIÁ ====== --}}
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th width="70">#</th>
        <th>Sản phẩm</th>
        <th>Khách hàng</th>
        <th width="140">Rating</th>
        <th class="text-start">Nhận xét</th>
        <th width="160">Thời gian</th>
        <th width="220">Hành động</th>
      </tr>
    </thead>
    <tbody>
    @forelse($reviews as $rv)
      {{-- HÀNG CHÍNH --}}
      <tr>
        <td><span class="badge badge-id">{{ $rv->id }}</span></td>

        <td class="text-start">
          <a href="{{ route('products.show', $rv->product_id) }}" target="_blank">
            {{ $rv->product->name ?? '—' }}
          </a>
        </td>

        <td>{{ $rv->user->name ?? '—' }}</td>

        <td class="text-nowrap">
          <div class="stars">
            @for($i=1;$i<=5;$i++) {!! $i <= (int)$rv->rating ? '★' : '☆' !!} @endfor
          </div>
          <small class="text-muted">{{ $rv->rating }} / 5</small>
        </td>

        <td class="text-start cell-comment">
          {{ $rv->comment ?: '—' }}
        </td>

        <td>{{ $rv->created_at->format('d/m/Y H:i') }}</td>

        <td class="text-start">
          <button class="btn btn-outline-primary btn-sm me-1"
                  type="button" data-bs-toggle="collapse" data-bs-target="#rep-{{ $rv->id }}">
            ↩ Trả lời ({{ $rv->replies->count() }})
          </button>

          <form action="{{ route('admin.reviews.destroy', $rv->id) }}" method="POST"
                class="d-inline" onsubmit="return confirm('Xóa đánh giá này?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Xóa</button>
          </form>
        </td>
      </tr>

      {{-- HÀNG REPLIES COLLAPSE --}}
      <tr>
        <td colspan="7" class="p-0 border-0">
          <div class="collapse" id="rep-{{ $rv->id }}">
            <div class="reply-wrap p-3">
              {{-- danh sách replies --}}
              @if($rv->replies->count())
                <div class="d-flex flex-column gap-2 mb-3">
                  @foreach($rv->replies as $rp)
                    <div class="reply-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="fw-semibold">ADMIN: {{ $rp->admin->name ?? 'Quản trị' }}</div>
                          <div class="reply-meta">Trả lời lúc {{ $rp->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="reply-actions">
                          {{-- nút sửa bật form bên dưới --}}
                          <button class="btn btn-outline-secondary btn-sm"
                                  type="button" data-bs-toggle="collapse"
                                  data-bs-target="#edit-rep-{{ $rp->id }}">Sửa</button>

                          <form action="{{ route('admin.reviews.replies.destroy', $rp->id) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Xoá trả lời này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Xoá</button>
                          </form>
                        </div>
                      </div>
                      <div class="mt-1">{{ $rp->content }}</div>

                      {{-- form edit --}}
                      <div class="collapse mt-2" id="edit-rep-{{ $rp->id }}">
                        <form method="POST" action="{{ route('admin.reviews.replies.update', $rp->id) }}">
                          @csrf @method('PATCH')
                          <div class="input-group">
                            <textarea name="content" class="form-control" rows="2" required>{{ $rp->content }}</textarea>
                            <button class="btn btn-gold">Cập nhật</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-muted mb-3">Chưa có trả lời.</div>
              @endif

              {{-- form thêm trả lời --}}
              <form method="POST" action="{{ route('admin.reviews.replies.store', $rv->id) }}">
                @csrf
                <label class="form-label fw-semibold">Thêm trả lời</label>
                <div class="input-group">
                  <textarea name="content" class="form-control" rows="2" placeholder="Phản hồi của admin..." required></textarea>
                  <button class="btn btn-primary">Gửi</button>
                </div>
                <div class="form-text">Trả lời sẽ hiển thị ngay dưới đánh giá của khách.</div>
              </form>
            </div>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center text-muted">Chưa có đánh giá.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="mt-2">
  {{ $reviews->links('pagination::bootstrap-5') }}
</div>
@endsection
