<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function editProfile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => ['required','string','max:255'],
            'phone' => ['nullable','regex:/^(0|\+84)\d{9,10}$/'], // 10-11 số, bắt đầu 0 hoặc +84
        ],[
            'name.required'  => 'Vui lòng nhập họ tên.',
            'phone.regex'    => 'Số điện thoại không hợp lệ.',
        ]);

        $user = Auth::user();
        $user->name  = $request->name;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success','Cập nhật thông tin thành công!');
    }

    public function editPassword()
    {
        return view('account.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password'              => ['required','current_password'],
            'new_password'              => ['required','string','min:6','confirmed'],
            // dùng input: new_password_confirmation để xác nhận
        ],[
            'old_password.required'     => 'Vui lòng nhập mật khẩu hiện tại.',
            'old_password.current_password' => 'Mật khẩu cũ không đúng.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải tối thiểu 6 ký tự.',
            'new_password.confirmed'    => 'Mật khẩu nhập lại không trùng khớp.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success','Đổi mật khẩu thành công!');
    }
}
