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

    public const PRICE_TYPES = ['Giá bán lẻ', 'Giá niêm yết', 'Giá khuyến mãi', 'Giá thanh lý', 'Giá sỉ'];

    // Khóa chính là chuỗi SKU, không tự tăng
    protected $primaryKey = 'sku';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'sku',
        'category_id',
        'brand_id',
        'name',
        'cost_price',
        'price',
        'price_type',
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
            'cost_price' => 'decimal:2',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    // Dùng SKU trên đường dẫn: /admin/products/BAN-HG-UC-001/edit
    public function getRouteKeyName(): string
    {
        return 'sku';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'product_sku', 'sku');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'product_sku', 'sku')->latest();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_sku', 'sku')->orderBy('sort_order')->orderBy('id');
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class, 'product_sku', 'sku');
    }
}
