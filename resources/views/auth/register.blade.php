@extends('layouts.app')
@section('title', 'Đăng ký')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <h2 class="fw-bold mb-3 text-dark text-center">📝 Đăng ký tài khoản</h2>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" novalidate>
          @csrf

          <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name') }}" placeholder="Nhập họ tên" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="Nhập email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Ít nhất 6 ký tự" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Nhập lại mật khẩu" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng ký</button>

          <p class="text-center mt-3 text-muted">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Đăng nhập</a>
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
