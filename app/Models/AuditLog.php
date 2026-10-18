<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    /**
     * Ghi một dòng nhật ký. Ví dụ: AuditLog::record('Thêm sản phẩm HG-UC-001');
     * user_id lấy từ người đang đăng nhập (null nếu do hệ thống tự chạy).
     */
    public static function record(string $action): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => Str::limit($action, 250),
            'timestamp' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
