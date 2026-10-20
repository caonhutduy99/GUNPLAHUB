<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /** Khách xem hóa đơn của chính mình */
    public function customer(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->render($order, route('orders.show', $order));
    }

    /** Admin xem hóa đơn của mọi đơn */
    public function admin(Order $order)
    {
        return $this->render($order, route('admin.orders.show', $order));
    }

    private function render(Order $order, string $backUrl)
    {
        $order->load(['invoice', 'details.product', 'payment', 'voucher']);

        // Chỉ đơn giao thành công mới có hóa đơn
        abort_unless($order->invoice, 404);

        return view('invoices.show', [
            'order' => $order,
            'invoice' => $order->invoice,
            'backUrl' => $backUrl,
        ]);
    }
}
