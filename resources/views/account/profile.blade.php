@extends('layouts.app')
@section('title','Đổi thông tin')

@push('styles')
<style>
.card{border-radius:12px}
</style>
@endpush

@section('content')
<div class="container py-3">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card p-3">
    <h5 class="mb-3">Cập nhật thông tin</h5>
    <form method="POST" action="{{ route('account.profile.update') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Họ tên</label>
        <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}" placeholder="0xxxxxxxxx">
      </div>
      <button class="btn btn-primary">Lưu thay đổi</button>
    </form>
  </div>
</div>
@endsection
