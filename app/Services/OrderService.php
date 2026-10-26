<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\AuditLog;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Support\Pricing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Toàn bộ nghiệp vụ đặt hàng và hủy đơn. Controller chỉ gọi vào đây.
 * Tồn kho được TRỪ NGAY khi đặt hàng và HOÀN LẠI khi đơn bị hủy.
 */
class OrderService
{
    /**
     * Các thao tác Admin được phép làm, theo luồng trạng thái trong báo cáo.
     * from: các trạng thái hiện tại cho phép; to: trạng thái mới (null = tính khi chạy).
     */
    public const ACTIONS = [
        'confirm' => ['from' => [Order::PENDING], 'to' => Order::CONFIRMED, 'label' => 'Xác nhận đơn', 'style' => 'success'],
        'cancel' => ['from' => [Order::PENDING], 'to' => Order::CANCELLED, 'label' => 'Hủy đơn', 'style' => 'danger'],
        'to_pickup' => ['from' => [Order::CONFIRMED], 'to' => Order::AWAITING_PICKUP, 'label' => 'Chuyển sang Chờ lấy hàng', 'style' => 'primary'],
        'picked_up' => ['from' => [Order::AWAITING_PICKUP], 'to' => Order::PICKED_UP, 'label' => 'Đã lấy hàng', 'style' => 'primary'],
        'start_shipping' => ['from' => [Order::PICKED_UP], 'to' => Order::SHIPPING, 'label' => 'Bắt đầu giao hàng', 'style' => 'primary'],
        'delivered' => ['from' => [Order::SHIPPING], 'to' => Order::DELIVERED, 'label' => 'Giao thành công', 'style' => 'success'],
        'delivery_failed' => ['from' => [Order::SHIPPING], 'to' => Order::DELIVERY_FAILED, 'label' => 'Giao thất bại', 'style' => 'danger'],
        'start_return' => ['from' => [Order::DELIVERY_FAILED], 'to' => Order::RETURNING, 'label' => 'Chuyển hoàn', 'style' => 'primary'],
        'returned' => ['from' => [Order::RETURNING], 'to' => Order::RETURNED_TO_SHOP, 'label' => 'Đã hoàn hàng về kho', 'style' => 'success'],
        'approve_cancel' => ['from' => [Order::CANCEL_PENDING], 'to' => Order::CANCELLED, 'label' => 'Đồng ý hủy đơn', 'style' => 'danger'],
        'reject_cancel' => ['from' => [Order::CANCEL_PENDING], 'to' => null, 'label' => 'Từ chối hủy (đơn tiếp tục)', 'style' => 'secondary'],
        'approve_return' => ['from' => [Order::RETURN_PENDING], 'to' => Order::RETURN_IN_PROGRESS, 'label' => 'Đồng ý trả hàng', 'style' => 'success'],
        'reject_return' => ['from' => [Order::RETURN_PENDING], 'to' => Order::DELIVERED, 'label' => 'Từ chối trả hàng', 'style' => 'secondary'],
        'refund' => ['from' => [Order::RETURN_IN_PROGRESS], 'to' => Order::REFUNDED, 'label' => 'Hoàn tiền & Hoàn tất', 'style' => 'success'],
    ];

    /** Những trạng thái được ghi sang bảng vận đơn (shipments) */
    private const SHIPMENT_STATUSES = [
        Order::CONFIRMED, Order::AWAITING_PICKUP, Order::PICKED_UP, Order::SHIPPING, Order::DELIVERED,
        Order::DELIVERY_FAILED, Order::RETURNING, Order::RETURNED_TO_SHOP, Order::CANCELLED,
    ];

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

            AuditLog::record("Khách đặt đơn #{$order->id} (".$paymentMethod.')');

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
                $this->failPayment($order);
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
    public function restock(Order $order, bool $returnVoucher = true): void
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

