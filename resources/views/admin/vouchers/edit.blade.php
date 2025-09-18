@extends('layouts.app')
@section('title','Sửa Voucher')

@section('content')
<h2 class="fw-bold mb-3">✏️ Sửa voucher</h2>
<form method="POST" action="{{ route('admin.vouchers.update',$voucher->id) }}" class="card p-3">
  @csrf @method('PUT')
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Mã</label>
      <input name="code" class="form-control" value="{{ $voucher->code }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">% giảm</label>
      <input name="discount_percent" type="number" min="1" max="100" class="form-control" value="{{ $voucher->discount_percent }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Số lượng</label>
      <input name="quantity" type="number" min="0" class="form-control" value="{{ $voucher->quantity }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Kích hoạt</label><br>
      <input type="checkbox" name="is_active" value="1" {{ $voucher->is_active ? 'checked' : '' }}> Đang bật
    </div>
    <div class="col-md-6">
      <label class="form-label">Bắt đầu</label>
      <input name="start_at" type="datetime-local" class="form-control" value="{{ $voucher->start_at->format('Y-m-d\TH:i') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Kết thúc</label>
      <input name="end_at" type="datetime-local" class="form-control" value="{{ $voucher->end_at->format('Y-m-d\TH:i') }}" required>
    </div>
  </div>
  <div class="mt-3">
    <button class="btn btn-gold">Lưu</button>
    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Quay lại</a>
  </div>
</form>
@endsection
