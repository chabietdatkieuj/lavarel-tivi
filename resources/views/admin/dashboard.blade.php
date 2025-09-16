{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Bảng điều khiển')

@push('styles')
<style>
  /* KPI Cards */
  .kpi-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:18px;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
    height:100%;
  }
  .kpi-card h6{ color:#6b7280; font-weight:700; margin:0; letter-spacing:.35px }
  .kpi-value{ font-size:1.6rem; font-weight:900; color:#111827; margin-top:6px }
  .kpi-sub{ color:#6b7280; font-size:.9rem }

  .kpi-badge{
    display:inline-flex; align-items:center; gap:.45rem;
    background:#f3f4f6; border:1px solid #e5e7eb;
    padding:.35rem .6rem; border-radius:8px;
    color:#374151; font-weight:600; font-size:.85rem;
  }

  /* Order status pills */
  .stt-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:14px 16px;
    display:flex; align-items:center; justify-content:space-between;
    box-shadow:0 2px 6px rgba(0,0,0,.05);
  }
  .stt-name{ color:#374151; font-weight:600 }
  .pill{
    border-radius:999px; padding:.25rem .6rem; font-size:.8rem; font-weight:700;
    color:#111827;
  }
  .bg-pending   { background:#fde68a }
  .bg-process   { background:#93c5fd }
  .bg-ship      { background:#6ee7b7 }
  .bg-delivered { background:#86efac }
  .bg-cancel    { background:#fca5a5 }

  /* Cards */
  .tv-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
  }
  .tv-title{ color:#111827; font-weight:800 }

  /* Table */
  .table-lightish thead th{
    background:#f9fafb; color:#374151; border:0;
    text-transform:uppercase; font-weight:700; font-size:.9rem
  }
  .table-lightish tbody td{
    background:#fff; color:#111827; border-color:#e5e7eb;
    vertical-align:middle!important;
  }
  .price{ color:#2563eb; font-weight:700 }

  .btn-view{ background:#3b82f6; color:#fff; border:none; border-radius:999px; padding:.35rem .8rem }
  .btn-view:hover{ filter:brightness(.95) }

  .btn-add{
    background:linear-gradient(90deg,#fcd34d,#f59e0b);
    border:none; color:#111827;
    font-weight:700; border-radius:8px; padding:.5rem .9rem;
  }
  .btn-add:hover{ filter:brightness(.95) }

  /* Chart box */
  .chart-wrap{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
  {{-- KPIs --}}
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="kpi-card">
        <h6>📦 Sản phẩm</h6>
        <div class="kpi-value">{{ number_format($stats['products']) }}</div>
        <div class="kpi-sub">Tổng số sản phẩm đang có</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="kpi-card">
        <h6>📁 Danh mục</h6>
        <div class="kpi-value">{{ number_format($stats['categories']) }}</div>
        <div class="kpi-sub">Nhóm sản phẩm</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="kpi-card">
        <h6>🧾 Đơn hàng</h6>
        <div class="kpi-value">{{ number_format($stats['orders']) }}</div>
        <div class="kpi-sub">Tổng số đơn đã tạo</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="kpi-card">
        <h6>💰 Doanh thu</h6>
        <div class="kpi-value">{{ number_format($stats['revenue'], 0, ',', '.') }} đ</div>
        <div class="kpi-sub">Từ các đơn đã giao</div>
      </div>
    </div>
  </div>

  {{-- Order status --}}
  <div class="row g-3 mt-2">
    <div class="col-6 col-md"><div class="stt-card"><div class="stt-name">Pending</div><span class="pill bg-pending">{{ $stats['pending'] }}</span></div></div>
    <div class="col-6 col-md"><div class="stt-card"><div class="stt-name">Processing</div><span class="pill bg-process">{{ $stats['processing'] }}</span></div></div>
    <div class="col-6 col-md"><div class="stt-card"><div class="stt-name">Shipping</div><span class="pill bg-ship">{{ $stats['shipping'] }}</span></div></div>
    <div class="col-6 col-md"><div class="stt-card"><div class="stt-name">Delivered</div><span class="pill bg-delivered">{{ $stats['delivered'] }}</span></div></div>
    <div class="col-6 col-md"><div class="stt-card"><div class="stt-name">Cancelled</div><span class="pill bg-cancel">{{ $stats['cancelled'] }}</span></div></div>

    @if(auth()->user()->role === 'admin')
    <div class="col-12 d-flex gap-2 mt-1">
      <a href="{{ route('products.create') }}" class="btn btn-add">+ Thêm sản phẩm</a>
      <a href="{{ route('categories.create') }}" class="btn btn-add">+ Thêm danh mục</a>
      <a href="{{ route('admin.orders.index') }}" class="kpi-badge">Xem tất cả đơn hàng →</a>
    </div>
    @endif
  </div>

  <div class="row g-3 mt-3">
    {{-- Pie chart --}}
    <div class="col-lg-5">
      <div class="tv-card">
        <h5 class="tv-title mb-2">📊 Tỉ lệ trạng thái đơn</h5>
        <div class="chart-wrap"><canvas id="orderStatusPie" height="220"></canvas></div>
      </div>
    </div>

    {{-- Latest orders --}}
    <div class="col-lg-7">
      <div class="tv-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="tv-title mb-0">🧾 Đơn hàng mới nhất</h5>
          <a href="{{ route('admin.orders.index') }}" class="kpi-badge">Tất cả đơn →</a>
        </div>

        <div class="table-responsive">
          <table class="table table-lightish align-middle text-center mb-0">
            <thead>
              <tr>
                <th width="70">#</th>
                <th class="text-start">Khách hàng</th>
                <th width="160">Ngày đặt</th>
                <th width="140">Tổng tiền</th>
                <th width="130">Thanh toán</th>
                <th width="130">Trạng thái</th>
                <th width="90">Xem</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestOrders as $o)
              <tr>
                <td><span class="badge bg-primary fw-bold">{{ $o->id }}</span></td>
                <td class="text-start">{{ $o->user->name ?? $o->shipping_name }}</td>
                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                <td class="price">{{ number_format($o->total_amount,0,',','.') }} đ</td>
                <td>{{ strtoupper($o->payment_method ?? 'COD') }}</td>
                <td>
                  <span class="pill
                    @if($o->status=='pending') bg-pending
                    @elseif($o->status=='processing') bg-process
                    @elseif($o->status=='shipping') bg-ship
                    @elseif($o->status=='delivered') bg-delivered
                    @else bg-cancel @endif">
                    {{ $o->status }}
                  </span>
                </td>
                <td><a class="btn-view btn-sm" href="{{ route('admin.orders.show',$o->id) }}">Xem</a></td>
              </tr>
              @empty
              <tr><td colspan="7" class="text-center text-muted">Chưa có đơn hàng</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const sttData = {
  pending:   {{ $stats['pending'] }},
  processing:{{ $stats['processing'] }},
  shipping:  {{ $stats['shipping'] }},
  delivered: {{ $stats['delivered'] }},
  cancelled: {{ $stats['cancelled'] }},
};
new Chart(document.getElementById('orderStatusPie'), {
  type: 'doughnut',
  data: {
    labels: ['Pending','Processing','Shipping','Delivered','Cancelled'],
    datasets: [{
      data: [
        sttData.pending, sttData.processing, sttData.shipping,
        sttData.delivered, sttData.cancelled
      ],
      backgroundColor: ['#fde68a','#93c5fd','#6ee7b7','#86efac','#fca5a5'],
      borderColor: '#fff', borderWidth: 2
    }]
  },
  options: {
    plugins: { legend: { labels: { color: '#374151', font:{weight:'600'} } } }
  }
});
</script>
@endpush
