<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $fillable = [
        'product_sku',
        'user_id',
        'old_cost_price',
        'cost_price',
        'old_price',
        'price',
        'price_type',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'old_cost_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_sku', 'sku');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Chênh lệch giữa giá bán và giá nhập (lợi nhuận gộp trên 1 sản phẩm) */
    public function margin(): float
    {
        return (float) $this->price - (float) $this->cost_price;
    }

    /** Tỷ lệ lợi nhuận gộp theo giá vốn (%) */
    public function marginPercent(): float
    {
        if ((float) $this->cost_price <= 0) {
            return 100.0;
        }

        return round((($this->margin()) / (float) $this->cost_price) * 100, 1);
    }
}
