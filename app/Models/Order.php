<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id','shipping_name','shipping_phone','shipping_address',
        'total_amount','payment_method','status','note'
    ];

    public function items()   { return $this->hasMany(OrderItem::class); }
    public function user()    { return $this->belongsTo(User::class);    }
}
