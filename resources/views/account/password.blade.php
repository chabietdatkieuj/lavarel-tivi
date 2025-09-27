@extends('layouts.app')
@section('title','Đổi mật khẩu')

@section('content')
<div class="container py-3">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card p-3">
    <h5 class="mb-3">Đổi mật khẩu</h5>
    <form method="POST" action="{{ route('account.password.update') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Mật khẩu hiện tại</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Mật khẩu mới (≥ 6 ký tự)</label>
        <input type="password" name="new_password" class="form-control" minlength="6" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nhập lại mật khẩu mới</label>
        <input type="password" name="new_password_confirmation" class="form-control" required>
      </div>
      <button class="btn btn-primary">Cập nhật mật khẩu</button>
    </form>
  </div>
</div>
@endsection
