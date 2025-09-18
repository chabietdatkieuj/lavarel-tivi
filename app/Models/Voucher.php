<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_percent', 'quantity',
        'start_at', 'end_at', 'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'quantity'         => 'integer',
        'is_active'        => 'boolean',
        'start_at'         => 'datetime',
        'end_at'           => 'datetime',
    ];

    // Dùng trong trang News nếu cần
    public function scopeActive($q)
    {
        return $q->where('is_active', 1)
                 ->where('start_at', '<=', now())
                 ->where('end_at', '>=', now())
                 ->where('quantity', '>', 0);
    }

    /**
     * Tính số tiền giảm cho hóa đơn $subtotal (VNĐ).
     * Trả về [discount(int), errorMsg|null]
     */
    public function computeDiscount(float $subtotal): array
    {
        if (!$this->is_active)            return [0, 'Voucher đã tắt.'];
        if ($this->start_at && now()->lt($this->start_at))
                                         return [0, 'Voucher chưa bắt đầu.'];
        if ($this->end_at && now()->gt($this->end_at))
                                         return [0, 'Voucher đã hết hạn.'];
        if ($this->quantity <= 0)         return [0, 'Voucher đã hết lượt sử dụng.'];

        $percent  = max(0, min(100, (int) $this->discount_percent));
        $discount = (int) round($subtotal * $percent / 100);

        return [$discount, null];
    }

    /** Trừ 1 lượt sử dụng */
    public function consumeOne(): void
    {
        $this->decrement('quantity', 1);
    }
}
