@extends('layouts.app')
@section('title','Biểu đồ báo cáo')

@push('styles')
<style>
.card-chart{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    color:#111827;
    box-shadow:0 6px 16px rgba(17,24,39,.06);
}
</style>
@endpush

@section('content')
<h2 class="fw-bold text-dark mb-3">📊 Biểu đồ báo cáo</h2>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-chart">
            <h5 class="mb-2">Doanh thu theo danh mục</h5>
            <canvas id="chartByCategory" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-chart">
            <h5 class="mb-2">Doanh thu 30 ngày gần nhất</h5>
            <canvas id="chartByDate" height="220"></canvas>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-chart">
            <h5 class="mb-2">Doanh thu 12 tháng gần nhất</h5>
            <canvas id="chartByMonth" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-chart">
            <h5 class="mb-2">Doanh thu theo năm</h5>
            <canvas id="chartByYear" height="220"></canvas>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-chart">
            <h5 class="mb-2">Doanh thu theo phương thức thanh toán</h5>
            <canvas id="chartPaymentMethod" height="220"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const catLabels  = @json($catLabels ?? []);
const catRevenue = @json($catRevenue ?? []);

const revDateLabels = @json($revDateLabels ?? []);
const revDateData   = @json($revDateData ?? []);

const revMonthLabels = @json($revMonthLabels ?? []);
const revMonthData   = @json($revMonthData ?? []);

const revYearLabels = @json($revYearLabels ?? []);
const revYearData   = @json($revYearData ?? []);

const payLabels = @json($paymentMethodLabels ?? []);
const payData   = @json($paymentMethodRevenue ?? []);

// GIỮ NGUYÊN PALETTE/GIÁ TRỊ – chỉ đổi màu nền/trục sang light
const palette = ['#36a2eb','#ff6384','#ffcd56','#4bc0c0','#9966ff','#ff9f40','#c9cbcf'];

const baseOptions = {
  plugins:{ legend:{ labels:{ color:'#111827' }}},
  scales:{
    x:{ ticks:{ color:'#374151' }, grid:{ color:'#e5e7eb' } },
    y:{ ticks:{ color:'#374151' }, grid:{ color:'#e5e7eb' } },
  }
};

// 1) Doughnut - by Category
new Chart(document.getElementById('chartByCategory'), {
  type: 'doughnut',
  data: {
    labels: catLabels,
    datasets: [{
      data: catRevenue,
      backgroundColor: palette,
      borderColor: '#e5e7eb',
      borderWidth: 1
    }]
  },
  options: { plugins: { legend: { labels: { color: '#111827' }}} }
});

// 2) Line - by Date
new Chart(document.getElementById('chartByDate'), {
  type: 'line',
  data: {
    labels: revDateLabels,
    datasets: [{
      label: 'Doanh thu (₫)',
      data: revDateData,
      borderColor: '#36a2eb',
      backgroundColor: 'rgba(54,162,235,0.18)',
      fill: true,
      tension: .2
    }]
  },
  options: baseOptions
});

// 3) Bar - by Month
new Chart(document.getElementById('chartByMonth'), {
  type: 'bar',
  data: {
    labels: revMonthLabels,
    datasets: [{
      label: 'Doanh thu (₫)',
      data: revMonthData,
      backgroundColor: '#ffcd56',
      borderColor: '#eab308',
      borderWidth: 1
    }]
  },
  options: baseOptions
});

// 4) Bar - by Year
new Chart(document.getElementById('chartByYear'), {
  type: 'bar',
  data: {
    labels: revYearLabels,
    datasets: [{
      label: 'Doanh thu (₫)',
      data: revYearData,
      backgroundColor: '#4bc0c0',
      borderColor: '#3e9d9d',
      borderWidth: 1
    }]
  },
  options: baseOptions
});

// 5) Pie - payment method
new Chart(document.getElementById('chartPaymentMethod'), {
  type: 'pie',
  data: {
    labels: payLabels,
    datasets: [{
      data: payData,
      backgroundColor: ['#ff6384','#36a2eb','#ffcd56','#4bc0c0','#9966ff','#ff9f40'],
      borderColor: '#e5e7eb',
      borderWidth: 1
    }]
  },
  options: { plugins: { legend: { labels: { color: '#111827' }}} }
});
</script>
@endpush
