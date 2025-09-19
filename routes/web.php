<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;

use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;

// NEW: Vouchers/Promos controllers
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\PromoController;

// NEW: CHAT
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ChatAdminController;

/* =========================
 *  TRANG CHỦ (public)
 * ========================= */
Route::get('/', function () {
    return view('welcome', [
        'hotCategories'    => Category::latest()->take(6)->get(),
        'featuredProducts' => Product::latest()->take(8)->get(),
    ]);
})->name('welcome');

/* =========================
 *  MoMo CALLBACK/IPN (public để MoMo gọi)
 * ========================= */
Route::get ('/payment/momo/callback', [OrderController::class,'callback'])->name('payment.momo.callback');
Route::post('/payment/momo/ipn',      [OrderController::class,'ipn'])->name('payment.momo.ipn');

/* =========================
 *  VÙNG ĐÃ ĐĂNG NHẬP + XÁC THỰC EMAIL
 * ========================= */
Route::middleware(['auth','verified'])->group(function () {

    /* ---------------------------------
     *  ADMIN (role=admin)
     * --------------------------------- */
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        // Orders
        Route::get   ('/admin/orders',                [AdminController::class, 'orders'])->name('admin.orders.index');
        Route::get   ('/admin/orders/{order}',        [AdminController::class, 'showOrder'])->name('admin.orders.show');
        Route::patch ('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
        Route::delete('/admin/orders/{order}',        [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

        // CRUD danh mục/sản phẩm (KHÔNG prefix /admin)
        Route::resource('categories', CategoryController::class)->except(['index','show']);
        Route::resource('products',   ProductController::class)->except(['index','show']);

        // Báo cáo + Người dùng + Quản lý đánh giá + Vouchers + Chat admin
        Route::prefix('admin')->name('admin.')->group(function () {
            // Reports
            Route::get('/reports',        [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');

            // Users
            Route::resource('users', UserController::class);

            // Reviews (admin)
            Route::get   ('/reviews',              [AdminReviewController::class, 'index'])->name('reviews.index');
            Route::delete('/reviews/{review}',     [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
            // Replies
            Route::post  ('/reviews/{review}/replies', [AdminReviewController::class, 'replyStore'])->name('reviews.replies.store');
            Route::patch ('/reviews/replies/{reply}',  [AdminReviewController::class, 'replyUpdate'])->name('reviews.replies.update');
            Route::delete('/reviews/replies/{reply}',  [AdminReviewController::class, 'replyDestroy'])->name('reviews.replies.destroy');

            // ✅ Voucher CRUD cho Admin
            Route::resource('vouchers', AdminVoucherController::class);

            // ✅ NEW: CHAT (Admin UI + API)
            Route::get ('/chats',                       [ChatAdminController::class,'index'])->name('chats.index');
            Route::get ('/chats/{conversation}',        [ChatAdminController::class,'show'])->name('chats.show');
            Route::post('/chats/{conversation}/send',   [ChatAdminController::class,'send'])->name('chats.send');
            Route::get ('/chats/{conversation}/fetch',  [ChatAdminController::class,'fetch'])->name('chats.fetch');
            Route::post('/chats/{conversation}/close',  [ChatAdminController::class,'close'])->name('chats.close');
        });
    });

    /* ---------------------------------
     *  CUSTOMER: chỉ XEM danh mục & sản phẩm
     * --------------------------------- */
    Route::resource('categories', CategoryController::class)->only(['index','show']);
    Route::resource('products',   ProductController::class)->only(['index','show']);

    /* ---------------------------------
     *  CUSTOMER: CART + CHECKOUT + LỊCH SỬ ĐƠN + TẠO REVIEW
     * --------------------------------- */
    Route::middleware('customer')->group(function () {
        // Cart
        Route::get   ('/cart',               [CartController::class,'index'])->name('cart.index');
        Route::post  ('/cart/add/{product}', [CartController::class,'add'])->name('cart.add');
        Route::patch ('/cart/update/{id}',   [CartController::class,'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}',   [CartController::class,'remove'])->name('cart.remove');
        Route::delete('/cart/clear',         [CartController::class,'clear'])->name('cart.clear');

        // Checkout (+ áp mã giảm giá trong OrderController@store)
        Route::get ('/checkout', [OrderController::class,'create'])->name('checkout.create');
        Route::post('/checkout', [OrderController::class,'store'])->name('checkout.store');

        // Orders (customer)
        Route::get ('/orders',         [OrderController::class,'index'])->name('orders.index');
        Route::get ('/orders/{order}', [OrderController::class,'show'])->name('orders.show');
        Route::get ('/orders/{order}/pay/momo', [OrderController::class,'payAgain'])->name('orders.momo.pay');

        // Reviews – tạo mới theo đơn đã giao
        Route::get ('/orders/{order}/reviews/create/{product}', [ReviewController::class,'create'])->name('reviews.create');
        Route::post('/orders/{order}/reviews/{product}',        [ReviewController::class,'store'])->name('reviews.store');

        // ✅ Trang khuyến mãi/tin tức cho KH
        Route::get('/promos', [PromoController::class,'index'])->name('promos.index');
        Route::get('/vouchers/news', [PromoController::class,'index'])->name('vouchers.news');

        // ✅ NEW: CHAT (User API)
        Route::post('/chat/send',  [ChatController::class,'send'])->name('chat.send');
        Route::get ('/chat/fetch', [ChatController::class,'fetch'])->name('chat.fetch');
    });

    /* ---------------------------------
     *  REVIEW: CHO PHÉP CHỦ REVIEW SỬA/XÓA
     * --------------------------------- */
    Route::get   ('/reviews/{review}/edit', [ReviewController::class,'edit'])->name('reviews.edit');
    Route::patch ('/reviews/{review}',      [ReviewController::class,'update'])->name('reviews.update');
    Route::delete('/reviews/{review}',      [ReviewController::class,'destroy'])->name('reviews.destroy');

    /* ---------------------------------
     *  (Tuỳ chọn) prefix /user nếu còn dùng
     * --------------------------------- */
    Route::prefix('user')->name('user.')->middleware('customer')->group(function () {
        Route::get('orders',      [OrderController::class,'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class,'show'])->name('orders.show');
    });
});

/* =========================
 *  AUTH
 * ========================= */
Route::get ('/register', [AuthController::class,'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class,'register'])->name('register.post');

Route::get ('/login',    [AuthController::class,'showLoginForm'])->name('login');
Route::post('/login',    [AuthController::class,'login'])->name('login.post');

Route::post('/logout',   [AuthController::class,'logout'])->name('logout');

/* =========================
 *  VERIFY EMAIL
 * ========================= */
Route::get('/email/verify', fn() => view('auth.verify-email'))
    ->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome');
})->middleware(['auth','signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message','Verification link sent!');
})->middleware(['auth','throttle:6,1'])->name('verification.send');

/* =========================
 *  FALLBACK
 * ========================= */
Route::fallback(fn() => redirect()->route('welcome'));
