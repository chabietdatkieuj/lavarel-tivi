<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index() {
        $vouchers = Voucher::latest()->paginate(12);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create() {
        return view('admin.vouchers.create');
    }

    public function store(Request $r) {
        $data = $r->validate([
            'code'             => 'required|string|max:50|unique:vouchers,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'quantity'         => 'required|integer|min:0',
            'start_at'         => 'required|date',
            'end_at'           => 'required|date|after:start_at',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = (bool) ($r->input('is_active', true));
        Voucher::create($data);
        return redirect()->route('admin.vouchers.index')->with('success','Đã tạo voucher.');
    }

    public function edit(Voucher $voucher) {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $r, Voucher $voucher) {
        $data = $r->validate([
            'code'             => 'required|string|max:50|unique:vouchers,code,'.$voucher->id,
            'discount_percent' => 'required|integer|min:1|max:100',
            'quantity'         => 'required|integer|min:0',
            'start_at'         => 'required|date',
            'end_at'           => 'required|date|after:start_at',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = (bool) ($r->input('is_active', true));
        $voucher->update($data);
        return redirect()->route('admin.vouchers.index')->with('success','Đã cập nhật voucher.');
    }

    public function destroy(Voucher $voucher) {
        $voucher->delete();
        return back()->with('success','Đã xoá voucher.');
    }
}
