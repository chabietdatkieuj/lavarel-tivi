<?php
namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('account.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_name'  => ['required','string','max:255'],
            'receiver_phone' => ['required','regex:/^(0|\+84)\d{9,10}$/'],
            'full_address'   => ['required','string','max:255'],
            'is_default'     => ['nullable','boolean'],
        ],[
            'receiver_phone.regex' => 'Số điện thoại không hợp lệ.',
        ]);

        $data['user_id'] = Auth::id();
        $isDefault = (bool)($data['is_default'] ?? false);

        if($isDefault){
            // bỏ mặc định cũ
            Address::where('user_id', Auth::id())->update(['is_default'=>false]);
        }
        Address::create($data);

        return back()->with('success','Thêm địa chỉ thành công!');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $data = $request->validate([
            'receiver_name'  => ['required','string','max:255'],
            'receiver_phone' => ['required','regex:/^(0|\+84)\d{9,10}$/'],
            'full_address'   => ['required','string','max:255'],
        ],[
            'receiver_phone.regex' => 'Số điện thoại không hợp lệ.',
        ]);

        $address->update($data);
        return back()->with('success','Cập nhật địa chỉ thành công!');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);

        $address->delete();
        return back()->with('success','Đã xóa địa chỉ!');
    }

    public function makeDefault(Address $address)
    {
        $this->authorizeAddress($address);

        Address::where('user_id', Auth::id())->update(['is_default'=>false]);
        $address->update(['is_default'=>true]);

        return back()->with('success','Đã đặt làm địa chỉ mặc định!');
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === Auth::id(), 403);
    }
}
