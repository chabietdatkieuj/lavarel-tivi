<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate(['code'=>'required|string|max:50']);
        $code = trim($request->code);

        $cart  = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $items = $cart->items()->with('product')->get();
        if ($items->isEmpty()) {
            return back()->with('error','Giỏ hàng trống.');
        }
        $subtotal = $items->sum(fn($i) => $i->quantity * $i->price);

        $voucher = Voucher::where('code',$code)->first();
        if (!$voucher) {
            return back()->with('error','Mã không tồn tại.');
        }
        if (!$voucher->isUsableFor((float)$subtotal)) {
            return back()->with('error','Mã không còn hiệu lực hoặc không đủ điều kiện.');
        }

        $discount = $voucher->calculateDiscount((float)$subtotal);
        if ($discount <= 0) {
            return back()->with('error','Mã không áp dụng được.');
        }

        session(['checkout_voucher'=>[
            'id'       => $voucher->id,
            'code'     => $voucher->code,
            'discount' => (float) $discount,
            'subtotal' => (float) $subtotal,
        ]]);

        return back()->with('success','Đã áp dụng mã giảm giá: '.$voucher->code);
    }

    public function remove()
    {
        session()->forget('checkout_voucher');
        return back()->with('success','Đã bỏ mã giảm giá.');
    }
}
