<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const GRADES = ['EG', 'HG', 'RG', 'MG', 'PG'];
    public const SCALES = ['1/144', '1/100', '1/60'];
    public const STATUSES = ['Còn hàng', 'Hết hàng', 'Ngừng kinh doanh'];

    // Khóa chính là chuỗi SKU, không tự tăng
    protected $primaryKey = 'sku';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'category_id',
        'name',
        'price',
        'stock_quantity',
        'grade',
        'scale',
        'series',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    // Dùng SKU trên đường dẫn: /admin/products/HG-UC-001/edit
    public function getRouteKeyName(): string
    {
        return 'sku';
    }

    // Địa chỉ ảnh để hiển thị, null nếu chưa có ảnh
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'product_sku', 'sku');
    }
}
