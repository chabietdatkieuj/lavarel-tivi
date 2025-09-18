<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function create($order, $product)
    {
        [$order, $product] = $this->assertCanReview((int)$order, (int)$product);

        $existing = Review::where([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'user_id'    => Auth::id(),
        ])->first();

        return view('reviews.create', compact('order','product','existing'));
    }

    public function store(Request $request, $order, $product)
    {
        [$order, $product] = $this->assertCanReview((int)$order, (int)$product);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $data['rating']  = (int) $data['rating'];
        $data['comment'] = isset($data['comment']) ? trim($data['comment']) : null;

        DB::transaction(function () use ($order, $product, $data) {
            Review::updateOrCreate(
                [
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'user_id'    => Auth::id(),
                ],
                $data
            );
        });

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Đã gửi đánh giá. Cảm ơn bạn!');
    }

    public function edit(Review $review)
    {
        abort_unless($review->user_id === Auth::id(), 403);
        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === Auth::id(), 403);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $data['rating']  = (int) $data['rating'];
        $data['comment'] = isset($data['comment']) ? trim($data['comment']) : null;

        $review->update($data);

        return back()->with('success','Đã cập nhật đánh giá.');
    }

    public function destroy(Review $review)
    {
        abort_unless($review->user_id === Auth::id(), 403);

        // Lưu lại điểm đến an toàn trước khi xoá
        $productId = $review->product_id;
        $orderId   = $review->order_id;

        $review->delete();

        // Ưu tiên đưa về trang chi tiết đơn nếu có
        if ($orderId) {
            return redirect()
                ->route('orders.show', $orderId)
                ->with('success', 'Đã xoá đánh giá.');
        }

        // Nếu không có order, đưa về trang sản phẩm (kèm anchor reviews)
        return redirect()
            ->to(route('products.show', $productId) . '#reviews')
            ->with('success', 'Đã xoá đánh giá.');
    }

    private function assertCanReview(int $orderId, int $productId): array
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        abort_if(!$order, 403, 'Bạn không có quyền xem đơn này.');
        abort_if($order->status !== 'delivered', 422, 'Đơn chưa ở trạng thái đã giao.');

        $product = Product::findOrFail($productId);

        $hasItem = DB::table('order_items')
            ->where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->exists();

        abort_if(!$hasItem, 422, 'Sản phẩm không có trong đơn hàng này.');

        return [$order, $product];
    }
}
