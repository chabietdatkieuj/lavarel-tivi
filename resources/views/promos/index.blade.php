@extends('layouts.app')
@section('title','🎟️ Khuyến mãi')

@section('content')
<h2 class="fw-bold mb-3">🎟️ Mã giảm giá đang chạy</h2>

@if($vouchers->isEmpty())
  <div class="alert alert-info">Hiện chưa có mã nào.</div>
@else
  <div class="row g-3">
    @foreach($vouchers as $v)
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-1">Mã: <code>{{ $v->code }}</code></h5>
              <span class="badge bg-success">{{ $v->discount_percent }}%</span>
            </div>
            <div class="small text-muted mb-2">
              Từ {{ $v->start_at->format('d/m/Y H:i') }} đến {{ $v->end_at->format('d/m/Y H:i') }}
            </div>
            <div>Còn lại: <strong>{{ $v->quantity }}</strong></div>
            <div class="mt-2 small text-muted">Sao chép mã và nhập ở bước Thanh toán.</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
@endsection
