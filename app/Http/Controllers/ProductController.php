<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Eager-load category + sắp xếp mới nhất
        $query = Product::with('category')->orderByDesc('id');

        // Mặc định: khách xem 8/sp trang
        $perPage = 8;

        // Nếu đã đăng nhập và là admin => 10/sp trang
        if (auth()->check() && auth()->user()->role === 'admin') {
            $perPage = 10;
        }

        // Phân trang, giữ query string nếu sau này có filter/search
        $products = $query->paginate($perPage)->withQueryString();
        $query = Product::with('category');

$query->when(request('q'), fn($q,$kw) =>
    $q->where(function($x) use ($kw){
        $x->where('name','like',"%$kw%")
          ->orWhere('description','like',"%$kw%")
          ->orWhere('features','like',"%$kw%");
    })
);

$query->when(request('category_id'), fn($q,$cid)=> $q->where('category_id',$cid));
$query->when(request('price_min'),   fn($q,$v)=> $q->where('price','>=',(int)$v));
$query->when(request('price_max'),   fn($q,$v)=> $q->where('price','<=',(int)$v));

$query->when(request('sort'), function($q,$sort){
    return match($sort){
        'price_asc'  => $q->orderBy('price','asc'),
        'price_desc' => $q->orderBy('price','desc'),
        'name_asc'   => $q->orderBy('name','asc'),
        'name_desc'  => $q->orderBy('name','desc'),
        default      => $q->latest()
    };
});

$products = $query->paginate(auth()->check() && auth()->user()->role==='admin' ? 10 : 8);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity'    => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'features'    => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity'    => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'features'    => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

   public function show(Product $product, Request $request)
{
    $ratingFilter = (int) $request->query('rating', 0);

    // đếm phân phối sao
    $ratingCounts = \App\Models\Review::select('rating', \DB::raw('COUNT(*) as c'))
        ->where('product_id', $product->id)
        ->groupBy('rating')
        ->pluck('c', 'rating'); // [5=>n,4=>n,...]

    $reviewsQuery = \App\Models\Review::with(['user','replies.admin','images'])
        ->where('product_id', $product->id)
        ->latest();

    if ($ratingFilter >= 1 && $ratingFilter <= 5) {
        $reviewsQuery->where('rating', $ratingFilter);
    }

    // nếu bạn đang dùng $product->reviews trong view,
    // có thể gán thủ công để không sửa view nhiều:
    $product->setRelation('reviews', $reviewsQuery->get());

    return view('products.show', [
        'product'       => $product,
        'ratingCounts'  => $ratingCounts,
        'ratingFilter'  => $ratingFilter,
    ]);
}


}
