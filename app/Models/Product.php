<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','description','quantity','price','features','image','category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Mặc định lấy review mới nhất trước
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class)->latest();
    }
}
