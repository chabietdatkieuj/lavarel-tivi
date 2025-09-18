<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::check() || Auth::user()->role !== 'admin', 403);

        $productId = $request->query('product_id');
        $rating    = $request->query('rating'); // 1..5 hoặc null

        $q = Review::with(['user','product'])->latest();

        if ($productId) {
            $q->where('product_id', $productId);
        }

        if ($rating !== null && $rating !== '') {
            $r = (int) $rating;
            if ($r >= 1 && $r <= 5) {
                $q->where('rating', $r);
            }
        }

        $reviews  = $q->paginate(12)->withQueryString();
        $products = Product::select('id','name')->orderBy('name')->get();

        // Lấy replies cho các review hiện có (gom nhóm theo review_id)
        $replyMap = collect();
        if ($reviews->count()) {
            $replyMap = DB::table('review_replies')
                ->join('users', 'users.id', '=', 'review_replies.admin_id')
                ->whereIn('review_id', $reviews->pluck('id'))
                ->orderBy('review_replies.created_at', 'asc')
                ->get([
                    'review_replies.id',
                    'review_replies.review_id',
                    'review_replies.content',
                    'review_replies.created_at',
                    'users.name as admin_name',
                ])
                ->groupBy('review_id');
        }

        return view('admin.reviews.index', compact(
            'reviews', 'products', 'productId', 'rating', 'replyMap'
        ));
    }

    public function destroy($id)
    {
        abort_if(!Auth::check() || Auth::user()->role !== 'admin', 403);

        $r = Review::findOrFail($id);
        // FK review_replies -> reviews đã cascadeOnDelete, không cần xóa tay
        $r->delete();

        return back()->with('success', 'Đã xóa đánh giá.');
    }

    /** Admin trả lời 1 review (không tạo model mới) */
    public function replyStore(Request $request, Review $review)
    {
        abort_if(!Auth::check() || Auth::user()->role !== 'admin', 403);

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        DB::table('review_replies')->insert([
            'review_id'  => $review->id,
            'admin_id'   => Auth::id(),
            'content'    => $data['content'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã trả lời đánh giá.');
    }

    /** Admin xóa 1 trả lời cụ thể */
    public function replyDestroy($replyId)
    {
        abort_if(!Auth::check() || Auth::user()->role !== 'admin', 403);

        DB::table('review_replies')->where('id', $replyId)->delete();

        return back()->with('success', 'Đã xoá trả lời.');
    }
}
