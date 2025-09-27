@extends('layouts.app')
@section('title', 'Danh mục Tivi')

@push('styles')
<style>
  .tv-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:20px;
    box-shadow:0 6px 18px rgba(17,24,39,.08);
  }
  .tv-title{ font-weight:800; color:#111827; }
  .btn-add{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
    color:#fff; font-weight:700; border:none;
    border-radius:8px; padding:9px 14px;
  }
  .btn-add:hover{ filter:brightness(.96) }

  .tv-table thead th{
    background:#f3f4f6; color:#111827;
    border:0; font-weight:700;
    text-transform:uppercase; letter-spacing:.2px;
  }
  .tv-table tbody td{
    background:#fff; color:#374151;
    vertical-align:middle!important;
    border-color:#e5e7eb;
  }
  .tv-badge{
    padding:5px 10px; border-radius:999px;
    font-weight:700; font-size:.85rem;
  }

  .btn-pill{ border-radius:999px; padding:.35rem .75rem }
  .btn-view{ background:#3b82f6; border:none; color:#fff }
  .btn-edit{ background:#2563eb; border:none; color:#fff }
  .btn-del{ background:#ef4444; border:none; color:#fff }
  .btn-view:hover,.btn-edit:hover,.btn-del:hover{ filter:brightness(.93) }

  .empty-state{
    padding:20px; border-radius:12px;
    text-align:center; background:#f9fafb; color:#6b7280;
  }
</style>
@endpush

@section('content')
<div class="container my-4">
  <div class="tv-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="tv-title mb-0">📂 Danh mục</h2>

      @auth
        @if(auth()->user()->role === 'admin')
          <a class="btn btn-add" href="{{ route('categories.create') }}">+ Thêm Danh Mục</a>
        @endif
      @endauth
    </div>

    

    @if($categories->count() === 0)
      <div class="empty-state mt-2">
        Chưa có danh mục nào.
        @auth
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('categories.create') }}" class="fw-bold text-primary">Tạo danh mục đầu tiên</a>
          @endif
        @endauth
      </div>
    @else
      <div class="table-responsive">
        <table class="table tv-table table-hover align-middle text-center mb-3">
          <thead>
            <tr>
              <th width="80">ID</th>
              <th class="text-start">Tên danh mục</th>
              <th width="260">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($categories as $category)
              <tr>
                <td>
                  <span class="badge bg-primary tv-badge">{{ $category->id }}</span>
                </td>
                <td class="text-start fw-semibold">{{ $category->name }}</td>
                <td>
                  <a class="btn btn-view btn-sm btn-pill me-1" href="{{ route('categories.show',$category->id) }}">Xem</a>
                  @auth
                    @if(auth()->user()->role === 'admin')
                      <a class="btn btn-edit btn-sm btn-pill me-1" href="{{ route('categories.edit',$category->id) }}">Sửa</a>
                      <form action="{{ route('categories.destroy',$category->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-del btn-sm btn-pill"
                          onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                      </form>
                    @endif
                  @endauth
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