        if ($returnVoucher && $order->voucher_code) {
            Voucher::whereKey($order->voucher_code)->where('used_count', '>', 0)->decrement('used_count');
        }
    }

    /** Khách gửi yêu cầu trả hàng/hoàn tiền cho đơn đã giao (chỉ khi hàng lỗi/hư hỏng). */
    public function requestReturn(Order $order, string $reason, ?string $imagePath = null): void
    {
        DB::transaction(function () use ($order, $reason, $imagePath) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::DELIVERED) {
                throw new OrderException('Chỉ đơn hàng đã giao mới có thể yêu cầu trả hàng.');
            }

            // Chỉ được yêu cầu trả hàng trong vòng 7 ngày kể từ khi nhận hàng (UC-12)
            if ($order->delivered_at && $order->delivered_at->lt(now()->subDays(7))) {
                throw new OrderException('Đã quá 7 ngày kể từ khi nhận hàng nên không thể yêu cầu trả hàng.');
            }

            $order->update([
                'status' => Order::RETURN_PENDING,
                'return_reason' => $reason,
                'return_image' => $imagePath,
            ]);
            AuditLog::record("Khách yêu cầu trả hàng đơn #{$order->id} (Đã giao -> Chờ duyệt trả hàng)");
        });
    }

    /** Các thao tác Admin có thể làm với đơn ở trạng thái hiện tại. */
    public function availableActions(Order $order): array
    {
        $result = [];
        foreach (self::ACTIONS as $key => $definition) {
            if (in_array($order->status, $definition['from'], true)) {
                $result[$key] = $definition + ['needs_note' => $key === 'cancel'];
            }
        }

        return $result;
    }

    /**
     * Admin chuyển trạng thái đơn hàng theo đúng luồng, kèm các tác động đi theo:
     * hoàn tồn kho, cập nhật thanh toán, cập nhật vận đơn, ghi Audit.
     */
    public function transition(Order $order, string $action, ?string $note = null, ?ShipmentService $shipments = null): Order
    {
        $definition = self::ACTIONS[$action] ?? throw new OrderException('Thao tác không hợp lệ.');
        $shipments ??= app(ShipmentService::class);

        return DB::transaction(function () use ($order, $action, $note, $definition, $shipments) {
            $order = Order::with('details')->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! in_array($order->status, $definition['from'], true)) {
                throw new OrderException("Không thể “{$definition['label']}” khi đơn đang ở trạng thái “{$order->status}”.");
            }

            $from = $order->status;
            // Từ chối hủy: đơn quay lại trạng thái trước khi khách gửi yêu cầu
            $to = $definition['to'] ?? ($order->status_before_cancel ?: Order::AWAITING_PICKUP);
            $updates = ['status' => $to];

            switch ($action) {
                case 'confirm':
                    $shipments->create($order);
                    break;

                case 'cancel':
                    $this->restock($order);
                    $this->failPayment($order);
                    $updates['cancel_reason'] = $note;
                    break;

                case 'approve_cancel':
                    // TODO (tích hợp GHN): gọi API Hủy đơn sang GHN trước khi hủy
                    $this->restock($order);
                    $this->failPayment($order);
                    $updates['status_before_cancel'] = null;
                    break;

                case 'reject_cancel':
                    $updates['status_before_cancel'] = null;
                    $updates['cancel_reason'] = null;
                    break;

                case 'delivered':
                    // COD: đã thu tiền khi giao hàng
                    Payment::where('order_id', $order->id)
                        ->where('payment_method', 'COD')
                        ->where('status', 'Chờ xử lý')
                        ->update(['status' => 'Thành công']);

                    $updates['delivered_at'] = now();
                    $this->issueInvoice($order);
                    break;

                case 'returned':
                    // Hàng giao thất bại đã về kho: hoàn tồn kho (lượt dùng mã giảm giá không trả lại)
                    $this->restock($order, false);
                    $this->failPayment($order);
                    break;

                case 'reject_return':
                    $updates['return_reason'] = null;
                    break;

                case 'refund':
                    // UC-12: Admin đã kiểm tra hàng trả còn nguyên vẹn nên cộng lại tồn kho
                    // (lượt dùng mã giảm giá không trả lại). Hoàn tiền online sẽ làm khi tích hợp VNPay.
                    $this->restock($order, false);
                    break;

                    // approve_return: chỉ đổi trạng thái.
            }

            $order->update($updates);

            if (in_array($to, self::SHIPMENT_STATUSES, true)) {
                $shipments->updateStatus($order, $to);
            }

            AuditLog::record("Admin đổi trạng thái đơn #{$order->id}: {$from} -> {$to}".($note ? " | {$note}" : ''));

            return $order;
        });
    }

    /** Đơn không thành công thì khoản thanh toán đang chờ được ghi nhận là thất bại. */
    private function failPayment(Order $order): void
    {
        Payment::where('order_id', $order->id)
            ->where('status', 'Chờ xử lý')
            ->update(['status' => 'Thất bại']);
    }

    /** Mỗi đơn giao thành công có đúng một hóa đơn với mã số duy nhất, ví dụ HD202610-000001. */
    private function issueInvoice(Order $order): Invoice
    {
        $existing = Invoice::where('order_id', $order->id)->first();
        if ($existing) {
            return $existing;
        }

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => (string) Str::uuid(), // tạm thời, đổi ngay khi biết id
            'issued_at' => now(),
            'total_amount' => $order->total_amount,
        ]);

        $invoice->update([
            'invoice_number' => sprintf('HD%s-%06d', now()->format('Ym'), $invoice->id),
        ]);

        return $invoice;
    }
}
