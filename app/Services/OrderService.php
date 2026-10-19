<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\AuditLog;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Support\Pricing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Toàn bộ nghiệp vụ đặt hàng và hủy đơn. Controller chỉ gọi vào đây.
 * Tồn kho được TRỪ NGAY khi đặt hàng và HOÀN LẠI khi đơn bị hủy.
 */
class OrderService
{
    /**
     * Tính trước các khoản tiền cho trang đặt hàng (chưa ghi gì vào CSDL).
     *
     * @return array{lines: Collection, problems: array, subtotal: float, shippingFee: int,
     *               discount: float, total: float, voucher: ?Voucher, voucherError: ?string}
     */
    public function quote(User $user, ?string $voucherCode = null): array
    {
        $items = $user->cartItems()->with('product')->get();

        $lines = collect();
        $problems = [];
        foreach ($items as $item) {
            $product = $item->product;
            if (! $product || $product->status !== 'Còn hàng' || $product->stock_quantity < $item->quantity) {
                $problems[] = $product?->name ?? $item->product_sku;
                continue;
            }
            $lines->push($item);
        }

        $subtotal = (float) $lines->sum(fn ($line) => (float) $line->product->price * $line->quantity);

        $voucher = null;
        $voucherError = null;
        $discount = 0.0;
        $voucherCode = strtoupper(trim((string) $voucherCode));
        if ($voucherCode !== '') {
            $found = Voucher::find($voucherCode);
            if (! $found) {
                $voucherError = 'Mã giảm giá không tồn tại.';
            } elseif ($error = $found->validationError($subtotal)) {
                $voucherError = $error;
            } else {
                $voucher = $found;
                $discount = $found->discountFor($subtotal);
            }
        }

        $shippingFee = $lines->isEmpty() ? 0 : Pricing::shippingFee($subtotal);

        return [
            'lines' => $lines,
            'problems' => $problems,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'discount' => $discount,
            'total' => $subtotal + $shippingFee - $discount,
            'voucher' => $voucher,
            'voucherError' => $voucherError,
        ];
    }

