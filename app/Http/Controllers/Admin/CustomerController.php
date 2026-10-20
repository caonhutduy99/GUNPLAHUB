<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\User;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            // Tổng chi tiêu chỉ tính các đơn đã giao thành công
            ->withSum(['orders as total_spent' => fn ($q) => $q->where('status', Order::DELIVERED)], 'total_amount');

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('full_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        match ($request->input('status')) {
            'blacklist' => $query->where('is_blacklisted', true),
            'normal' => $query->where('is_blacklisted', false),
            default => null,
        };

        return view('admin.customers.index', [
            'customers' => $query->latest()->paginate(15)->withQueryString(),
            'blacklistCount' => User::where('role', 'customer')->where('is_blacklisted', true)->count(),
        ]);
    }

    public function show(User $customer)
    {
        $this->ensureCustomer($customer);

        $orders = $customer->orders();

        $stats = [
            'orders' => (clone $orders)->count(),
            'delivered' => (clone $orders)->where('status', Order::DELIVERED)->count(),
            'cancelled' => (clone $orders)->where('status', Order::CANCELLED)->count(),
            // Giao thất bại là dấu hiệu "bom hàng"
            'failed' => (clone $orders)->whereIn('status', [
                Order::DELIVERY_FAILED, Order::RETURNING, Order::RETURNED_TO_SHOP,
            ])->count(),
            'spent' => (float) (clone $orders)->where('status', Order::DELIVERED)->sum('total_amount'),
        ];

        // Các địa chỉ giao hàng gần đây (không trùng)
        $addresses = $customer->orders()->latest('order_date')->take(20)
            ->get(['receiver_name', 'receiver_phone', 'shipping_address'])
            ->unique('shipping_address')->take(5);

        return view('admin.customers.show', [
            'customer' => $customer->load('blacklistedBy'),
            'stats' => $stats,
            'addresses' => $addresses,
            'orders' => $customer->orders()->withCount('details')->latest('order_date')->latest('id')->paginate(10),
            'reasons' => User::BLACKLIST_REASONS,
        ]);
    }

    public function blacklist(Request $request, User $customer): RedirectResponse
    {
        $this->ensureCustomer($customer);

        if ($customer->is_blacklisted) {
            return back()->with('error', 'Khách hàng này đã nằm trong Blacklist.');
        }

        $data = $request->validate(
            [
                'blacklist_reason' => ['required', Rule::in(User::BLACKLIST_REASONS)],
                'blacklist_note' => ['nullable', 'string', 'max:500'],
            ],
            ValidationMessages::common(),
            ['blacklist_reason' => 'Lý do', 'blacklist_note' => 'Ghi chú']
        );

        $customer->forceFill([
            'is_blacklisted' => true,
            'blacklist_reason' => $data['blacklist_reason'],
            'blacklist_note' => $data['blacklist_note'] ?? null,
            'blacklisted_at' => now(),
            'blacklisted_by' => $request->user()->id,
        ])->save();

        AuditLog::record("Đưa khách #{$customer->id} ({$customer->full_name}) vào Blacklist: {$data['blacklist_reason']}");

        return back()->with('success', 'Đã đưa khách hàng vào Blacklist. Khách sẽ không đặt hàng được nữa.');
    }

    public function unblacklist(Request $request, User $customer): RedirectResponse
    {
        $this->ensureCustomer($customer);

        if (! $customer->is_blacklisted) {
            return back()->with('error', 'Khách hàng này không nằm trong Blacklist.');
        }

        $customer->forceFill([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'blacklist_note' => null,
            'blacklisted_at' => null,
            'blacklisted_by' => null,
        ])->save();

        AuditLog::record("Gỡ khách #{$customer->id} ({$customer->full_name}) khỏi Blacklist");

        return back()->with('success', 'Đã gỡ khách hàng khỏi Blacklist.');
    }

    /** Trang này chỉ dành cho khách hàng, tài khoản admin trả 404 */
    private function ensureCustomer(User $user): void
    {
        abort_if($user->isAdmin(), 404);
    }
}
