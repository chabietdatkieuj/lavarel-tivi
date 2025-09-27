<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /** Trang sửa thông tin tài khoản */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('account.edit', compact('user'));
    }

    /** Lưu thông tin tài khoản (họ tên/điện thoại/địa chỉ) */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
        ]);

        $user->update($data);

        return back()->with('success', 'Cập nhật tài khoản thành công.');
    }

    /** Trang đổi mật khẩu */
    public function editPassword(Request $request)
    {
        return view('account.password');
    }

    /** Lưu mật khẩu mới với validate “chuẩn chỉnh” */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|string|min:6|confirmed', // cần trường new_password_confirmation
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = $request->user();

        // Kiểm tra mật khẩu hiện tại
        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])
                ->withInput();
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Đã cập nhật mật khẩu thành công.');
    }
}
