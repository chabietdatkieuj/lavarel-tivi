@extends('layouts.app')
@section('title','Địa chỉ của tôi')

@section('content')
<h4 class="mb-3 fw-bold">📍 Địa chỉ của tôi</h4>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card p-3">
      <h6 class="fw-bold mb-2">Thêm địa chỉ mới</h6>
      <form method="POST" action="{{ route('account.addresses.store') }}">
        @csrf
        <div class="mb-2">
          <label class="form-label">Tên người nhận</label>
          <input name="receiver_name" class="form-control" required value="{{ old('receiver_name', auth()->user()->name) }}">
        </div>
        <div class="mb-2">
          <label class="form-label">Số điện thoại</label>
          <input name="receiver_phone" class="form-control" required value="{{ old('receiver_phone', auth()->user()->phone) }}">
        </div>
        <div class="mb-2">
          <label class="form-label">Địa chỉ đầy đủ</label>
          <input name="full_address" class="form-control" required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault">
          <label class="form-check-label" for="isDefault">Đặt làm địa chỉ mặc định</label>
        </div>
        <button class="btn btn-primary">Thêm</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card p-3">
      <h6 class="fw-bold mb-2">Danh sách địa chỉ</h6>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Người nhận</th><th>Điện thoại</th><th>Địa chỉ</th><th>Mặc định</th><th class="text-end">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($addresses as $ad)
              <tr>
                <td>{{ $ad->receiver_name }}</td>
                <td>{{ $ad->receiver_phone }}</td>
                <td>{{ $ad->full_address }}</td>
                <td>{!! $ad->is_default ? '<span class="badge bg-success">Mặc định</span>' : '' !!}</td>
                <td class="text-end">
                  {{-- Sửa inline (modal đơn giản bằng collapse) --}}
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit{{$ad->id}}">Sửa</button>
                  <form action="{{ route('account.addresses.destroy',$ad) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Xóa địa chỉ này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                  </form>
                  @unless($ad->is_default)
                    <form action="{{ route('account.addresses.default',$ad) }}" method="POST" class="d-inline">
                      @csrf @method('PATCH')
                      <button class="btn btn-sm btn-outline-primary">Đặt mặc định</button>
                    </form>
                  @endunless
                </td>
              </tr>
              <tr class="collapse" id="edit{{$ad->id}}">
                <td colspan="5">
                  <form method="POST" action="{{ route('account.addresses.update',$ad) }}" class="row g-2">
                    @csrf @method('PUT')
                    <div class="col-md-3"><input name="receiver_name" class="form-control" value="{{ $ad->receiver_name }}" required></div>
                    <div class="col-md-3"><input name="receiver_phone" class="form-control" value="{{ $ad->receiver_phone }}" required></div>
                    <div class="col-md-4"><input name="full_address" class="form-control" value="{{ $ad->full_address }}" required></div>
                    <div class="col-md-2 d-grid"><button class="btn btn-primary">Lưu</button></div>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">Chưa có địa chỉ.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
