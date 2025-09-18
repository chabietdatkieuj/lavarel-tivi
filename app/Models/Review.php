<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
    ];

    // (Tuỳ chọn) luôn load user để tránh N+1
    protected $with = ['user'];

    protected $casts = [
        'rating' => 'integer',
    ];

    /** Quan hệ tới người dùng */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Quan hệ tới sản phẩm */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Trả lời của admin cho review này */
    public function replies(): HasMany
    {
        // Cần App\Models\ReviewReply (model đơn giản với fillable: review_id, admin_id, content)
        return $this->hasMany(ReviewReply::class)->latest();
    }

    /** Đảm bảo rating luôn nằm trong khoảng 1–5 */
    public function setRatingAttribute($value): void
    {
        $v = (int) $value;
        if ($v < 1) $v = 1;
        if ($v > 5) $v = 5;
        $this->attributes['rating'] = $v;
    }

    /**
     * Scope tiện lọc (dùng trong admin):
     * Review::with(['user','product'])->filter($productId, $rating)->latest()->paginate();
     */
    public function scopeFilter($q, ?int $productId = null, ?int $rating = null)
    {
        return $q
            ->when($productId, fn($qq) => $qq->where('product_id', $productId))
            ->when($rating,    fn($qq) => $qq->where('rating', $rating));
    }
}
