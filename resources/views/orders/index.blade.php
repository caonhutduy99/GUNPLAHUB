@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - GunplaHub')

@section('content')
<h3 class="mb-3">Đơn hàng của tôi</h3>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th class="text-center">Số sản phẩm</th>
                    <th class="text-end">Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th style="width: 120px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-semibold">#{{ $order->id }}</td>
                        <td>{{ $order->order_date->format('d/m/Y H:i') }}</td>
                        <td class="text-center">{{ $order->details_count }}</td>
                        <td class="text-end">{{ \App\Support\Money::format($order->total_amount) }}</td>
                        <td><span class="badge text-bg-{{ $order->statusBadge() }}">{{ $order->status }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark">Xem chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Bạn chưa có đơn hàng nào.
                            <a href="{{ route('products.index') }}">Mua sắm ngay</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection
