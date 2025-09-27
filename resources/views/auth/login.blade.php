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
                   value="{{ old('email') }}" required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password"
                   class="form-control"
                   placeholder="Nhập mật khẩu" required>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="mb-0 text-muted">
              Chưa có tài khoản?
              <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Đăng ký ngay</a>
            </p>

            {{-- Quên mật khẩu: auto gửi về đúng email đã nhập --}}
            <a href="#" id="forgotBtn" class="small">Quên mật khẩu?</a>
          </div>

          <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng nhập</button>
        </form>

        {{-- Form ẩn để gửi yêu cầu đặt lại mật khẩu bằng email trong ô phía trên --}}
        <form id="forgotForm" method="POST" action="{{ route('password.email') }}" class="d-none">
          @csrf
          <input type="hidden" name="email" id="forgotEmail">
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

@push('scripts')
<script>
(function(){
  const forgotBtn   = document.getElementById('forgotBtn');
  const emailInput  = document.querySelector('input[name="email"]');
  const forgotForm  = document.getElementById('forgotForm');
  const hiddenEmail = document.getElementById('forgotEmail');

  if (forgotBtn) {
    forgotBtn.addEventListener('click', function(e){
      e.preventDefault();
      const email = (emailInput?.value || '').trim();

      // Chưa nhập email -> đưa sang trang nhập email chuẩn để tránh lỗi
      if(!email){
        window.location.href = "{{ route('password.request') }}";
        return;
      }

      // Đã nhập email -> gửi thẳng yêu cầu reset đến email đó
      hiddenEmail.value = email;
      forgotForm.submit();
    });
  }
})();
</script>
@endpush
