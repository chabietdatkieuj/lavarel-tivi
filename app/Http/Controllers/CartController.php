<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order; // ➕ THÊM DÒNG NÀY

class CartController extends Controller
{
    private function getUserCart()
    {
        // Lấy giỏ theo user, nếu chưa có thì tạo
        return Cart::with('items.product')
            ->firstOrCreate(['user_id' => auth()->id()]);
    }

    /** Hiển thị giỏ hàng */
    public function index(Request $request)
    {
        $cart  = $this->getUserCart();
        $items = $cart->items; // đã có quan hệ product

        $totalQty  = $items->sum('quantity');
        $totalCost = $items->sum(fn ($i) => $i->quantity * $i->price);

        // ➕ LẤY 5 ĐƠN GẦN NHẤT CỦA USER
        $recentOrders = Order::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('cart.index', compact('cart', 'items', 'totalQty', 'totalCost', 'recentOrders')); // ➕ truyền $recentOrders
    }

    /** Thêm sản phẩm vào giỏ */
    public function add(Request $request, Product $product)
    {
        $cart = $this->getUserCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => 1,
                'price'      => (float) $product->price, // cần cột price trong cart_items
            ]);
        }

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    /** Cập nhật số lượng */
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = $this->getUserCart();
        $item = $cart->items()->where('id', $id)->first();

        if ($item) {
            $item->update(['quantity' => (int) $request->quantity]);
        }

        return back()->with('success', 'Đã cập nhật số lượng.');
    }

    /** Xóa 1 dòng trong giỏ */
    public function remove(Request $request, $id)
    {
        $cart = $this->getUserCart();
        $cart->items()->where('id', $id)->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ.');
    }

    /** Xóa tất cả */
    public function clear(Request $request)
    {
        $cart = $this->getUserCart();
        $cart->items()->delete();

        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }
}
