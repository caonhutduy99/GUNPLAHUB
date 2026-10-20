@extends('layouts.app')

@section('title', 'Đơn hàng - GunplaHub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Quản lý đơn hàng</h3>
    @if($needsAction > 0)
        <a href="{{ route('admin.orders.index', ['status' => \App\Models\Order::PENDING]) }}" class="badge text-bg-danger text-decoration-none fs-6">
            {{ $needsAction }} đơn cần xử lý
        </a>
    @endif
</div>

<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Mã đơn, tên hoặc số điện thoại người nhận">
    </div>
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">-- Tất cả trạng thái --</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 d-flex gap-1">
        <button type="submit" class="btn btn-secondary flex-fill">Lọc</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Khách hàng</th>
                    <th>Người nhận</th>
                    <th class="text-end">Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-semibold">#{{ $order->id }}</td>
                        <td class="small">{{ $order->order_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->user?->full_name ?? '—' }}</td>
                        <td>
                            <div>{{ $order->receiver_name }}</div>
                            <div class="small text-muted">{{ $order->receiver_phone }}</div>
                        </td>
                        <td class="text-end">{{ \App\Support\Money::format($order->total_amount) }}</td>
                        <td><span class="badge text-bg-{{ $order->statusBadge() }}">{{ $order->status }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Không có đơn hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection
