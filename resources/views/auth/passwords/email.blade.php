@extends('layouts.app')
@section('title','Quên mật khẩu')

@section('content')
<h2 class="mb-3 fw-bold">🔑 Quên mật khẩu</h2>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="card p-3">
  @csrf
  <div class="mb-3">
    <label class="form-label">Email đã đăng ký</label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
  </div>
  <button class="btn btn-gold">Gửi liên kết đặt lại mật khẩu</button>
  <a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Quay lại đăng nhập</a>
</form>
@endsection
