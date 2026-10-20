@extends('layouts.app')

@section('title', 'Hóa đơn ' . $invoice->invoice_number)
@section('no_navbar', '1')

@section('content')
<div class="no-print d-flex justify-content-between mb-3">
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">← Quay lại</a>
    <button type="button" class="btn btn-dark" onclick="window.print()"><i class="bi bi-printer me-1"></i>In hóa đơn / Lưu PDF</button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ config('shop.name') }}</h4>
                @if(config('shop.address'))<div class="small">Địa chỉ: {{ config('shop.address') }}</div>@endif
                <div class="small">Điện thoại: {{ config('shop.phone') }}</div>
                @if(config('shop.email'))<div class="small">Email: {{ config('shop.email') }}</div>@endif
            </div>
            <div class="text-md-end">
                <h5 class="fw-bold text-uppercase mb-1">Hóa đơn bán hàng</h5>
                <div class="small">Số: <strong>{{ $invoice->invoice_number }}</strong></div>
                <div class="small">Ngày xuất: {{ $invoice->issued_at->format('d/m/Y H:i') }}</div>
                <div class="small">Đơn hàng: #{{ $order->id }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="fw-semibold mb-1">Người nhận</div>
                <div>{{ $order->receiver_name }}</div>
                <div class="small">{{ $order->receiver_phone }}</div>
                <div class="small">{{ $order->shipping_address }}</div>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="fw-semibold mb-1">Thanh toán</div>
                @php($methods = ['COD' => 'Thanh toán khi nhận hàng (COD)', 'VNPAY' => 'VNPay', 'MOMO' => 'MoMo'])
                <div>{{ $methods[$order->payment?->payment_method] ?? '—' }}</div>
                <div class="small">Trạng thái: {{ $order->payment?->status ?? '—' }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">STT</th>
                        <th>Sản phẩm</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $detail->product?->name ?? $detail->product_sku }}
                                <div class="small text-muted">{{ $detail->product_sku }}</div>
                            </td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-end">{{ \App\Support\Money::format($detail->unit_price) }}</td>
                            <td class="text-end">{{ \App\Support\Money::format($detail->unit_price * $detail->quantity) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-5">
                <div class="d-flex justify-content-between mb-1"><span>Tạm tính</span><span>{{ \App\Support\Money::format($order->subtotal) }}</span></div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Phí vận chuyển</span>
                    <span>{{ (float) $order->shipping_fee > 0 ? \App\Support\Money::format($order->shipping_fee) : 'Miễn phí' }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-1">
                        <span>Giảm giá @if($order->voucher_code)({{ $order->voucher_code }})@endif</span>
                        <span>-{{ \App\Support\Money::format($order->discount) }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Tổng thanh toán</span>
                    <span>{{ \App\Support\Money::format($invoice->total_amount) }}</span>
                </div>
            </div>
        </div>

        @if($order->status === \App\Models\Order::REFUNDED)
            <div class="alert alert-secondary mt-4 mb-0 small">Đơn hàng này đã được trả hàng và hoàn tiền.</div>
        @endif

        <div class="text-center text-muted small mt-5">Cảm ơn quý khách đã mua hàng tại {{ config('shop.name') }}.</div>
    </div>
</div>
@endsection
