@extends('layouts.app')
@section('title','Báo cáo tổng quan')

@section('content')
<h2 class="fw-bold text-dark mb-3">🧾 Báo cáo tổng quan</h2>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="h6 text-muted mb-1">Tổng số đơn hàng</div>
                <div class="fs-3 fw-bold text-primary">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="h6 text-muted mb-1">Tổng khách hàng</div>
                <div class="fs-3 fw-bold text-success">{{ $totalCustomers }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-md-end">
        <a class="btn btn-primary mt-2" href="{{ route('admin.reports.charts') }}">📊 Xem biểu đồ</a>
    </div>
</div>

<hr class="my-4">

{{-- Doanh thu theo Danh mục --}}
<h4 class="text-dark mt-2 mb-2">📂 Doanh thu theo danh mục</h4>
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th>Danh mục</th>
            <th>Tổng SL</th>
            <th>Doanh thu</th>
        </tr>
    </thead>
    <tbody>
    @forelse($categoryRevenue as $r)
        <tr>
            <td class="text-start">{{ $r->category_name }}</td>
            <td>{{ number_format($r->total_qty) }}</td>
            <td class="fw-bold text-primary">{{ number_format($r->total_revenue,0,',','.') }} đ</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-muted">Chưa có dữ liệu.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- Doanh thu theo NGÀY --}}
<h4 class="text-dark mt-4 mb-2">📅 Doanh thu theo ngày</h4>
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th>Ngày</th>
            <th>Số đơn</th>
            <th>Doanh thu</th>
        </tr>
    </thead>
    <tbody>
    @forelse($revenueByDate as $r)
        <tr>
            <td>{{ \Carbon\Carbon::parse($r->d)->format('d/m/Y') }}</td>
            <td>{{ number_format($r->order_count) }}</td>
            <td class="fw-bold text-primary">{{ number_format($r->total_revenue,0,',','.') }} đ</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-muted">Chưa có dữ liệu.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- Doanh thu theo THÁNG --}}
<h4 class="text-dark mt-4 mb-2">🗓️ Doanh thu theo tháng</h4>
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th>Tháng</th>
            <th>Số đơn</th>
            <th>Doanh thu</th>
        </tr>
    </thead>
    <tbody>
    @forelse($revenueByMonth as $r)
        <tr>
            <td>{{ $r->ym }}</td>
            <td>{{ number_format($r->order_count) }}</td>
            <td class="fw-bold text-primary">{{ number_format($r->total_revenue,0,',','.') }} đ</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-muted">Chưa có dữ liệu.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- Doanh thu theo NĂM --}}
<h4 class="text-dark mt-4 mb-2">📆 Doanh thu theo năm</h4>
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th>Năm</th>
            <th>Số đơn</th>
            <th>Doanh thu</th>
        </tr>
    </thead>
    <tbody>
    @forelse($revenueByYear as $r)
        <tr>
            <td>{{ $r->y }}</td>
            <td>{{ number_format($r->order_count) }}</td>
            <td class="fw-bold text-primary">{{ number_format($r->total_revenue,0,',','.') }} đ</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-muted">Chưa có dữ liệu.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