    /**
     * Tạo đơn hàng từ giỏ hàng, trong MỘT transaction:
     * kiểm tra lại tồn kho (khóa dòng), tính tiền, tạo đơn + chi tiết + thanh toán,
     * trừ tồn kho, tăng lượt dùng mã giảm giá, xóa giỏ hàng.
     */
    public function place(User $user, array $shipping, ?string $voucherCode, string $paymentMethod): Order
    {
        if ($user->is_blacklisted) {
            throw new OrderException('Tài khoản của bạn đang bị hạn chế đặt hàng. Vui lòng liên hệ cửa hàng để được hỗ trợ.');
        }

        return DB::transaction(function () use ($user, $shipping, $voucherCode, $paymentMethod) {
            $items = CartItem::where('user_id', $user->id)->get();
            if ($items->isEmpty()) {
                throw new OrderException('Giỏ hàng của bạn đang trống.');
            }

            // Khóa các dòng sản phẩm để hai người đặt cùng lúc không bán quá tồn kho
            $products = Product::whereIn('sku', $items->pluck('product_sku'))->lockForUpdate()->get()->keyBy('sku');

            $subtotal = 0.0;
            foreach ($items as $item) {
                $product = $products->get($item->product_sku);
                if (! $product || $product->status !== 'Còn hàng' || $product->stock_quantity < $item->quantity) {
                    $name = $product?->name ?? $item->product_sku;
                    throw new OrderException("Sản phẩm “{$name}” không còn đủ hàng. Vui lòng kiểm tra lại giỏ hàng.");
                }
                $subtotal += (float) $product->price * $item->quantity;
            }

            $voucher = null;
            $discount = 0.0;
            $voucherCode = strtoupper(trim((string) $voucherCode));
            if ($voucherCode !== '') {
                $voucher = Voucher::whereKey($voucherCode)->lockForUpdate()->first();
                if (! $voucher) {
                    throw new OrderException('Mã giảm giá không tồn tại.', 'voucher_code');
                }
                if ($error = $voucher->validationError($subtotal)) {
                    throw new OrderException($error, 'voucher_code');
                }
                $discount = $voucher->discountFor($subtotal);
            }

            $shippingFee = Pricing::shippingFee($subtotal);

            $order = Order::create([
                'user_id' => $user->id,
                'voucher_code' => $voucher?->code,
                'total_amount' => $subtotal + $shippingFee - $discount,
                'shipping_fee' => $shippingFee,
                'status' => Order::PENDING,
                'receiver_name' => $shipping['receiver_name'],
                'receiver_phone' => $shipping['receiver_phone'],
                'shipping_address' => $shipping['shipping_address'],
            ]);

            foreach ($items as $item) {
                $product = $products->get($item->product_sku);

                // Lưu đơn giá tại thời điểm đặt để sau này đổi giá không ảnh hưởng đơn cũ
                $order->details()->create([
                    'product_sku' => $product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price,
                ]);

                $newStock = $product->stock_quantity - $item->quantity;
                $product->update([
                    'stock_quantity' => $newStock,
                    'status' => $newStock > 0 ? 'Còn hàng' : 'Hết hàng',
                ]);
            }

            if ($voucher) {
                $voucher->increment('used_count');
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'status' => 'Chờ xử lý',
            ]);

            CartItem::where('user_id', $user->id)->delete();

            AuditLog::record("Khách đặt đơn #{$order->id} (" . $paymentMethod . ")");

            return $order;
        });
    }

    /**
     * Khách hủy đơn.
     * - "Chờ xác nhận": hủy ngay và hoàn tồn kho.
     * - "Xác nhận và Tạo mã vận đơn" / "Chờ lấy hàng": chỉ gửi yêu cầu, chờ Admin duyệt.
     * - Các trạng thái khác: không được hủy.
     *
     * @return string 'cancelled' hoặc 'requested'
     */
    public function cancelByCustomer(Order $order, ?string $reason = null): string
    {
        return DB::transaction(function () use ($order, $reason) {
            // Khóa đơn để tránh vừa hủy vừa bị Admin xác nhận cùng lúc
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->canCancelFreely()) {
                $this->restock($order);
                $order->update(['status' => Order::CANCELLED, 'cancel_reason' => $reason]);
                AuditLog::record("Khách hủy đơn #{$order->id} (Chờ xác nhận -> Đã hủy)");

                return 'cancelled';
            }

            if ($order->needsCancelApproval()) {
                $before = $order->status;
                $order->update([
                    'status_before_cancel' => $before,
                    'status' => Order::CANCEL_PENDING,
                    'cancel_reason' => $reason,
                ]);
                AuditLog::record("Khách gửi yêu cầu hủy đơn #{$order->id} ({$before} -> Chờ duyệt hủy)");

                return 'requested';
            }

            if ($order->status === Order::CANCEL_PENDING) {
                throw new OrderException('Yêu cầu hủy của bạn đang chờ cửa hàng duyệt.');
            }

            throw new OrderException("Đơn hàng đang ở trạng thái “{$order->status}” nên không thể hủy.");
        });
    }

    /** Hoàn tồn kho cho các sản phẩm của đơn và trả lại lượt dùng mã giảm giá. Gọi trong transaction. */
    public function restock(Order $order): void
    {
        $order->loadMissing('details');

        foreach ($order->details as $detail) {
            $product = Product::whereKey($detail->product_sku)->lockForUpdate()->first();
            if (! $product) {
                continue;
            }

            $product->update([
                'stock_quantity' => $product->stock_quantity + $detail->quantity,
                // Sản phẩm đã ngừng kinh doanh thì giữ nguyên trạng thái
                'status' => $product->status === 'Ngừng kinh doanh' ? $product->status : 'Còn hàng',
            ]);
        }

        if ($order->voucher_code) {
            Voucher::whereKey($order->voucher_code)->where('used_count', '>', 0)->decrement('used_count');
        }
    }
}
