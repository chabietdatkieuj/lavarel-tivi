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

// +++ NEW: Admin sub-controllers +++
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;

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
 *  VÙNG ĐÃ ĐĂNG NHẬP + XÁC THỰC EMAIL
 * ========================= */
Route::middleware(['auth','verified'])->group(function () {

    /* ---------------------------------
     *  ADMIN (phân quyền role=admin)
     *  Ghi chú:
     *   - Giữ nguyên quy ước cũ:
     *     + CRUD Categories/Products cho Admin KHÔNG có prefix /admin
     *       nhưng chỉ dành cho Admin và EXCEPT index/show (để không đụng với route customer)
     *   - Các module Orders/Reports/Users sẽ có prefix /admin và name('admin.')
     * --------------------------------- */
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        // --- ADMIN: QUẢN LÝ ĐƠN HÀNG (/admin/orders/...)
        Route::get   ('/admin/orders',                [AdminController::class, 'orders'])->name('admin.orders.index');
        Route::get   ('/admin/orders/{order}',        [AdminController::class, 'showOrder'])->name('admin.orders.show');
        Route::patch ('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
        Route::delete('/admin/orders/{order}',        [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

        // --- ADMIN: CRUD danh mục/sản phẩm (KHÔNG prefix /admin)
        Route::resource('categories', CategoryController::class)->except(['index','show']);
        Route::resource('products',   ProductController::class)->except(['index','show']);

        // --- ADMIN: BÁO CÁO + NGƯỜI DÙNG (CÓ prefix /admin, name admin.*)
        Route::prefix('admin')->name('admin.')->group(function () {
            // Báo cáo
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            // 🔥 Thêm route Biểu đồ (Chart.js)
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');


            // Quản lý người dùng
            Route::resource('users', UserController::class);
        });
    });


    /* ---------------------------------
     *  CUSTOMER: chỉ XEM danh mục & sản phẩm
     * --------------------------------- */
    Route::resource('categories', CategoryController::class)->only(['index','show']);
    Route::resource('products',   ProductController::class)->only(['index','show']);


    /* ---------------------------------
     *  CUSTOMER: CART + CHECKOUT + LỊCH SỬ ĐƠN
     * --------------------------------- */
    Route::middleware('customer')->group(function () {

        // CART
        Route::get   ('/cart',               [CartController::class,'index'])->name('cart.index');
        Route::post  ('/cart/add/{product}', [CartController::class,'add'])->name('cart.add');
        Route::patch ('/cart/update/{id}',   [CartController::class,'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}',   [CartController::class,'remove'])->name('cart.remove');
        Route::delete('/cart/clear',         [CartController::class,'clear'])->name('cart.clear');

        // CHECKOUT
        Route::get ('/checkout', [OrderController::class,'create'])->name('checkout.create');
        Route::post('/checkout', [OrderController::class,'store'])->name('checkout.store');

        // Orders history for customer
        Route::get ('/orders',         [OrderController::class,'index'])->name('orders.index');
        Route::get ('/orders/{order}', [OrderController::class,'show'])->name('orders.show');

        // MoMo
        Route::get ('/orders/{order}/pay/momo', [OrderController::class,'payAgain'])->name('orders.momo.pay');
        Route::get ('/payment/momo/callback',   [OrderController::class,'callback'])->name('payment.momo.callback');
        Route::post('/payment/momo/ipn',        [OrderController::class,'ipn'])->name('payment.momo.ipn');
    });

    /* ---------------------------------
     *  (Tuỳ chọn) Khu vực prefix /user nếu bạn còn dùng song song
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
