<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Voucher;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($search = trim((string) $request->input('q'))) {
            $query->where('code', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true)
                    ->where('expiry_date', '>', now())
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
                    });
            } elseif ($status === 'expired') {
                $query->where('expiry_date', '<=', now());
            } elseif ($status === 'depleted') {
                $query->whereNotNull('usage_limit')->whereColumn('used_count', '>=', 'usage_limit');
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $vouchers = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:vouchers,code'],
            'discount_amount' => ['required', 'numeric', 'min:1000', 'max:99999999'],
            'min_order_value' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'expiry_date' => ['required', 'date', 'after:now'],
            'is_active' => ['nullable', 'boolean'],
        ], array_merge(ValidationMessages::common(), [
            'code.regex' => 'Mã giảm giá chỉ được chứa chữ cái, số, gạch ngang và gạch dưới.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'expiry_date.after' => 'Ngày hết hạn phải ở tương lai.',
        ]), [
            'code' => 'Mã giảm giá',
            'discount_amount' => 'Số tiền giảm',
            'min_order_value' => 'Giá trị đơn tối thiểu',
            'usage_limit' => 'Giới hạn số lượt dùng',
            'expiry_date' => 'Ngày hết hạn',
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);
        $data['used_count'] = 0;

        $voucher = Voucher::create($data);
        AuditLog::record("Tạo mã giảm giá {$voucher->code} (giảm " . number_format($voucher->discount_amount) . "đ)");

        return redirect()->route('admin.vouchers.index')->with('success', "Đã tạo mã giảm giá {$voucher->code}.");
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $minUsage = $voucher->used_count > 0 ? $voucher->used_count : 1;

        $data = $request->validate([
            'discount_amount' => ['required', 'numeric', 'min:1000', 'max:99999999'],
            'min_order_value' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'usage_limit' => ['nullable', 'integer', "min:{$minUsage}", 'max:1000000'],
            'expiry_date' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ], array_merge(ValidationMessages::common(), [
            'usage_limit.min' => "Giới hạn lượt dùng không được nhỏ hơn số lượt đã dùng ({$voucher->used_count}).",
        ]), [
            'discount_amount' => 'Số tiền giảm',
            'min_order_value' => 'Giá trị đơn tối thiểu',
            'usage_limit' => 'Giới hạn số lượt dùng',
            'expiry_date' => 'Ngày hết hạn',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $voucher->update($data);
        AuditLog::record("Cập nhật mã giảm giá {$voucher->code}");

        return redirect()->route('admin.vouchers.index')->with('success', "Đã cập nhật mã giảm giá {$voucher->code}.");
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        if ($voucher->orders()->exists()) {
            $voucher->update(['is_active' => false]);
            AuditLog::record("Tạm ngừng áp dụng mã giảm giá {$voucher->code} (đã có đơn hàng)");

            return redirect()->route('admin.vouchers.index')
                ->with('warning', "Mã {$voucher->code} đã có đơn hàng sử dụng nên không thể xóa hoàn toàn; hệ thống đã chuyển sang trạng thái Tạm ngừng.");
        }

        $code = $voucher->code;
        $voucher->delete();
        AuditLog::record("Xóa mã giảm giá {$code}");

        return redirect()->route('admin.vouchers.index')->with('success', "Đã xóa mã giảm giá {$code}.");
    }
}
