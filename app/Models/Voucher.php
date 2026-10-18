<?php

namespace App\Models;

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
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'expiry_date' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'voucher_code', 'code');
    }
}
