@extends('layouts.app')
@section('title','Đặt lại mật khẩu')

@section('content')
<h2 class="mb-3 fw-bold">🔒 Đặt lại mật khẩu</h2>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="card p-3">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ request('email') }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Mật khẩu mới (≥ 6 ký tự)</label>
    <input type="password" name="password" class="form-control" minlength="6" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Nhập lại mật khẩu mới</label>
    <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
  </div>
  <button class="btn btn-gold">Cập nhật mật khẩu</button>
  <a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Quay lại đăng nhập</a>
</form>
@endsection
