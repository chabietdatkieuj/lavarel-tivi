<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);
         // Gửi email xác thực
    $user->sendEmailVerificationNotification();

    Auth::login($user);

    return redirect()->route('verification.notice')
        ->with('success', 'Vui lòng kiểm tra email để xác thực tài khoản.');


       

       
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
   public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        if (! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice')
                ->with('success', 'Tài khoản chưa xác thực email. Vui lòng kiểm tra hộp thư hoặc bấm gửi lại liên kết xác thực.');
        }

        return redirect()->route('products.index')->with('success', 'Đăng nhập thành công!');
    }

    return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng!']);
}

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Đăng xuất thành công');
    }
}
