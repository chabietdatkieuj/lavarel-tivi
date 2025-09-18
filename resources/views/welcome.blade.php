@extends('layouts.app')
@section('title', 'TV Store - Trang chủ')

@push('styles')
<style>
  :root{
    --bg-50:#f7f8fb; --surface:#ffffff; --border:#e5e7eb;
    --text-900:#111827; --text-600:#4b5563; --text-400:#94a3b8;
    --primary-600:#2563eb; --primary-700:#1e40af; --accent-500:#fbbf24;
  }

  /* ===== Banner (thêm mới) ===== */
  .tv-banner{
    position:relative; overflow:hidden; border-radius:18px; margin-bottom:24px;
    border:1px solid var(--border); background:#fff;
    box-shadow:0 8px 24px rgba(17,24,39,.08); height: 360px;
  }
  .tv-banner .slides{ position:relative; width:100%; height:100%; }
  .tv-banner .slide{
    position:absolute; inset:0; opacity:0; transition:opacity .6s ease;
    background-size:cover; background-position:center;
  }
  .tv-banner .slide.is-active{ opacity:1; }
  .tv-banner .nav{
    position:absolute; top:50%; transform:translateY(-50%);
    z-index:10; background:rgba(0,0,0,.38); color:#fff; border:none;
    width:42px; height:42px; border-radius:50%; display:grid; place-items:center;
    cursor:pointer;
  }
  .tv-banner .nav:hover{ background:rgba(0,0,0,.55); }
  .tv-banner .prev{ left:14px; } .tv-banner .next{ right:14px; }

  .tv-banner .dots{
    position:absolute; left:0; right:0; bottom:12px; display:flex; gap:8px;
    justify-content:center; z-index:10;
  }
  .tv-banner .dot{
    width:8px; height:8px; border-radius:999px; background:rgba(255,255,255,.6);
    border:1px solid rgba(0,0,0,.15);
  }
  .tv-banner .dot.is-active{ background:#fff; }

  /* ========= HERO ========= */
  .hero-box{
    position:relative; overflow:hidden; border-radius:18px; padding:48px 26px;
    background:
      radial-gradient(900px 480px at 90% -20%, rgba(37,99,235,.10), transparent 60%),
      radial-gradient(700px 420px at -10% 0%, rgba(37,99,235,.08), transparent 58%),
      linear-gradient(180deg, #eef2ff, #ffffff);
    color:var(--text-900);
    border:1px solid var(--border);
    box-shadow:0 8px 24px rgba(17,24,39,.08);
  }
  .hero-title{ font-weight:900; line-height:1.1; font-size:clamp(2rem,1.2rem + 2.5vw,3rem) }
  .hero-sub{ color:var(--text-600); max-width:760px; font-size:1.04rem }

  .btn-cta{ padding:.85rem 1.1rem; border-radius:12px; font-weight:800; letter-spacing:.2px }
  .btn-cta-primary{ background:linear-gradient(135deg,var(--primary-600),#60a5fa); color:#fff; border:none; }
  .btn-cta-primary:hover{ filter:brightness(.97) }
  .btn-cta-ghost{ background:#ffffff; border:1px solid var(--border); color:var(--text-900); }
  .btn-cta-ghost:hover{ background:#f9fafb }

  /* ========= CATEGORY CHIPS ========= */
  .chip{
    display:inline-flex; align-items:center; gap:.45rem; padding:.5rem .8rem; border-radius:999px;
    color:var(--text-600); background:#fff; border:1px solid var(--border); text-decoration:none;
    transition:.2s; white-space:nowrap; box-shadow:0 2px 6px rgba(17,24,39,.04);
  }
  .chip:hover{ background:#eef2ff; color:var(--primary-700); border-color:#dbe3ff }

  /* ========= PRODUCT GRID ========= */
  .shop-grid{ margin-top:.25rem }
  .card-product{
    background:var(--surface); border:1px solid var(--border); border-radius:14px;
    box-shadow:0 8px 20px rgba(17,24,39,.06); transition:.18s; display:flex; flex-direction:column; overflow:hidden;
  }
  .card-product:hover{ transform:translateY(-2px); box-shadow:0 12px 26px rgba(17,24,39,.10) }
  .cp-img-wrap{ height:200px; border-bottom:1px solid var(--border); background:#f3f4f6 }
  .cp-img{ width:100%; height:100%; object-fit:cover; display:block }
  .cp-body{ padding:14px 16px; flex:1; min-height:90px }
  .cp-title{ font-weight:800; color:var(--text-900); margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:30ch }
  .cp-price{ color:var(--primary-700); font-weight:900; letter-spacing:.2px }
  .cp-cat{ color:var(--text-400); font-size:.86rem }
  .cp-actions{ padding:0 16px 16px; display:flex; gap:.5rem }
  .btn-cart{ background:linear-gradient(90deg,#42e695,#3bb2b8); border:none; color:#083040; font-weight:800 }
  .btn-cart:hover{ filter:brightness(.95) }

  /* ========= SERVICE STRIP ========= */
  .service-card{
    background:#ffffff; color:var(--text-900); padding:18px; border-radius:12px;
    border:1px solid var(--border); display:flex; gap:.9rem; align-items:flex-start;
    box-shadow:0 6px 16px rgba(17,24,39,.06);
  }
  .service-ic{
    width:42px; height:42px; border-radius:10px; display:grid; place-items:center;
    background:linear-gradient(135deg,#60a5fa,var(--primary-600)); color:#fff; font-weight:900;
    box-shadow: 0 4px 10px rgba(37,99,235,.25);
  }

  /* ========= FEATURE PANEL ========= */
  .panel-feature{
    position:relative; padding:16px 16px 18px; border-radius:14px;
    background: linear-gradient(180deg,#ffffff,#f9fafb);
    border:1px solid var(--border); box-shadow:0 8px 20px rgba(17,24,39,.06);
  }
  .f-head{ display:flex; align-items:center; gap:.8rem; margin-bottom:10px; }
  .f-badge{ width:48px; height:48px; border-radius:12px; display:grid; place-items:center; color:#fff; font-weight:900; font-size:18px; background:linear-gradient(135deg,var(--primary-600),#60a5fa); box-shadow:0 6px 14px rgba(37,99,235,.3); }
  .f-title{ color:var(--text-900); font-weight:800; font-size:1.05rem; margin-bottom:2px }
  .f-sub  { color:var(--text-600); font-size:.9rem }
  .benefit-col{ display:flex; gap:.8rem }
  .pill{
    flex:1; min-height:190px; display:flex; flex-direction:column; gap:.35rem; padding:14px; border-radius:12px;
    color:var(--text-900); background:#ffffff; border:1px solid var(--border);
    box-shadow:0 6px 14px rgba(17,24,39,.06); transition:transform .18s ease, box-shadow .18s ease;
  }
  .pill:hover{ transform:translateY(-2px); box-shadow:0 10px 22px rgba(17,24,39,.10) }
  .pill-dot{ width:26px;height:26px;border-radius:999px;display:grid;place-items:center;font-size:.78rem;font-weight:900;color:#fff;background:linear-gradient(135deg,#60a5fa,var(--primary-600)); box-shadow:0 4px 10px rgba(37,99,235,.28); }
  .pill-title{ font-weight:800; color:var(--text-900); margin:2px 0 2px }
  .pill-desc { font-size:.95rem; line-height:1.35; color:var(--text-600) }
  @media (max-width: 991.98px){
    .benefit-col{ flex-direction:column }
    .tv-banner{ height: 240px; }
  }
</style>
@endpush

@section('content')

@php
  // Fallback nếu controller chưa truyền dữ liệu
  $hotCategories    = $hotCategories    ?? \App\Models\Category::latest()->take(6)->get();
  $featuredProducts = $featuredProducts ?? \App\Models\Product::latest()->take(8)->get();
  $isAdmin = auth()->check() && (auth()->user()->role === 'admin');

  // Banner: có thể truyền $banners từ controller; nếu không dùng mặc định dưới
  $banners = $banners ?? [
    'https://cdn.nguyenkimmall.com/images/companies/_1/MKT_ECM/0925/Japan%20month/Cate-AV-897x350.jpg',
    'https://cdn.nguyenkimmall.com/images/companies/_1/MKT_ECM/0825/dealsheet/av/897x350.jpg',
    'https://cdn.nguyenkimmall.com/images/companies/_1/MKT_ECM/0925/dealsheet/av/NK%20879x350.jpg',
  ];
@endphp

{{-- ===== Banner Carousel (mới) ===== --}}
@if(!empty($banners))
<div id="homeBanner" class="tv-banner" data-interval="4500">
  <div class="slides">
    @foreach($banners as $idx => $url)
      <div class="slide {{ $idx===0?'is-active':'' }}" style="background-image:url('{{ $url }}')"></div>
    @endforeach
  </div>
  <button class="nav prev" aria-label="Ảnh trước">&#10094;</button>
  <button class="nav next" aria-label="Ảnh sau">&#10095;</button>
  <div class="dots">
    @foreach($banners as $idx => $url)
      <span class="dot {{ $idx===0?'is-active':'' }}" data-index="{{ $idx }}"></span>
    @endforeach
  </div>
</div>
@endif

{{-- ========= HERO ========= --}}
<div class="hero-box mb-4">
  <div class="row align-items-center">
    <div class="col-lg-7">
      <h1 class="hero-title mb-3">
        Nâng tầm trải nghiệm <span class="text-primary">TV Store</span>
      </h1>
      <p class="hero-sub">
        Kho Tivi mới nhất, hình ảnh sắc nét, âm thanh sống động.
        @guest Đăng nhập/đăng ký để bắt đầu mua sắm ngay hôm nay!
        @else Quản lý danh mục, sản phẩm và đặt hàng nhanh chóng. @endguest
      </p>

      <div class="mt-4 d-flex gap-2 flex-wrap">
        @guest
          <a href="{{ route('login') }}" class="btn btn-cta btn-cta-primary">🔐 Đăng nhập</a>
          <a href="{{ route('register') }}" class="btn btn-cta btn-cta-ghost">📝 Đăng ký</a>
        @else
          @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}" class="btn btn-cta btn-cta-primary">⚙️ Bảng điều khiển</a>
            <a href="{{ route('products.index') }}" class="btn btn-cta btn-cta-ghost">🛒 Quản lý sản phẩm</a>
          @else
            <a href="{{ route('categories.index') }}" class="btn btn-cta btn-cta-primary">📂 Vào Danh mục</a>
            <a href="{{ route('products.index') }}" class="btn btn-cta btn-cta-ghost">🛒 Xem Sản phẩm</a>
          @endif
        @endguest
      </div>
    </div>

    {{-- Panel ưu đãi --}}
    <div class="col-lg-5 mt-4 mt-lg-0">
      <div class="panel-feature">
        <div class="f-head">
          <div class="f-badge">TV</div>
          <div>
            <div class="f-title">Siêu ưu đãi</div>
            <div class="f-sub">Hàng mới mỗi ngày • Bảo hành 24 tháng</div>
          </div>
        </div>

        <div class="benefit-col">
          <div class="pill">
            <div class="d-flex align-items-center gap-2">
              <span class="pill-dot">4K</span>
              <div class="pill-title">Màn hình 4K/8K</div>
            </div>
            <div class="pill-desc">Hình ảnh sắc nét, màu sắc rực rỡ cho mọi nội dung.</div>
          </div>

          <div class="pill">
            <div class="d-flex align-items-center gap-2">
              <span class="pill-dot">AI</span>
              <div class="pill-title">Tối ưu AI</div>
            </div>
            <div class="pill-desc">Nâng cấp hình ảnh & âm thanh theo thời gian thực.</div>
          </div>

          <div class="pill">
            <div class="d-flex align-items-center gap-2">
              <span class="pill-dot">♪</span>
              <div class="pill-title">Âm thanh Dolby</div>
            </div>
            <div class="pill-desc">Trải nghiệm rạp hát tại gia với Dolby Atmos/DTS:X.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ========= DANH MỤC NỔI BẬT ========= --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
  <h2 class="mb-0 fw-bold">📚 Danh mục nổi bật</h2>
  <a href="{{ route('categories.index') }}" class="btn btn-sm btn-cta-ghost">Xem tất cả</a>
</div>
<div class="d-flex flex-wrap gap-2 mb-4">
  @forelse($hotCategories as $c)
    <a class="chip" href="{{ route('categories.show', $c->id) }}">📁 {{ $c->name }}</a>
  @empty
    <span class="text-muted">Chưa có danh mục.</span>
  @endforelse
</div>

{{-- ========= SẢN PHẨM NỔI BẬT ========= --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
  <h2 class="mb-0 fw-bold">🔥 Sản phẩm nổi bật</h2>
  <a href="{{ route('products.index') }}" class="btn btn-sm btn-cta-ghost">Xem tất cả</a>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 shop-grid">
  @forelse($featuredProducts as $p)
    @php $img = $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/420x260?text=TV+Store'; @endphp
    <div class="col">
      <div class="card-product">
        <div class="cp-img-wrap"><img class="cp-img" src="{{ $img }}" alt="{{ $p->name }}"></div>
        <div class="cp-body">
          <div class="cp-title">{{ $p->name }}</div>
          <div class="cp-cat">{{ $p->category->name ?? 'Chưa phân loại' }}</div>
          <div class="cp-price mt-1">{{ number_format($p->price,0,',','.') }} VND</div>
        </div>
        <div class="cp-actions">
          <a href="{{ route('products.show', $p->id) }}" class="btn btn-outline-primary w-50">Xem</a>
          @auth
            @unless($isAdmin)
              <form action="{{ route('cart.add', $p->id) }}" method="POST" class="w-50">
                @csrf
                <button class="btn btn-cart w-100">+ Thêm giỏ</button>
              </form>
            @endunless
          @endauth
          @guest
            <a href="{{ route('login') }}" class="btn btn-cart w-50">+ Thêm giỏ</a>
          @endguest
        </div>
      </div>
    </div>
  @empty
    <div class="col"><div class="alert alert-info">Chưa có sản phẩm.</div></div>
  @endforelse
</div>

{{-- ========= DẢI DỊCH VỤ ========= --}}
<div class="row g-3 mt-2">
  <div class="col-md-3 col-6">
    <div class="service-card">
      <div class="service-ic">🚚</div>
      <div>
        <div class="fw-bold">Miễn phí vận chuyển</div>
        <small class="text-muted">Áp dụng cho đơn ≥ 1.000.000đ</small>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="service-card">
      <div class="service-ic">↩</div>
      <div>
        <div class="fw-bold">Đổi trả 7 ngày</div>
        <small class="text-muted">Hỗ trợ đổi trả nhanh chóng</small>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="service-card">
      <div class="service-ic">🛡</div>
      <div>
        <div class="fw-bold">Bảo hành 24 tháng</div>
        <small class="text-muted">Chính hãng, an tâm sử dụng</small>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="service-card">
      <div class="service-ic">💬</div>
      <div>
        <div class="fw-bold">Hỗ trợ 24/7</div>
        <small class="text-muted">Chat & hotline luôn sẵn sàng</small>
      </div>
    </div>
  </div>
</div>

{{-- ========= CTA ========= --}}
<div class="mt-4 p-4 rounded-3" style="background:#ffffff;border:1px solid var(--border);box-shadow:0 6px 16px rgba(17,24,39,.06);">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
      <h5 class="mb-1 fw-bold">Sẵn sàng khám phá?</h5>
      <div class="text-muted">Hơn 100+ mẫu Tivi đang sẵn hàng với nhiều ưu đãi hấp dẫn.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('categories.index') }}" class="btn btn-cta btn-cta-primary">📂 Khám phá danh mục</a>
      <a href="{{ route('products.index') }}" class="btn btn-cta btn-cta-ghost">🛍 Xem tất cả sản phẩm</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const root = document.getElementById('homeBanner');
  if(!root) return;

  const slides = root.querySelectorAll('.slide');
  const dots   = root.querySelectorAll('.dot');
  const prev   = root.querySelector('.prev');
  const next   = root.querySelector('.next');
  const delay  = parseInt(root.dataset.interval || '4500', 10);

  let i = 0, timer;

  function go(n){
    slides[i].classList.remove('is-active');
    dots[i]?.classList.remove('is-active');
    i = (n + slides.length) % slides.length;
    slides[i].classList.add('is-active');
    dots[i]?.classList.add('is-active');
  }
  function start(){ timer = setInterval(()=>go(i+1), delay); }
  function stop(){ clearInterval(timer); }

  prev.addEventListener('click', ()=>{ go(i-1); stop(); start(); });
  next.addEventListener('click', ()=>{ go(i+1); stop(); start(); });
  dots.forEach(d=> d.addEventListener('click', e => { go(parseInt(d.dataset.index,10)); stop(); start(); }));

  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);

  start();
})();
</script>
@endpush
