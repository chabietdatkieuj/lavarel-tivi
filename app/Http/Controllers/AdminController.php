<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Thống kê nhanh
        $stats = [
            'products'    => Product::count(),
            'categories'  => Category::count(),
            'orders'      => Order::count(),
            'pending'     => Order::where('status', 'pending')->count(),
            'processing'  => Order::where('status', 'processing')->count(),
            'shipping'    => Order::where('status', 'shipping')->count(),
            'delivered'   => Order::where('status', 'delivered')->count(),
            'cancelled'   => Order::where('status', 'cancelled')->count(),
            'revenue'     => Order::where('status', 'delivered')->sum('total_amount'),
        ];

        $latestOrders = Order::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'latestOrders'));
    }

    public function products()
    {
        $products = Product::with('category')->latest()->paginate(12);
        return view('admin.products.index', compact('products'));
    }

    public function categories()
    {
        $categories = Category::latest()->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }

    /* =========================
     *  QUẢN TRỊ ĐƠN HÀNG
     * ========================= */

    // Danh sách đơn + lọc theo trạng thái + tìm kiếm
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('s');

        $q = Order::query()->with('user')->latest();

        if ($status && in_array($status, ['pending','processing','shipping','delivered','cancelled','failed','paid','unpaid'])) {
            $q->where('status', $status);
        }

        if ($search) {
            $q->where(function($x) use ($search) {
                $x->where('id', intval($search))
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }


         // 🔽 chỉ 10 đơn/trang + giữ query khi chuyển trang
    $orders = $q->paginate(10)->withQueryString();

    return view('admin.orders.index', compact('orders', 'status', 'search'));
    }

    // Chi tiết đơn
    public function showOrder(Order $order)
    {
        $order->load(['items.product', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,delivered,cancelled,failed,paid,unpaid'
        ]);

        $old = $order->status;
        $new = $request->status;

        // Quy tắc chuyển trạng thái cơ bản
        $allowed = [
            'pending'    => ['processing','cancelled'],
            'processing' => ['shipping','cancelled'],
            'shipping'   => ['delivered','cancelled'],
            // Nếu muốn cho phép admin sửa về bất cứ trạng thái nào, bỏ check này
            'delivered'  => [],
            'cancelled'  => [],
        ];

        if (isset($allowed[$old]) && !in_array($new, $allowed[$old])) {
            return back()->with('error', "Không thể chuyển trạng thái từ {$old} ➜ {$new}.");
        }

        DB::transaction(function () use ($order, $new) {
            // Hoàn kho nếu HỦY
            if ($new === 'cancelled' && $order->status !== 'cancelled') {
                $order->loadMissing('items.product');
                foreach ($order->items as $it) {
                    if ($it->product) {
                        $it->product->increment('quantity', $it->quantity);
                    }
                }
            }

            $order->update(['status' => $new]);
        });

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    // Xóa đơn (tuỳ chọn)
    public function destroyOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Đã xóa đơn hàng.');
    }
}
