<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'discount_amount',
        'min_order_value',
        'usage_limit',
        'used_count',
        'expiry_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'expiry_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /** Trạng thái hiển thị của mã giảm giá */
    public function status(): string
    {
        if (isset($this->is_active) && ! $this->is_active) {
            return 'Tạm ngừng';
        }
        if ($this->isExpired()) {
            return 'Hết hạn';
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'Hết lượt';
        }

        return 'Đang áp dụng';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status()) {
            'Đang áp dụng' => 'bg-success',
            'Hết hạn' => 'bg-danger',
            'Hết lượt' => 'bg-warning text-dark',
            'Tạm ngừng' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /** Trả về thông báo lỗi nếu mã không dùng được cho đơn có tiền hàng $subtotal, ngược lại trả null. */
    public function validationError(float $subtotal): ?string
    {
        if (isset($this->is_active) && ! $this->is_active) {
            return 'Mã giảm giá đã bị tạm ngừng áp dụng.';
        }
        if ($this->isExpired()) {
            return 'Mã giảm giá đã hết hạn.';
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'Mã giảm giá đã hết lượt sử dụng.';
        }
        if ($subtotal < (float) $this->min_order_value) {
            return 'Đơn hàng cần tối thiểu ' . Money::format($this->min_order_value) . ' để áp dụng mã này.';
        }

        return null;
    }

    /** Số tiền được giảm (không vượt quá tiền hàng) */
    public function discountFor(float $subtotal): float
    {
        return min((float) $this->discount_amount, $subtotal);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'voucher_code', 'code');
    }
}
