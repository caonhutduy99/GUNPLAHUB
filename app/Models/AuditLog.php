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
        'ip_address',
        'user_agent',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    /** Nhật ký chỉ được ghi thêm, không được sửa hay xóa (UC-18, BR-31). */
    protected static function booted(): void
    {
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    /**
     * Ghi một dòng nhật ký: Ai (user), Làm gì (action), Lúc nào (timestamp), Ở đâu (IP/thiết bị).
     * Ví dụ: AuditLog::record('Thêm sản phẩm BAN-HG-UC-001');
     */
    public static function record(string $action): void
    {
        $request = request();
        $userAgent = $request?->userAgent();

        static::create([
            'user_id' => auth()->id(),
            'action' => Str::limit($action, 250),
            'ip_address' => $request?->ip(),
            'user_agent' => $userAgent ? Str::limit($userAgent, 250, '') : null,
            'timestamp' => now(),
        ]);
    }

    /** Tên thiết bị rút gọn, ví dụ "Chrome / Windows" */
    public function getDeviceLabelAttribute(): string
    {
        $ua = (string) $this->user_agent;
        if ($ua === '') {
            return '—';
        }

        $os = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Khác',
        };

        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/'), str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'Chrome') => 'Chrome',
            str_contains($ua, 'Firefox') => 'Firefox',
            str_contains($ua, 'Safari') => 'Safari',
            default => 'Khác',
        };

        return "{$browser} / {$os}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
