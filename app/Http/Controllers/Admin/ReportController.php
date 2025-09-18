<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    /** BÁO CÁO DẠNG BẢNG */
    public function index(Request $request)
    {
        $paidStatuses = ['delivered', 'paid'];

        // Doanh thu theo danh mục (từ order_items)
        $categoryRevenue = DB::table('order_items as oi')
            ->join('orders as o',   'oi.order_id',   '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->whereIn('o.status', $paidStatuses)
            ->select(
                'p.category_id',
                DB::raw('COALESCE(c.name, "Chưa phân loại") AS category_name'),
                DB::raw('COALESCE(SUM(oi.price * oi.quantity), 0) AS total_revenue'),
                DB::raw('COALESCE(SUM(oi.quantity), 0) AS total_qty')
            )
            ->groupBy('p.category_id', 'c.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalOrders    = Order::count();
        $totalCustomers = DB::table('users')
            ->whereIn('role', ['customer','user','CUSTOMER','USER'])
            ->count();

        // Theo ngày
        $revenueByDate = Order::query()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS total_revenue, COUNT(*) AS order_count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('d')
            ->get();

        // Theo tháng
        $revenueByMonth = Order::query()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, COALESCE(SUM(total_amount),0) AS total_revenue, COUNT(*) AS order_count')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // Theo năm
        $revenueByYear = Order::query()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('YEAR(created_at) AS y, COALESCE(SUM(total_amount),0) AS total_revenue, COUNT(*) AS order_count')
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        return view('admin.reports.index', compact(
            'categoryRevenue',
            'totalOrders',
            'totalCustomers',
            'revenueByDate',
            'revenueByMonth',
            'revenueByYear'
        ));
    }

    /** BÁO CÁO DẠNG BIỂU ĐỒ (Chart.js) */
    public function charts(Request $request)
    {
        $paid = ['delivered','paid'];

        // 1) Danh mục (group chắc ăn, tránh alias)
        $byCat = DB::table('order_items as oi')
            ->join('orders as o',   'oi.order_id',   '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->whereIn('o.status', $paid)
            ->select(
                'p.category_id',
                DB::raw('COALESCE(c.name, CONCAT("Danh mục #", p.category_id)) AS cat_name'),
                DB::raw('COALESCE(SUM(oi.price * oi.quantity),0) AS revenue')
            )
            ->groupBy('p.category_id','c.name')
            ->orderByDesc('revenue')
            ->get();

        $catLabels  = $byCat->pluck('cat_name')->toArray();
        $catRevenue = $byCat->pluck('revenue')->map(fn($v)=>(float)$v)->toArray();

        // 2) Theo ngày – 30 ngày gần nhất (điền 0 cho ngày thiếu)
        $startDay = Carbon::now()->startOfDay()->subDays(29);
        $endDay   = Carbon::now()->endOfDay();

        $byDate = Order::whereIn('status', $paid)
            ->whereBetween('created_at', [$startDay, $endDay])
            ->selectRaw('DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS revenue')
            ->groupBy('d')->orderBy('d')
            ->pluck('revenue', 'd');

        $revDateLabels = [];
        $revDateData   = [];
        for ($d = $startDay->copy(); $d <= $endDay; $d->addDay()) {
            $key = $d->toDateString();
            $revDateLabels[] = $d->format('d/m');
            $revDateData[]   = (float)($byDate[$key] ?? 0);
        }

        // 3) Theo tháng – 12 tháng gần nhất (điền 0 cho tháng thiếu)
        $startMonth = Carbon::now()->startOfMonth()->subMonths(11);

        $byMonth = Order::whereIn('status', $paid)
            ->where('created_at', '>=', $startMonth)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, COALESCE(SUM(total_amount),0) AS revenue')
            ->groupBy('ym')->orderBy('ym')
            ->pluck('revenue', 'ym');

        $revMonthLabels = [];
        $revMonthData   = [];
        for ($i=0; $i<12; $i++) {
            $m = $startMonth->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $revMonthLabels[] = $m->format('m/Y');
            $revMonthData[]   = (float)($byMonth[$key] ?? 0);
        }

        // 4) Theo năm
        $byYear = Order::whereIn('status', $paid)
            ->selectRaw('YEAR(created_at) AS y, COALESCE(SUM(total_amount),0) AS revenue')
            ->groupBy('y')->orderBy('y')->get();

        $revYearLabels = $byYear->pluck('y')->toArray();
        $revYearData   = $byYear->pluck('revenue')->map(fn($v)=>(float)$v)->toArray();

        // 5) Theo phương thức thanh toán
        $paymentMethodLabels  = ['MOMO','COD'];
        $paymentMethodRevenue = [
            (float) Order::whereIn('status',$paid)->where('payment_method','momo')->sum('total_amount') ?? 0,
            (float) Order::whereIn('status',$paid)->where('payment_method','cod')->sum('total_amount')  ?? 0,
        ];

        return view('admin.reports.charts', compact(
            'catLabels','catRevenue',
            'revDateLabels','revDateData',
            'revMonthLabels','revMonthData',
            'revYearLabels','revYearData',
            'paymentMethodLabels','paymentMethodRevenue'
        ));
    }
}
