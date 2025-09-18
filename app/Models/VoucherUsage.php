<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    protected $fillable = ['voucher_id','user_id','order_id','used_at'];
    public $timestamps = false;

    public function voucher(){ return $this->belongsTo(Voucher::class); }
    public function order(){ return $this->belongsTo(Order::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
