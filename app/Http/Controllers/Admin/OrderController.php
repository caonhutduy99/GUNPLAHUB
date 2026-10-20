<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->withCount('details');

        if ($request->filled('status') && in_array($request->input('status'), Order::STATUSES, true)) {
            $query->where('status', $request->input('status'));
        }

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('receiver_name', 'like', "%{$keyword}%")
                  ->orWhere('receiver_phone', 'like', "%{$keyword}%");
                if (ctype_digit(ltrim($keyword, '#'))) {
                    $w->orWhere('id', (int) ltrim($keyword, '#'));
                }
            });
        }

        return view('admin.orders.index', [
            'orders' => $query->latest('order_date')->latest('id')->paginate(15)->withQueryString(),
            'statuses' => Order::STATUSES,
            'needsAction' => Order::needsAction()->count(),
        ]);
    }

    public function show(Order $order, OrderService $orders)
    {
        $order->load(['user', 'details.product', 'payment', 'shipment', 'voucher', 'invoice']);

        return view('admin.orders.show', [
            'order' => $order,
            'actions' => $orders->availableActions($order),
        ]);
    }

    public function transition(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $data = $request->validate(
            [
                'action' => ['required', Rule::in(array_keys(OrderService::ACTIONS))],
                // Admin hủy đơn thì bắt buộc nêu lý do
                'note' => [Rule::requiredIf(fn () => $request->input('action') === 'cancel'), 'nullable', 'string', 'max:500'],
            ],
            ValidationMessages::common(),
            ['action' => 'Thao tác', 'note' => 'Lý do']
        );

        try {
            $updated = $orders->transition($order, $data['action'], $data['note'] ?? null);
        } catch (OrderException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Đơn #{$updated->id} đã chuyển sang trạng thái “{$updated->status}”.");
    }
}
