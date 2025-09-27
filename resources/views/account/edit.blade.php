{{-- resources/views/account/edit.blade.php --}}
@extends('layouts.app')
@section('title','Thông tin tài khoản')

@section('content')
<h2 class="fw-bold mb-3">👤 Thông tin tài khoản</h2>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
  <div class="col-lg-7">
    <form method="POST" action="{{ route('account.update') }}" class="card p-3">
      @csrf @method('PATCH')
      <div class="mb-3">
        <label class="form-label">Họ tên</label>
        <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email (không đổi)</label>
        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}">
      </div>

      <div class="mb-3">
        <label class="form-label">Địa chỉ hiện tại</label>
        <input type="text" name="address" class="form-control" value="{{ old('address',$user->address) }}">
      </div>

      <button class="btn btn-primary">Lưu thay đổi</button>
    </form>
  </div>

  <div class="col-lg-5">
    <div class="card p-3">
      <h5 class="mb-2">💡 Mẹo</h5>
      <div class="text-muted">
        Cập nhật <strong>địa chỉ</strong> và <strong>số điện thoại</strong> ở đây để khi Thanh toán bạn có thể chọn nhanh “Địa chỉ hiện tại” mà không cần gõ lại.
      </div>
    </div>
  </div>
</div>
@endsection
