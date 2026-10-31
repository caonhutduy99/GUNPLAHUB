@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - GunplaHub')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="letter-spacing: -0.01em;">
            <x-icon name="package-open" class="text-danger me-2" />Đơn hàng của tôi
        </h4>
        <p class="text-muted small mb-0">Theo dõi trạng thái xử lý, vận chuyển và lịch sử mua sắm.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
        <x-icon name="layout-grid" class="me-1" />Tiếp tục mua sắm
    </a>
</div>

<div class="card shadow-sm border mb-4" style="border-radius: var(--gh-radius); overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-body-tertiary">
                <tr class="small text-muted text-uppercase" style="letter-spacing: 0.04em;">
                    <th class="ps-3 py-3">Mã đơn</th>
                    <th class="py-3">Thời gian đặt</th>
                    <th class="text-center py-3">Số lượng mặt hàng</th>
                    <th class="text-end py-3">Tổng thanh toán</th>
                    <th class="py-3">Trạng thái đơn</th>
                    <th style="width: 140px;" class="pe-3 py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="ps-3 py-3 fw-bold">
                            <span class="text-danger">#{{ $order->id }}</span>
                        </td>
                        <td class="small text-muted">
                            <x-icon name="calendar" class="me-1" />{{ $order->order_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary">{{ $order->details_count }} sản phẩm</span>
                        </td>
                        <td class="text-end fw-bold text-danger" style="font-size: 0.95rem;">
                            {{ \App\Support\Money::format($order->total_amount) }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->statusBadge() }}-subtle text-{{ $order->statusBadge() }} border border-{{ $order->statusBadge() }}-subtle px-2 py-1">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="pe-3 py-3 text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1" style="font-size: 0.8rem; font-weight: 600;">
                                Chi tiết <x-icon name="chevron-right" class="ms-1" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <x-icon name="package-open" size="3rem" class="text-muted opacity-50 mb-3 d-block" />
                            <h6 class="fw-bold mb-1">Bạn chưa có đơn hàng nào</h6>
                            <p class="small text-muted mb-3">Hãy dạo quanh cửa hàng và chọn cho mình mô hình Gunpla yêu thích!</p>
                            <a href="{{ route('products.index') }}" class="btn btn-danger btn-sm rounded-pill px-4">Mua sắm ngay</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection
