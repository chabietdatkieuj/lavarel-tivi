@extends('layouts.app')
@section('title','Xác thực email')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4 text-center">
        <h2 class="fw-bold mb-3 text-dark">📧 Xác thực Email</h2>
        <p class="text-muted mb-4">
          Chúng tôi đã gửi liên kết xác thực đến<br>
          <strong>{{ auth()->user()->email }}</strong>.<br>
          Vui lòng kiểm tra hộp thư (kể cả mục Spam).
        </p>

        @if (session('message'))
          <div class="alert alert-success">{{ session('message') }}</div>
        @endif>

        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn btn-primary btn-lg px-4">
            Gửi lại email xác thực
          </button>
        </form>

        {{-- Tùy chọn: liên kết chỉnh email, có thể bỏ nếu không dùng --}}
        {{-- <p class="mt-3 text-muted">
          Sai email? <a href="{{ route('profile.edit') }}" class="fw-semibold text-decoration-none">Cập nhật lại</a>
        </p> --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .card{ border-radius:16px; }
</style>
@endpush
