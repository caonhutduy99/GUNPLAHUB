@extends('layouts.app')

@section('title', $customer->full_name . ' - GunplaHub')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h3 class="mb-0">{{ $customer->full_name }}</h3>
    @if($customer->is_blacklisted)
        <span class="badge fs-6 text-bg-danger">Blacklist</span>
    @else
        <span class="badge fs-6 text-bg-success">Hoạt động</span>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-4">
        {{-- Hồ sơ --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="mb-3">Hồ sơ khách hàng</h6>
                <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ $customer->email }}</p>
                <p class="mb-1"><i class="bi bi-telephone me-2"></i>{{ $customer->phone ?: '—' }}</p>
                <p class="mb-0 small text-muted">Đăng ký ngày {{ $customer->created_at->format('d/m/Y') }}</p>

                @if($addresses->isNotEmpty())
                    <hr>
                    <div class="small fw-semibold mb-1">Địa chỉ giao hàng gần đây</div>
                    @foreach($addresses as $address)
                        <div class="small mb-2">
                            {{ $address->shipping_address }}
                            <div class="text-muted">{{ $address->receiver_name }} · {{ $address->receiver_phone }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="mb-3">Lịch sử mua hàng</h6>
                <div class="d-flex justify-content-between"><span>Tổng số đơn</span><strong>{{ $stats['orders'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Đã giao</span><strong class="text-success">{{ $stats['delivered'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Đã hủy</span><strong>{{ $stats['cancelled'] }}</strong></div>
                <div class="d-flex justify-content-between">
                    <span>Giao thất bại</span>
                    <strong class="{{ $stats['failed'] > 0 ? 'text-danger' : '' }}">{{ $stats['failed'] }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Đã chi tiêu</span>
                    <strong>{{ \App\Support\Money::format($stats['spent']) }}</strong>
                </div>
            </div>
        </div>

        {{-- Blacklist --}}
        <div class="card shadow-sm {{ $customer->is_blacklisted ? 'border-danger' : '' }}">
            <div class="card-body">
                <h6 class="mb-3">Danh sách đen (Blacklist)</h6>

                @if($customer->is_blacklisted)
                    <div class="alert alert-danger small">
                        <div><strong>Lý do:</strong> {{ $customer->blacklist_reason }}</div>
                        @if($customer->blacklist_note)
                            <div><strong>Ghi chú:</strong> {{ $customer->blacklist_note }}</div>
                        @endif
                        <div class="mt-1 text-muted">
                            {{ $customer->blacklisted_at?->format('d/m/Y H:i') }}
                            @if($customer->blacklistedBy) · bởi {{ $customer->blacklistedBy->full_name }} @endif
                        </div>
                    </div>
                    <p class="small text-muted">Khách vẫn xem được sản phẩm nhưng không thể đặt hàng.</p>

                    <form method="POST" action="{{ route('admin.customers.unblacklist', $customer) }}"
                          onsubmit="return confirm('Gỡ khách hàng này khỏi Blacklist?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100">Gỡ khỏi Blacklist</button>
                    </form>
                @else
                    <p class="small text-muted">Đưa vào Blacklist khi khách bom hàng, nợ xấu hoặc gian lận thanh toán. Khách sẽ bị chặn đặt hàng.</p>

                    <form method="POST" action="{{ route('admin.customers.blacklist', $customer) }}"
                          onsubmit="return confirm('Đưa khách hàng này vào Blacklist?');">
                        @csrf
                        <select name="blacklist_reason" class="form-select mb-2 @error('blacklist_reason') is-invalid @enderror" required>
                            <option value="">-- Chọn lý do --</option>
                            @foreach($reasons as $reason)
                                <option value="{{ $reason }}" @selected(old('blacklist_reason') === $reason)>{{ $reason }}</option>
                            @endforeach
                        </select>
                        @error('blacklist_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <textarea name="blacklist_note" rows="2" placeholder="Ghi chú (không bắt buộc)"
                                  class="form-control mb-2">{{ old('blacklist_note') }}</textarea>
                        <button type="submit" class="btn btn-danger w-100">Đưa vào Blacklist</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Đơn hàng --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Đơn hàng của khách</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th class="text-end">Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-semibold">#{{ $order->id }}</td>
                                <td class="small">{{ $order->order_date->format('d/m/Y H:i') }}</td>
                                <td class="text-end">{{ \App\Support\Money::format($order->total_amount) }}</td>
                                <td><span class="badge text-bg-{{ $order->statusBadge() }}">{{ $order->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Khách chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary mt-4">← Về danh sách khách hàng</a>
@endsection
