<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    /** Chỉ xem và tra cứu, không có thao tác sửa hay xóa nhật ký */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where('action', 'like', "%{$keyword}%");
        }
        if ($request->filled('user_id') && is_numeric($request->input('user_id'))) {
            $query->where('user_id', (int) $request->input('user_id'));
        }
        if ($from = $this->date($request->input('from'))) {
            $query->where('timestamp', '>=', $from->startOfDay());
        }
        if ($to = $this->date($request->input('to'))) {
            $query->where('timestamp', '<=', $to->endOfDay());
        }

        return view('admin.audit.index', [
            'logs' => $query->latest('timestamp')->latest('id')->paginate(25)->withQueryString(),
            'users' => User::whereIn('id', AuditLog::whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->orderBy('full_name')->get(['id', 'full_name', 'role']),
        ]);
    }

    private function date(?string $value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
