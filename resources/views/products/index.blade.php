@extends('layouts.app')
@section('title', 'Sản phẩm')

@push('styles')
<style>
  /* ===== Advanced Search ===== */
  .adv-card{
    background:#fff; border:1px solid #e5e7eb;
    border-radius:14px; padding:14px 16px; margin-bottom:16px;
    box-shadow:0 4px 12px rgba(17,24,39,.06);
  }
  .adv-label{ color:#374151; font-weight:600; font-size:.9rem }
  .adv-input,.adv-select{ background:#fff; border:1px solid #d1d5db; color:#111827 }
  .btn-reset{ background:#f9fafb; border:1px solid #d1d5db; color:#374151 }

  /* ===== CARD GRID (Customer/Guest) ===== */
  .shop-grid{ margin-top:.25rem }
  .card-product{
    background:#fff; border:1px solid #e5e7eb;
    border-radius:14px; box-shadow:0 8px 20px rgba(17,24,39,.06);
    transition:.18s; display:flex; flex-direction:column; overflow:hidden;
  }
  .card-product:hover{ transform:translateY(-2px); box-shadow:0 12px 24px rgba(17,24,39,.10) }
  .cp-img-wrap{ height:200px; background:#f3f4f6 }
  .cp-img{ width:100%; height:100%; object-fit:cover; }
  .cp-body{ padding:14px 16px; flex:1 }
  .cp-title{ font-weight:800; color:#111827; margin-bottom:2px }
  .cp-price{ color:#2563eb; font-weight:900 }
  .cp-cat{ color:#6b7280; font-size:.86rem }
  .cp-actions{ padding:0 16px 16px; display:flex; gap:.5rem }
  .btn-cart{ background:linear-gradient(90deg,#42e695,#3bb2b8); border:none; color:#083040; font-weight:800 }
  .btn-cart:hover{ filter:brightness(.95) }

  .ellipsis-28{ max-width:28ch; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }

  /* ===== ADMIN TABLE ===== */
  .tv-card{
    background:#fff; border:1px solid #e5e7eb;
    border-radius:14px; padding:16px;
    box-shadow:0 8px 20px rgba(17,24,39,.08);
  }
  .admin-table-wrap{ max-height:540px; overflow:auto; border-radius:12px; border:1px solid #e5e7eb }
  .tv-table thead th{
    position:sticky; top:0; background:#f3f4f6;
    color:#111827; border:0; font-weight:700; text-transform:uppercase;
  }
  .tv-table tbody td{ background:#fff; color:#374151; border-color:#e5e7eb; vertical-align:middle!important }
  .img-thumb{ width:72px; height:48px; object-fit:cover; border:1px solid #e5e7eb; border-radius:8px }
.btn-add{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
    color:#fff; font-weight:700; border:none;
    border-radius:8px; padding:9px 14px;
  }
  .btn-add:hover{ filter:brightness(.96) }
  .btn-pill{ border-radius:999px; padding:.32rem .65rem }
  .btn-view{ background:#3b82f6; border:none; color:#fff }
  .btn-edit{ background:#2563eb; border:none; color:#fff }
  .btn-del{ background:#ef4444; border:none; color:#fff }
  .price{ font-weight:800; color:#2563eb }
  .qty{ font-variant-numeric: tabular-nums }

  .pagination{ justify-content:center; gap:.25rem }
  .page-link{ background:#fff; color:#374151; border:1px solid #d1d5db }
  .page-link:hover{ background:#f3f4f6; color:#111827 }
  .page-item.active .page-link{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
    color:#fff; border-color:transparent; font-weight:800
  }
  .page-item.disabled .page-link{ background:#f9fafb; color:#9ca3af }
</style>
@endpush

@section('content')

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
  // lấy categories cho dropdown tìm kiếm
  $__categories = \App\Models\Category::select('id','name')->orderBy('name')->get();
@endphp

{{-- ======= Thanh tìm kiếm nâng cao (chung) ======= --}}
<form action="{{ route('products.index') }}" method="GET" class="adv-card">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-4">
      <label class="adv-label">Từ khóa</label>
      <input type="text" name="q" class="form-control adv-input" placeholder="Tên, mô tả, đặc điểm…"
             value="{{ request('q') }}">
    </div>
    <div class="col-6 col-md-3">
      <label class="adv-label">Danh mục</label>
      <select name="category_id" class="form-select adv-select">
        <option value="">-- Tất cả --</option>
        @foreach($__categories as $c)
          <option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="adv-label">Giá từ (VND)</label>
      <input type="number" name="price_min" min="0" class="form-control adv-input"
             value="{{ request('price_min') }}">
    </div>
    <div class="col-6 col-md-2">
      <label class="adv-label">Đến (VND)</label>
      <input type="number" name="price_max" min="0" class="form-control adv-input"
             value="{{ request('price_max') }}">
    </div>
    <div class="col-6 col-md-3">
      <label class="adv-label">Sắp xếp</label>
      <select name="sort" class="form-select adv-select">
        <option value="">Mặc định</option>
        <option value="price_asc"  @selected(request('sort')=='price_asc')>Giá tăng dần</option>
        <option value="price_desc" @selected(request('sort')=='price_desc')>Giá giảm dần</option>
        <option value="name_asc"   @selected(request('sort')=='name_asc')>Tên A → Z</option>
        <option value="name_desc"  @selected(request('sort')=='name_desc')>Tên Z → A</option>
      </select>
    </div>
    <div class="col-12 col-md-3 d-flex gap-2">
      <button class="btn btn-gold flex-grow-1" type="submit">🔎 Tìm kiếm</button>
      <a class="btn btn-reset" href="{{ route('products.index') }}">Làm mới</a>
    </div>
    <div class="col-12 small text-white-50">
      @php $count = method_exists($products,'total') ? $products->total() : $products->count(); @endphp
      @if(request()->query())
        Đang lọc: hiển thị <strong>{{ $products->count() }}</strong> / tổng <strong>{{ $count }}</strong> sản phẩm.
      @endif
    </div>
  </div>
</form>

@guest
  {{-- ======= GUEST: lưới sản phẩm ======= --}}
  <h2 class="mb-3 fw-bold text-white">🛒 Sản phẩm</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 shop-grid">
    @forelse($products as $p)
      <div class="col">
        <div class="card-product">
          <div class="cp-img-wrap">
            @php $img = $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/420x260?text=TV+Store'; @endphp
            <img class="cp-img" src="{{ $img }}" alt="{{ $p->name }}">
          </div>
          <div class="cp-body">
            <div class="cp-title ellipsis-28" title="{{ $p->name }}">{{ $p->name }}</div>
            <div class="cp-cat">{{ $p->category->name ?? 'Chưa phân loại' }}</div>
            <div class="cp-price mt-1">{{ number_format($p->price,0,',','.') }} VND</div>
          </div>
          <div class="cp-actions">
            <a href="{{ route('products.show', $p->id) }}" class="btn btn-outline-info w-50">Xem</a>
            <a href="{{ route('login') }}" class="btn btn-warning w-50">Đăng nhập để mua</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col"><div class="alert alert-info">Chưa có sản phẩm.</div></div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $products->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>

@else
  @if(auth()->user()->role !== 'admin')
    {{-- ======= CUSTOMER: lưới sản phẩm ======= --}}
    <h2 class="mb-3 fw-bold text-white">🛒 Sản phẩm</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 shop-grid">
      @foreach($products as $p)
        <div class="col">
          <div class="card-product">
            <div class="cp-img-wrap">
              @php $img = $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/420x260?text=TV+Store'; @endphp
              <img class="cp-img" src="{{ $img }}" alt="{{ $p->name }}">
            </div>
            <div class="cp-body">
              <div class="cp-title ellipsis-28" title="{{ $p->name }}">{{ $p->name }}</div>
              <div class="cp-cat">{{ $p->category->name ?? 'Chưa phân loại' }}</div>
              <div class="cp-price mt-1">{{ number_format($p->price,0,',','.') }} VND</div>
            </div>
            <div class="cp-actions">
              <a href="{{ route('products.show', $p->id) }}" class="btn btn-outline-info w-50">Xem</a>
              <form action="{{ route('cart.add', $p->id) }}" method="POST" class="w-50">
                @csrf
                <button class="btn btn-cart w-100">+ Thêm vào giỏ</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $products->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

  @else
    {{-- ======= ADMIN: bảng gọn, khung cuộn riêng ======= --}}
    <div class="tv-card">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <h2 class="tv-title mb-0">🛒 Sản phẩm</h2>
        <div class="d-flex gap-2">
          <button type="button" class="btn density-toggle" id="btnDensity">Mật độ: Chuẩn</button>
          <a class="btn btn-add" href="{{ route('products.create') }}">+ Thêm sản phẩm</a>
        </div>
      </div>

      <div class="admin-table-wrap" id="adminTableWrap">
        <table class="table table-sm tv-table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th width="52">#</th>
              <th width="200">Tên</th>
              <th class="col-desc">Mô tả</th>
              <th width="74">SL</th>
              <th width="150">Giá</th>
              <th class="col-features" width="220">Đặc điểm</th>
              <th width="150">Danh mục</th>
              <th width="110">Hình</th>
              <th width="230">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $item)
              <tr title="{{ $item->name }}">
                <td><span class="badge bg-primary fw-bold">{{ $products->firstItem() + $loop->index }}</span></td>
                <td class="fw-semibold ellipsis-24">{{ $item->name }}</td>
                <td class="text-start col-desc ellipsis-24" title="{{ $item->description }}">{{ $item->description }}</td>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="price">{{ number_format($item->price, 0, ',', '.') }} <small>VND</small></td>
                <td class="text-start col-features ellipsis-24" title="{{ $item->features }}">{{ $item->features }}</td>
                <td class="fw-semibold ellipsis-16" title="{{ $item->category->name ?? '' }}">{{ $item->category->name ?? '' }}</td>
                <td>
                  @if($item->image)
                    <img src="{{ asset('storage/'.$item->image) }}" class="img-thumb" alt="Hình ảnh">
                  @else
                    <span class="text-muted">Không</span>
                  @endif
                </td>
                <td>
                  {{-- Hành động: inline ở màn lớn, dropdown ở màn nhỏ --}}
                  <div class="d-none d-xl-inline">
                    <a class="btn btn-view btn-sm btn-pill me-1" href="{{ route('products.show', $item->id) }}">Xem</a>
                    <a class="btn btn-edit btn-sm btn-pill me-1" href="{{ route('products.edit', $item->id) }}">Sửa</a>
                    <form action="{{ route('products.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-del btn-sm btn-pill"
                              onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                    </form>
                  </div>

                  <div class="dropdown d-xl-none">
                    <button class="btn btn-view btn-sm dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                      <li><a class="dropdown-item" href="{{ route('products.show', $item->id) }}">Xem</a></li>
                      <li><a class="dropdown-item" href="{{ route('products.edit', $item->id) }}">Sửa</a></li>
                      <li>
                        <form action="{{ route('products.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                          @csrf @method('DELETE')
                          <button class="dropdown-item text-danger">Xóa</button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="9"><em>Chưa có sản phẩm</em></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-2">
        {{ $products->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>

    @push('scripts')
    <script>
      (function(){
        const root = document.querySelector('.tv-card');
        const btn  = document.getElementById('btnDensity');
        if(!root || !btn) return;
        let dense = false;
        btn.addEventListener('click', () => {
          dense = !dense;
          root.classList.toggle('dense', dense);
          btn.textContent = 'Mật độ: ' + (dense ? 'Gọn' : 'Chuẩn');
        });
      })();
    </script>
    @endpush

  @endif
@endguest
@endsection
