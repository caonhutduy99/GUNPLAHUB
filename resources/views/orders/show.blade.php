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
                            @php $product = $detail->product @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($product?->image_url)
                                            <img src="{{ $product->image_url }}" alt="" style="width: 56px; height: 56px; object-fit: contain;" class="border rounded bg-white">
                                        @else
                                            <div class="bg-secondary-subtle rounded" style="width: 56px; height: 56px;"></div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $product?->name ?? $detail->product_sku }}</div>
                                            <div class="small text-muted">{{ $detail->product_sku }}</div>
                                        </div>
                                    </div>
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

        {{-- Giao hàng và thanh toán --}}
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-3">Thông tin giao hàng</h5>
                <p class="mb-1"><strong>Người nhận:</strong> {{ $order->receiver_name }}</p>
                <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                <p class="mb-3"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>

                <h5 class="mb-2">Thanh toán</h5>
                <p class="mb-0">
                    @php
                        $methods = ['COD' => 'Thanh toán khi nhận hàng (COD)', 'VNPAY' => 'VNPay', 'MOMO' => 'MoMo'];
                    @endphp
                    {{ $methods[$order->payment?->payment_method] ?? '—' }}
                    @if($order->payment)
                        <span class="text-muted">· {{ $order->payment->status }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Tổng tiền --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="mb-3">Tổng kết</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính</span>
                    <span>{{ \App\Support\Money::format($order->subtotal) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển</span>
                    @if((float) $order->shipping_fee > 0)
                        <span>{{ \App\Support\Money::format($order->shipping_fee) }}</span>
                    @else
                        <span class="text-success">Miễn phí</span>
                    @endif
                </div>
                @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá @if($order->voucher_code)<span class="text-muted">({{ $order->voucher_code }})</span>@endif</span>
                        <span class="text-success">-{{ \App\Support\Money::format($order->discount) }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Tổng cộng</span>
                    <span class="text-danger">{{ \App\Support\Money::format($order->total_amount) }}</span>
                </div>
                <div class="small text-muted mt-2">Đặt lúc {{ $order->order_date->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- Hủy đơn --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Hủy đơn hàng</h6>

                @if($order->canCancelFreely())
                    <p class="small text-muted">Bạn có thể hủy ngay khi đơn đang chờ xác nhận.</p>
                    <form method="POST" action="{{ route('orders.cancel', $order) }}"
                          onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">Hủy đơn</button>
                    </form>

                @elseif($order->needsCancelApproval())
                    <p class="small text-muted">Đơn hàng đã được xác nhận nên cần gửi yêu cầu để cửa hàng duyệt.</p>
                    <form method="POST" action="{{ route('orders.cancel', $order) }}">
                        @csrf
                        <textarea name="cancel_reason" rows="3" placeholder="Lý do hủy đơn*"
                                  class="form-control mb-2 @error('cancel_reason') is-invalid @enderror" required>{{ old('cancel_reason') }}</textarea>
                        @error('cancel_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-outline-danger w-100">Gửi yêu cầu hủy</button>
                    </form>

                @elseif($order->status === \App\Models\Order::CANCEL_PENDING)
                    <p class="small mb-0">Yêu cầu hủy của bạn đang chờ cửa hàng duyệt.</p>
                    @if($order->cancel_reason)
                        <p class="small text-muted mb-0 mt-2">Lý do: {{ $order->cancel_reason }}</p>
                    @endif

                @elseif($order->cancelLocked())
                    <button type="button" class="btn btn-outline-danger w-100" disabled>Hủy đơn</button>
                    <p class="small text-muted mt-2 mb-0">Đơn hàng đã được lấy hoặc đang giao nên không thể hủy.</p>

                @elseif($order->status === \App\Models\Order::CANCELLED)
                    <p class="small mb-0">Đơn hàng đã được hủy.</p>
                    @if($order->cancel_reason)
                        <p class="small text-muted mb-0 mt-2">Lý do: {{ $order->cancel_reason }}</p>
                    @endif

                @else
                    <p class="small text-muted mb-0">Không thể hủy đơn ở trạng thái hiện tại.</p>
                @endif
            </div>
        </div>

        {{-- Trả hàng / Hoàn tiền --}}
        @if($order->status === \App\Models\Order::DELIVERED)
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Trả hàng / Hoàn tiền</h6>
                    <p class="small text-muted">Chỉ áp dụng khi sản phẩm bị lỗi hoặc hư hỏng khi nhận hàng.</p>
                    <form method="POST" action="{{ route('orders.return', $order) }}">
                        @csrf
                        <textarea name="return_reason" rows="3" placeholder="Mô tả lỗi của sản phẩm*"
                                  class="form-control mb-2 @error('return_reason') is-invalid @enderror" required>{{ old('return_reason') }}</textarea>
                        @error('return_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-outline-danger w-100">Gửi yêu cầu trả hàng</button>
                    </form>
                </div>
            </div>
        @elseif($order->status === \App\Models\Order::RETURN_PENDING)
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Trả hàng / Hoàn tiền</h6>
                    <p class="small mb-0">Yêu cầu trả hàng của bạn đang chờ cửa hàng duyệt.</p>
                    @if($order->return_reason)
                        <p class="small text-muted mb-0 mt-2">Lý do: {{ $order->return_reason }}</p>
                    @endif
                </div>
            </div>
        @elseif($order->status === \App\Models\Order::RETURN_IN_PROGRESS)
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Trả hàng / Hoàn tiền</h6>
                    <p class="small mb-0">Yêu cầu đã được duyệt. Vui lòng gửi trả sản phẩm cho cửa hàng.</p>
                </div>
            </div>
        @elseif($order->status === \App\Models\Order::REFUNDED)
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Trả hàng / Hoàn tiền</h6>
                    <p class="small mb-0">Cửa hàng đã nhận hàng trả và hoàn tiền cho bạn.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mt-4">← Về danh sách đơn hàng</a>
@endsection
