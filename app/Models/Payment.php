<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const PENDING = 'Chờ xử lý';

    public const SUCCESS = 'Thành công';

    public const FAILED = 'Thất bại';

    /** Đơn đã thanh toán online nhưng bị hủy: cửa hàng cần hoàn tiền thủ công. */
    public const REFUND_PENDING = 'Chờ hoàn tiền';

    public const REFUNDED = 'Đã hoàn tiền';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_code',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
