<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TV Store')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ====== PALETTE: Light, Neutral ====== */
        :root{
            --bg-50:#f7f8fb;           /* page background */
            --surface:#ffffff;         /* cards/panels/nav */
            --border:#e5e7eb;          /* borders */
            --text-900:#111827;        /* primary text */
            --text-600:#4b5563;        /* secondary text */

            --navy-800:#1e3a8a;        /* navy for header/footer */
            --navy-700:#1e40af;

            --primary-600:#2563eb;     /* blue accent for buttons */
            --accent-500:#fbbf24;      /* gold accent */

            --shadow-1:0 8px 24px rgba(17,24,39,.08);
        }

        html,body{height:100%}
        body{
            min-height:100vh; display:flex; flex-direction:column;
            background: var(--bg-50);
            color:var(--text-900);
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        /* ===== NAVBAR (navy gradient) ===== */
        .navbar{
            background:linear-gradient(90deg,var(--navy-800),var(--navy-700)) !important;
            box-shadow:0 2px 6px rgba(0,0,0,.15);
        }
        .navbar .navbar-toggler{ border-color: rgba(255,255,255,.45) }
        .brand-wrap{ display:flex; align-items:center; gap:.6rem; color:#fff; text-decoration:none; }
        .brand-icon{
            width:34px; height:34px; border-radius:8px;
            background: linear-gradient(135deg, var(--accent-500), #f59e0b);
            display:grid; place-items:center; color:#111; font-weight:800;
            box-shadow:0 4px 12px rgba(251,191,36,.35);
        }
        .brand-text{ font-weight:800; letter-spacing:.4px; color:#fff }

        .nav-link{
            color:#e0e7ff !important; font-weight:600; position:relative;
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.7rem .85rem;
        }
        .nav-link:hover{ color:#fff !important }
        .nav-link.active{ color:#fff !important; }
        .nav-link.active::after, .nav-link:hover::after{
            content:""; position:absolute; left:.6rem; right:.6rem; bottom:-6px; height:3px;
            background:linear-gradient(90deg,#fbbf24,#fcd34d); border-radius:999px;
        }
        /* Nút outline trong navbar trên nền tối */
        .navbar .btn-outline-secondary{
            color:#fff; border-color:rgba(255,255,255,.65);
        }
        .navbar .btn-outline-secondary:hover{
            color:#111; background:#fff; border-color:#fff;
        }

        /* ===== SIDEBAR (Admin) – giữ sáng như cũ ===== */
        .sidebar{
            width:240px; min-height:calc(100vh - 64px);
            background:#f9fafb;
            border-right:1px solid var(--border);
            position:sticky; top:64px;
        }
        .sidebar .menu-title{
            font-weight:800; color:var(--text-900); letter-spacing:.3px;
        }
        .sidebar .nav-link{
            color:var(--text-600) !important; border-radius:10px; padding:.55rem .75rem;
        }
        .sidebar .nav-link:hover{ background:#e5e7eb; color:var(--text-900) !important; }
        .sidebar .nav-link.active{ background:#e0e7ff; color:#1e40af !important; }

        /* Badge role */
        .role-badge{
            background:linear-gradient(135deg,#fde68a,#f59e0b);
            color:#3b2f00; font-weight:800; border-radius:999px;
            padding:.25rem .6rem; font-size:.8rem;
        }

        /* Cart badge (Customer) */
        .cart-link{ position:relative; display:inline-flex; align-items:center; gap:.35rem; color:#e0e7ff; text-decoration:none; }
        .cart-badge{
            position:absolute; top:-8px; right:-12px;
            background:linear-gradient(135deg,#ef4444,#f59e0b);
            color:#fff; font-weight:900; border-radius:999px; font-size:.72rem; padding:.15rem .35rem;
            box-shadow:0 6px 16px rgba(239,68,68,.25); min-width:20px; text-align:center;
        }

        /* ===== CONTENT ===== */
        .container-main{ flex:1; width:100%; max-width:1200px }
        .panel{
            margin:42px auto 56px; background:var(--surface); border:1px solid var(--border);
            border-radius:16px; padding:28px; box-shadow: var(--shadow-1);
        }
        .panel h1,.panel h2{ font-weight:800; color:var(--text-900) }
        .alert{ border:1px solid var(--border); border-radius:12px; padding:.9rem 1rem; }

        .card, .table{
            background:var(--surface); color:var(--text-900);
            border:1px solid var(--border); border-radius:12px;
        }
        .table thead th{
            color:var(--text-900); border-bottom-color:var(--border);
            background:#f9fafb;
        }
        .table tbody td{ color:var(--text-900) }
        .table img{ border-radius:10px }

        /* ===== FOOTER (navy gradient) ===== */
        footer{
            background: linear-gradient(90deg,var(--navy-800),var(--navy-700));
            border-top:1px solid rgba(255,255,255,.08); color:#e5e7eb;
        }
        footer a{ color:#fcd34d; text-decoration:none }
        footer a:hover{ color:#fff; text-decoration:underline }

        /* Primary CTA */
        .btn-gold{
            background:linear-gradient(135deg,var(--primary-600),#60a5fa);
            color:#fff; border:none; font-weight:700;
        }
        .btn-gold:hover{ filter:brightness(.96) }
    </style>
    @stack('styles')
</head>
<body>

@php
    $isAdmin  = auth()->check() && auth()->user()->role === 'admin';
    $cartItems = session('cart', []);
    $cartQty   = is_array($cartItems) ? collect($cartItems)->sum('quantity') : 0;
@endphp

<nav class="navbar navbar-expand-lg navbar-dark" style="min-height:64px;">
    <div class="container">
        {{-- Logo --}}
        <a class="brand-wrap" href="{{ route('welcome') }}">
            <div class="brand-icon">TV</div>
            <div class="brand-text">TV Store</div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- LEFT --}}
            <ul class="navbar-nav me-auto ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}"
                       href="{{ route('welcome') }}">🏠 Trang chủ</a>
                </li>

                @unless($isAdmin)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                           href="{{ route('categories.index') }}">📂 Danh mục Tivi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                           href="{{ route('products.index') }}">🛍️ Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                           href="{{ route('orders.index') }}">📦 Đơn hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cart-link {{ request()->routeIs('cart.*') ? 'active' : '' }}"
                           href="{{ route('cart.index') }}">
                            🛒 Giỏ hàng
                            @if($cartQty > 0)
                                <span class="cart-badge">{{ $cartQty }}</span>
                            @endif
                        </a>
                    </li>
                @endunless
            </ul>

            {{-- RIGHT --}}
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            👤 <strong>{{ Auth::user()->name }}</strong>
                            @if(!empty(Auth::user()->role))
                                <span class="role-badge ms-2 text-uppercase">{{ Auth::user()->role }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">🚪 Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-gold btn-sm" href="{{ route('register') }}">Đăng ký</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ===== BODY ===== --}}
@if($isAdmin)
    <div class="d-flex">
        <aside class="sidebar p-3 d-none d-lg-block">
            <div class="menu-title mb-3">🛠️ Quản trị</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}">🏷️ Bảng điều khiển</a>

                <div class="mt-2 mb-1 text-uppercase small text-muted">Sản phẩm</div>
                <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}"
                   href="{{ route('products.index') }}">📋 Quản lý sản phẩm</a>

                <div class="mt-3 mb-1 text-uppercase small text-muted">Danh mục</div>
                <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}"
                   href="{{ route('categories.index') }}">📁 Quản lý Danh mục</a>

                <div class="mt-3 mb-1 text-uppercase small text-muted">Khác</div>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                   href="{{ route('admin.orders.index') }}">🧾 Quản lý Đơn hàng</a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   href="{{ route('admin.users.index') }}">👥 Quản lý Người dùng</a>
                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"
                   href="{{ route('admin.reports.index') }}">📑 Báo cáo (bảng)</a>
                <a class="nav-link {{ request()->routeIs('admin.reports.charts') ? 'active' : '' }}"
                   href="{{ route('admin.reports.charts') }}">📈 Biểu đồ</a>
            </nav>
        </aside>

        <div class="flex-grow-1">
            <main class="container container-main">
                <section class="panel">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </section>
            </main>
        </div>
    </div>
@else
    <main class="container container-main">
        <section class="panel">
            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
@endif

<footer class="py-4">
    <div class="container text-center">
        <div class="small mb-1">© {{ date('Y') }} <strong>TV Store</strong>. All rights reserved.</div>
        <div class="small">
            <a href="#">Chính sách bảo hành</a> •
            <a href="#">Liên hệ</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
