@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chi tiết sản phẩm</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

                        {{-- ✅ Thêm vào giỏ hàng --}}
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
</div>
@endsection
