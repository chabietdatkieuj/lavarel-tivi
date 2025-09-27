@extends('layouts.app')
@section('title','Quản lý Voucher')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold">🎟️ Voucher</h2>
  <a href="{{ route('admin.vouchers.create') }}" class="btn btn-gold">+ Thêm</a>
</div>



@if($vouchers->isEmpty())
  <div class="alert alert-info">Chưa có voucher.</div>
@else
  <div class="table-responsive">
    <table class="table table-hover text-center align-middle">
      <thead>
        <tr>
          <th>#</th><th>Mã</th><th>% Giảm</th><th>Còn</th><th>Bắt đầu</th><th>Kết thúc</th><th>TT</th><th>HĐ</th>
        </tr>
      </thead>
      <tbody>
        @foreach($vouchers as $v)
          <tr>
            <td>{{ $v->id }}</td>
            <td><code>{{ $v->code }}</code></td>
            <td>{{ $v->discount_percent }}%</td>
            <td>{{ $v->quantity }}</td>
            <td>{{ $v->start_at->format('d/m/Y H:i') }}</td>
            <td>{{ $v->end_at->format('d/m/Y H:i') }}</td>
            <td>{!! $v->is_active ? '<span class="badge bg-success">on</span>' : '<span class="badge bg-secondary">off</span>' !!}</td>
            <td>
              <a class="btn btn-sm btn-info" href="{{ route('admin.vouchers.edit',$v->id) }}">Sửa</a>
              <form action="{{ route('admin.vouchers.destroy',$v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Xóa</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-2">
    {{ $vouchers->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
@endif
@endsection
