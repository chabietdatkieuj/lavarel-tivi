@extends('layouts.app')
@section('title','Đổi mật khẩu')

@push('styles')
<style>
  .panel-nice{background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 8px 20px rgba(17,24,39,.06)}
  .form-control{border-radius:10px}
</style>
@endpush

@section('content')
<div class="panel-nice p-4">
  <h3 class="fw-bold mb-3">🔒 Đổi mật khẩu</h3>

  

  <form action="{{ route('account.password.update') }}" method="POST" class="row g-3" novalidate>
    @csrf
    @method('PATCH')

    <div class="col-12">
      <label class="form-label">Mật khẩu hiện tại</label>
      <input type="password"
             name="current_password"
             class="form-control @error('current_password') is-invalid @enderror"
             required>
      @error('current_password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-12">
      <label class="form-label">Mật khẩu mới</label>
      <input type="password"
             name="new_password"
             class="form-control @error('new_password') is-invalid @enderror"
             minlength="6" required>
      @error('new_password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-12">
      <label class="form-label">Nhập lại mật khẩu mới</label>
      <input type="password"
             name="new_password_confirmation"
             class="form-control"
             minlength="6" required>
    </div>

    <div class="col-12">
      <button class="btn btn-primary">Cập nhật mật khẩu</button>
      <a href="{{ route('account.edit') }}" class="btn btn-outline-secondary ms-1">↩ Quay lại tài khoản</a>
    </div>
  </form>
</div>
@endsection
