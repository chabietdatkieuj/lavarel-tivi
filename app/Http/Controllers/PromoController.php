<?php

namespace App\Http\Controllers;

use App\Models\Voucher;

class PromoController extends Controller
{
    public function index() {
        $vouchers = Voucher::active()->orderBy('end_at')->get();
        return view('promos.index', compact('vouchers'));
    }
}
