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

        $order->load(['details.product', 'payment', 'voucher', 'invoice', 'shipment']);

        return view('orders.show', compact('order'));
    }

    public function payment(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);
        $order->load('payment');
        abort_unless($order->payment && in_array($order->payment->payment_method, ['VNPAY', 'VIETQR'], true), 404);

        $reference = 'GUNPLA'.$order->id;
        $qrUrl = sprintf(
            'https://img.vietqr.io/image/%s-%s-compact2.png?amount=%s&addInfo=%s&accountName=%s',
            config('payments.vietqr.bank_id'),
            config('payments.vietqr.account_number'),
            (int) $order->total_amount,
            urlencode($reference),
            urlencode(config('payments.vietqr.account_name'))
        );

        return view('payments.show', [
            'order' => $order,
            'qrUrl' => $qrUrl,
            'accountName' => config('payments.vietqr.account_name'),
            'accountNumber' => config('payments.vietqr.account_number'),
        ]);
    }

    public function simulatePayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);
        abort_unless(config('payments.sandbox'), 404);

        $payment = $order->payment;
        abort_unless($payment && in_array($payment->payment_method, ['VNPAY', 'VIETQR'], true), 404);

        $payment->update([
            'status' => 'Thành công',
            'transaction_code' => 'DEMO-'.str()->upper(str()->random(12)),
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Thanh toán sandbox thành công.');
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
        $name = Str::uuid().'.'.$file->extension();
        $file->move(public_path('images/returns'), $name);
        $path = 'images/returns/'.$name;

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
