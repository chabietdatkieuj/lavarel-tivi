<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * BÁO CÁO DẠNG BẢNG (tổng quan)
     */
    public function index(Request $request)
    {
        // Chỉ tính doanh thu với đơn đã thanh toán / đã giao
        $paidStatuses = ['delivered', 'paid'];

        // 1) Doanh thu theo DANH MỤC (SUM(order_items.price * order_items.quantity))
        $categoryRevenue = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
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

        // 2) Tổng số đơn hàng
        $totalOrders = Order::count();

        // 3) Tổng số khách hàng
        $totalCustomers = DB::table('users')
            ->whereIn('role', ['customer', 'user', 'CUSTOMER', 'USER'])
            ->count();

        // 4) Doanh thu theo NGÀY (MySQL)
        $revenueByDate = Order::query()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS total_revenue, COUNT(*) AS order_count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('d')
            ->get();

        // 5) Doanh thu theo THÁNG (MySQL)
        $revenueByMonth = Order::query()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, COALESCE(SUM(total_amount),0) AS total_revenue, COUNT(*) AS order_count')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // 6) Doanh thu theo NĂM (MySQL)
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

    /**
     * BÁO CÁO DẠNG BIỂU ĐỒ (Chart.js)
     */
    public function charts(Request $request)
    {
        $paid = ['delivered', 'paid'];

        // 1) Doanh thu theo DANH MỤC
        $byCat = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->whereIn('o.status', $paid)
            ->selectRaw('COALESCE(c.name, CONCAT("Danh mục #", p.category_id)) AS name')
            ->selectRaw('SUM(oi.price * oi.quantity) AS revenue')
            ->groupBy('name')->orderByDesc('revenue')
            ->get();

        $catLabels  = $byCat->pluck('name')->toArray();
        $catRevenue = $byCat->pluck('revenue')->map(fn($v)=>(float)$v)->toArray();

        // 2) Doanh thu theo NGÀY – 30 ngày gần nhất
        $startDay = Carbon::now()->subDays(29)->startOfDay();
        $endDay   = Carbon::now()->endOfDay();

        $byDate = Order::whereIn('status', $paid)
            ->whereBetween('created_at', [$startDay, $endDay])
            ->selectRaw('DATE(created_at) AS d, SUM(total_amount) AS revenue')
            ->groupBy('d')->orderBy('d')
            ->pluck('revenue', 'd'); // map: 'Y-m-d' => revenue

        $revDateLabels = [];
        $revDateData   = [];
        for ($d = $startDay->copy(); $d <= $endDay; $d->addDay()) {
            $key = $d->toDateString();
            $revDateLabels[] = $d->format('d/m');
            $revDateData[]   = (float)($byDate[$key] ?? 0);
        }

        // 3) Doanh thu theo THÁNG – 12 tháng gần nhất
        $startMonth = Carbon::now()->subMonths(11)->startOfMonth();
        $byMonth = Order::whereIn('status', $paid)
            ->where('created_at', '>=', $startMonth)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, SUM(total_amount) AS revenue')
            ->groupBy('ym')->orderBy('ym')
            ->pluck('revenue', 'ym'); // map: 'YYYY-mm' => revenue

        $revMonthLabels = [];
        $revMonthData   = [];
        for ($i=0; $i<12; $i++) {
            $m   = $startMonth->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $revMonthLabels[] = $m->format('m/Y');
            $revMonthData[]   = (float)($byMonth[$key] ?? 0);
        }

        // 4) Doanh thu theo NĂM
        $byYear = Order::whereIn('status', $paid)
            ->selectRaw('YEAR(created_at) AS y, SUM(total_amount) AS revenue')
            ->groupBy('y')->orderBy('y')->get();

        $revYearLabels = $byYear->pluck('y')->toArray();
        $revYearData   = $byYear->pluck('revenue')->map(fn($v)=>(float)$v)->toArray();

        // 5) Doanh thu theo PHƯƠNG THỨC THANH TOÁN
        $paymentMethodLabels = ['MOMO','COD'];
        $paymentMethodRevenue = [
            (float) Order::whereIn('status',$paid)->where('payment_method','momo')->sum('total_amount'),
            (float) Order::whereIn('status',$paid)->where('payment_method','cod')->sum('total_amount'),
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
