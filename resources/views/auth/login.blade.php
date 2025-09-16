@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <h2 class="mb-3 fw-bold text-dark text-center">🔑 Đăng nhập</h2>

        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
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

        <form action="{{ route('login.post') }}" method="POST" novalidate>
          @csrf

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control"
                   placeholder="Nhập email"
                   value="{{ old('email') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password"
                   class="form-control"
                   placeholder="Nhập mật khẩu" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng nhập</button>

          <p class="text-center mt-3 text-muted">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Đăng ký ngay</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .card{ border-radius:16px; }
  .form-control{ border-radius:12px; padding:.75rem 1rem; }
</style>
@endpush
