@extends('layouts.app')

@section('title', 'Đơn hàng #' . $order->id . ' - GunplaHub')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h3 class="mb-0">Đơn hàng #{{ $order->id }}</h3>
    <span class="badge fs-6 text-bg-{{ $order->statusBadge() }}">{{ $order->status }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Sản phẩm --}}
        <div class="card shadow-sm mb-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $detail)
                            @php($product = $detail->product)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $product?->name ?? $detail->product_sku }}</div>
                                    <div class="small text-muted">{{ $detail->product_sku }}</div>
                                </td>
                                <td class="text-end">{{ \App\Support\Money::format($detail->unit_price) }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-end">{{ \App\Support\Money::format($detail->unit_price * $detail->quantity) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="mb-3">Khách hàng</h6>
                        <p class="mb-1">
                            @if($order->user)
                                <a href="{{ route('admin.customers.show', $order->user) }}" class="text-body fw-semibold">{{ $order->user->full_name }}</a>
                            @endif
                        </p>
                        <p class="mb-1 small text-muted">{{ $order->user?->email }}</p>
                        <p class="mb-0 small text-muted">{{ $order->user?->phone }}</p>
                        @if($order->user?->is_blacklisted)
                            <span class="badge text-bg-danger mt-2">Đang trong danh sách Blacklist</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="mb-3">Giao hàng</h6>
                        <p class="mb-1"><strong>{{ $order->receiver_name }}</strong> · {{ $order->receiver_phone }}</p>
                        <p class="mb-2">{{ $order->shipping_address }}</p>
                        <p class="mb-0 small text-muted">
                            Vận đơn:
                            @if($order->shipment)
                                {{ $order->shipment->ghn_order_code ?: 'Chưa có mã GHN' }} · {{ $order->shipment->status }}
                            @else
                                Chưa tạo
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Tổng tiền + thanh toán --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="mb-3">Tổng kết</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính</span>
                    <span>{{ \App\Support\Money::format($order->subtotal) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển</span>
                    <span>{{ (float) $order->shipping_fee > 0 ? \App\Support\Money::format($order->shipping_fee) : 'Miễn phí' }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá @if($order->voucher_code)<span class="text-muted">({{ $order->voucher_code }})</span>@endif</span>
                        <span class="text-success">-{{ \App\Support\Money::format($order->discount) }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-2">
                    <span>Tổng cộng</span>
                    <span class="text-danger">{{ \App\Support\Money::format($order->total_amount) }}</span>
                </div>
                <div class="small text-muted">
                    Thanh toán: {{ $order->payment?->payment_method ?? '—' }} · {{ $order->payment?->status ?? '—' }}
                </div>
                <div class="small text-muted">Đặt lúc {{ $order->order_date->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- Yêu cầu của khách đang chờ duyệt --}}
        @if($order->status === \App\Models\Order::CANCEL_PENDING)
            <div class="alert alert-warning">
                <strong>Khách yêu cầu hủy đơn</strong>
                <div class="small mt-1">Lý do: {{ $order->cancel_reason ?: '—' }}</div>
                <div class="small">Trạng thái trước đó: {{ $order->status_before_cancel ?: '—' }}</div>
            </div>
        @elseif($order->status === \App\Models\Order::RETURN_PENDING)
            <div class="alert alert-warning">
                <strong>Khách yêu cầu trả hàng</strong>
                <div class="small mt-1">Lý do: {{ $order->return_reason ?: '—' }}</div>
            </div>
        @elseif($order->status === \App\Models\Order::CANCELLED && $order->cancel_reason)
            <div class="alert alert-secondary small mb-3">Lý do hủy: {{ $order->cancel_reason }}</div>
        @endif

        {{-- Thao tác của Admin --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Thao tác</h6>

                @forelse($actions as $key => $action)
                    <form method="POST" action="{{ route('admin.orders.transition', $order) }}" class="mb-2"
                          onsubmit="return confirm('{{ $action['label'] }}?');">
                        @csrf
                        <input type="hidden" name="action" value="{{ $key }}">
                        @if($action['needs_note'])
                            <textarea name="note" rows="2" class="form-control mb-2" placeholder="Lý do*" required></textarea>
                        @endif
                        <button type="submit" class="btn btn-{{ $action['style'] }} w-100">{{ $action['label'] }}</button>
                    </form>
                @empty
                    <p class="small text-muted mb-0">Đơn hàng đã ở trạng thái cuối, không còn thao tác nào.</p>
                @endforelse

                @if($actions)
                    <div class="form-text mt-2">
                        Các bước vận chuyển (Chờ lấy hàng, Đã lấy, Đang giao...) hiện do Admin cập nhật tay.
                        Khi tích hợp GHN, các bước này sẽ được cập nhật tự động.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary mt-4">← Về danh sách đơn hàng</a>
@endsection
