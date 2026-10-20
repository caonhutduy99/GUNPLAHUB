<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderException;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->withCount('details')
            ->latest('order_date')
            ->latest('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load(['details.product', 'payment', 'voucher', 'invoice']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        // Gửi yêu cầu hủy (cần Admin duyệt) thì bắt buộc nêu lý do
        $data = $request->validate(
            ['cancel_reason' => [
                Rule::requiredIf(fn () => $order->needsCancelApproval()),
                'nullable', 'string', 'max:500',
            ]],
            ValidationMessages::common(),
            ['cancel_reason' => 'Lý do hủy']
        );

        try {
            $result = $orders->cancelByCustomer($order, $data['cancel_reason'] ?? null);
        } catch (OrderException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $result === 'cancelled'
            ? 'Đơn hàng đã được hủy.'
            : 'Đã gửi yêu cầu hủy đơn. Vui lòng chờ cửa hàng duyệt.');
    }

    public function requestReturn(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        $data = $request->validate(
            [
                'return_reason' => ['required', 'string', 'min:10', 'max:500'],
                // Ảnh minh chứng lỗi sản phẩm (UC-12)
                'return_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            array_merge(ValidationMessages::common(), [
                'return_reason.min' => 'Vui lòng mô tả rõ lỗi của sản phẩm (tối thiểu 10 ký tự).',
                'return_image.required' => 'Vui lòng đính kèm ảnh minh chứng.',
            ]),
            ['return_reason' => 'Lý do trả hàng', 'return_image' => 'Ảnh minh chứng']
        );

        $file = $request->file('return_image');
        $name = Str::uuid() . '.' . $file->extension();
        $file->move(public_path('images/returns'), $name);
        $path = 'images/returns/' . $name;

        try {
            $orders->requestReturn($order, $data['return_reason'], $path);
        } catch (OrderException $e) {
            File::delete(public_path($path)); // yêu cầu không hợp lệ thì bỏ ảnh vừa tải lên

            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Đã gửi yêu cầu trả hàng. Vui lòng chờ cửa hàng duyệt.');
    }

    /** Khách chỉ được xem đơn của chính mình (trả 404 để không lộ đơn của người khác tồn tại) */
    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
