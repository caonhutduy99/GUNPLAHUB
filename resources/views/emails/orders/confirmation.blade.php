<x-mail::message>
# Đặt hàng thành công

Xin chào {{ $order->user->full_name }},

Cảm ơn bạn đã đặt hàng tại **{{ config('shop.name') }}**. Đơn hàng của bạn đã được ghi nhận với thông tin sau:

**Mã đơn hàng:** #{{ $order->id }}  
**Ngày đặt:** {{ $order->order_date->format('d/m/Y H:i') }}  
**Trạng thái:** {{ $order->status }}  
**Người nhận:** {{ $order->receiver_name }}  
**Số điện thoại:** {{ $order->receiver_phone }}  
**Địa chỉ:** {{ $order->shipping_address }}

| Sản phẩm | SL | Thành tiền |
|:--|--:|--:|
@foreach($order->details as $detail)
| {{ $detail->product?->name ?? $detail->product_sku }} | {{ $detail->quantity }} | {{ \App\Support\Money::format($detail->unit_price * $detail->quantity) }} |
@endforeach

**Phí vận chuyển:** {{ \App\Support\Money::format($order->shipping_fee) }}  
**Tổng thanh toán:** {{ \App\Support\Money::format($order->total_amount) }}

<x-mail::button :url="route('orders.show', $order)">
Xem chi tiết đơn hàng
</x-mail::button>

Trân trọng,<br>
{{ config('shop.name') }}
</x-mail::message>
