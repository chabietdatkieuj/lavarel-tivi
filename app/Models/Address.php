<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','receiver_name','receiver_phone','full_address','is_default'
    ];

    protected $casts = ['is_default'=>'boolean'];

    public function user(){ return $this->belongsTo(User::class); }
}
