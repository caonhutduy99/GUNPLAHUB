<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderException;
use App\Mail\OrderConfirmation;
use App\Services\OrderService;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    /** Các phương thức thanh toán đang hỗ trợ (VNPay sẽ thêm ở bước tích hợp cổng thanh toán) */
    private const PAYMENT_METHODS = ['COD', 'VNPAY', 'VIETQR'];

    public function create(Request $request, OrderService $orders)
    {
        $user = $request->user();

        if ($user->is_blacklisted) {
            return redirect()->route('cart.index')
                ->with('error', 'Tài khoản của bạn đang bị hạn chế đặt hàng. Vui lòng liên hệ cửa hàng để được hỗ trợ.');
        }

        $code = $request->old('voucher_code', session('checkout_voucher'));
        $quote = $orders->quote($user, $code);

        if ($quote['lines']->isEmpty() && empty($quote['problems'])) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        if (! empty($quote['problems'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Một số sản phẩm trong giỏ không còn đủ hàng. Vui lòng kiểm tra lại giỏ hàng trước khi đặt.');
        }

        return view('checkout.create', $quote + ['user' => $user, 'code' => $code]);
    }

    /** Bấm "Áp dụng": kiểm tra mã và ghi nhớ trong session, giữ nguyên thông tin đã nhập. */
    public function applyVoucher(Request $request, OrderService $orders): RedirectResponse
    {
        $request->validate(
            ['voucher_code' => ['required', 'string', 'max:30']],
            ValidationMessages::common(),
            ['voucher_code' => 'Mã giảm giá']
        );

        $code = strtoupper(trim($request->input('voucher_code')));
        $quote = $orders->quote($request->user(), $code);

        if ($quote['voucherError']) {
            session()->forget('checkout_voucher');

            return back()->withInput()->withErrors(['voucher_code' => $quote['voucherError']]);
        }

        session(['checkout_voucher' => $code]);

        return back()->withInput(array_merge($request->except('_token'), ['voucher_code' => $code]));
    }

    public function removeVoucher(Request $request): RedirectResponse
    {
        session()->forget('checkout_voucher');

        return back()->withInput($request->except(['_token', 'voucher_code']));
    }

    public function store(Request $request, OrderService $orders): RedirectResponse
    {
        $data = $request->validate(
            [
                'receiver_name' => ['required', 'string', 'max:100'],
                'receiver_phone' => ['required', 'regex:/^(0|\+84)[0-9]{9}$/'],
                'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
                'payment_method' => ['required', Rule::in(self::PAYMENT_METHODS)],
                'voucher_code' => ['nullable', 'string', 'max:30'],
            ],
            array_merge(ValidationMessages::common(), [
                'receiver_phone.regex' => 'Số điện thoại không hợp lệ (ví dụ: 0912345678).',
                'shipping_address.min' => 'Vui lòng nhập địa chỉ đầy đủ (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành).',
            ]),
            [
                'receiver_name' => 'Tên người nhận',
                'receiver_phone' => 'Số điện thoại',
                'shipping_address' => 'Địa chỉ giao hàng',
                'payment_method' => 'Phương thức thanh toán',
                'voucher_code' => 'Mã giảm giá',
            ]
        );

        try {
            $order = $orders->place(
                $request->user(),
                $request->only(['receiver_name', 'receiver_phone', 'shipping_address']),
                $data['voucher_code'] ?? null,
                $data['payment_method']
            );
        } catch (OrderException $e) {
            if ($e->field) {
                return back()->withInput()->withErrors([$e->field => $e->getMessage()]);
            }

            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        session()->forget('checkout_voucher');
        $order->load(['user', 'details.product']);
        Mail::to($order->user->email)->send(new OrderConfirmation($order));

        return redirect()->route(
            in_array($data['payment_method'], ['VNPAY', 'VIETQR'], true) ? 'payments.show' : 'orders.show',
            $order
        )
            ->with('success', "Đặt hàng thành công! Mã đơn hàng của bạn là #{$order->id}.");
    }
}
