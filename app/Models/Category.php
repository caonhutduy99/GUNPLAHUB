<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** Tên rút gọn để hiển thị ở bộ lọc: "Gunpla HG" -> "HG" */
    public function getShortNameAttribute(): string
    {
        return preg_replace('/^Gunpla\s+/', '', $this->name);
    }

    public function isGunpla(): bool
    {
        return str_starts_with($this->name, 'Gunpla');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
