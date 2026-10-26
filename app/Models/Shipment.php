<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'carrier',
        'ghn_order_code',
        'tracking_url',
        'status',
        'tracking_events',
    ];

    protected function casts(): array
    {
        return [
            'tracking_events' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
