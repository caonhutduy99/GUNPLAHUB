<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    // Các trạng thái đơn hàng theo báo cáo (mục 1.2.3.c)
    public const PENDING = 'Chờ xác nhận';
    public const CONFIRMED = 'Xác nhận và Tạo mã vận đơn';
    public const AWAITING_PICKUP = 'Chờ lấy hàng';
    public const PICKED_UP = 'Đã lấy';
    public const SHIPPING = 'Đang giao';
    public const DELIVERED = 'Đã giao';
    public const CANCEL_PENDING = 'Chờ duyệt hủy';
    public const CANCELLED = 'Đã hủy';
    public const DELIVERY_FAILED = 'Giao thất bại';
    public const RETURNING = 'Đang chuyển hoàn';
    public const RETURNED_TO_SHOP = 'Đã hoàn hàng';
    public const RETURN_PENDING = 'Chờ duyệt trả hàng';
    public const RETURN_IN_PROGRESS = 'Đang trả hàng';
    public const REFUNDED = 'Đã trả hàng/Hoàn tiền';

    public const STATUSES = [
        self::PENDING, self::CONFIRMED, self::AWAITING_PICKUP, self::PICKED_UP, self::SHIPPING,
        self::DELIVERED, self::CANCEL_PENDING, self::CANCELLED, self::DELIVERY_FAILED,
        self::RETURNING, self::RETURNED_TO_SHOP, self::RETURN_PENDING, self::RETURN_IN_PROGRESS,
        self::REFUNDED,
    ];

    protected $fillable = [
        'user_id',
        'voucher_code',
        'order_date',
        'total_amount',
        'shipping_fee',
        'status',
        'cancel_reason',
        'status_before_cancel',
        'return_reason',
        'receiver_name',
        'receiver_phone',
        'shipping_address',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'total_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
        ];
    }

    /** Các đơn đang chờ Admin xử lý: chờ xác nhận, chờ duyệt hủy, chờ duyệt trả hàng */
    public function scopeNeedsAction($query)
    {
        return $query->whereIn('status', [self::PENDING, self::CANCEL_PENDING, self::RETURN_PENDING]);
    }

    /** Khách được tự hủy ngay (hoàn tồn kho tức thì) */
    public function canCancelFreely(): bool
    {
        return $this->status === self::PENDING;
    }

    /** Khách chỉ được gửi yêu cầu hủy, Admin duyệt */
    public function needsCancelApproval(): bool
    {
        return in_array($this->status, [self::CONFIRMED, self::AWAITING_PICKUP], true);
    }

    /** Không được hủy nữa */
    public function cancelLocked(): bool
    {
        return in_array($this->status, [self::PICKED_UP, self::SHIPPING, self::DELIVERED], true);
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::PENDING, self::CANCEL_PENDING, self::RETURN_PENDING => 'warning',
            self::CONFIRMED, self::AWAITING_PICKUP, self::PICKED_UP, self::SHIPPING => 'info',
            self::DELIVERED => 'success',
            self::DELIVERY_FAILED => 'danger',
            default => 'secondary',
        };
    }

    /** Tiền hàng (chưa gồm phí vận chuyển, chưa trừ giảm giá) */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->details->sum(fn ($d) => (float) $d->unit_price * $d->quantity);
    }

    /** Số tiền đã được giảm */
    public function getDiscountAttribute(): float
    {
        return max(0, $this->subtotal + (float) $this->shipping_fee - (float) $this->total_amount);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_code', 'code');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }
}
