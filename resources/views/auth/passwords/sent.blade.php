@extends('layouts.app')
@section('title','Đã gửi yêu cầu đặt lại mật khẩu')

@section('content')
<div class="card p-4">
  <h3 class="fw-bold mb-2">📧 Yêu cầu đặt lại mật khẩu đã được gửi</h3>

  <p class="mb-2">
    Chúng tôi đã gửi một email đến địa chỉ của bạn
    @if(session('reset_email'))
      <strong>{{ session('reset_email') }}</strong>
    @else
      <strong>(email đã nhập)</strong>
    @endif
    với liên kết để đặt lại mật khẩu. Vui lòng kiểm tra hộp thư (kể cả mục Spam).
  </p>

  <div class="mt-3 d-flex gap-2">
    <a class="btn btn-gold" href="{{ route('login') }}">⬅️ Quay lại đăng nhập</a>

    {{-- Nếu có email trong session thì cho phép bấm Gửi lại ngay --}}
    @if(session('reset_email'))
      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('reset_email') }}">
        <button class="btn btn-outline-secondary" type="submit">🔁 Gửi lại yêu cầu</button>
      </form>
    @else
      {{-- Không có email thì dẫn về form nhập email --}}
      <a class="btn btn-outline-secondary" href="{{ route('password.request') }}">🔁 Gửi lại yêu cầu</a>
    @endif
  </div>

  @if(session('status'))
    <div class="alert alert-success mt-3 mb-0">{{ session('status') }}</div>
  @endif
</div>
@endsection
