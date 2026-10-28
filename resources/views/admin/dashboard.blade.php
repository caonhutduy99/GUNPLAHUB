@extends('layouts.app')

@section('title', 'Bảng điều khiển Quản trị - GunplaHub')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">
            <i class="bi bi-speedometer2 text-danger me-2"></i>Bảng điều khiển Quản trị
        </h3>
        <p class="text-muted small mb-0">Tổng quan tình hình kinh doanh và các tác vụ vận hành cửa hàng.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-body-tertiary text-muted border px-3 py-2 small">
            <i class="bi bi-clock me-1"></i>{{ now()->format('d/m/Y - H:i') }}
        </span>
        <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3" target="_blank">
            <i class="bi bi-box-arrow-up-right me-1"></i>Xem Storefront
        </a>
    </div>
</div>

{{-- Các chỉ số tổng quan (Stat Cards) --}}
<div class="row g-3 mb-4">
    {{-- Khách hàng --}}
    <div class="col-6 col-lg-3">
        <div class="card h-100 shadow-sm border p-3" style="border-radius: var(--gh-radius);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Khách hàng</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 36px; height: 36px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="display-6 fw-bold mb-1" style="font-size: 2rem;">{{ number_format($userCount) }}</div>
            <div class="small text-muted">Tài khoản đăng ký</div>
        </div>
    </div>

    {{-- Sản phẩm --}}
    <div class="col-6 col-lg-3">
        <div class="card h-100 shadow-sm border p-3" style="border-radius: var(--gh-radius);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Sản phẩm</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info-emphasis" style="width: 36px; height: 36px;">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <div class="display-6 fw-bold mb-1" style="font-size: 2rem;">{{ number_format($productCount) }}</div>
            <div class="small text-muted">Mô hình & phụ kiện</div>
        </div>
    </div>

    {{-- Tổng số Đơn hàng --}}
    <div class="col-6 col-lg-3">
        <div class="card h-100 shadow-sm border p-3" style="border-radius: var(--gh-radius);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Tổng đơn hàng</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 36px; height: 36px;">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
            <div class="display-6 fw-bold mb-1" style="font-size: 2rem;">{{ number_format($orderCount) }}</div>
            <div class="small text-muted">Đơn đã ghi nhận</div>
        </div>
    </div>

    {{-- Đơn cần xử lý --}}
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border p-3 {{ $pendingCount > 0 ? 'border-danger bg-danger-subtle bg-opacity-10' : '' }}" style="border-radius: var(--gh-radius);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-uppercase {{ $pendingCount > 0 ? 'text-danger' : 'text-muted' }}" style="letter-spacing: 0.05em; font-size: 0.72rem;">Đơn cần duyệt</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $pendingCount > 0 ? 'bg-danger text-white' : 'bg-body-secondary text-muted' }}" style="width: 36px; height: 36px;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold mb-1 {{ $pendingCount > 0 ? 'text-danger' : 'text-body' }}" style="font-size: 2rem;">
                    {{ number_format($pendingCount) }}
                </div>
                <div class="small {{ $pendingCount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                    {{ $pendingCount > 0 ? 'Cần xử lý ngay →' : 'Đã xử lý hết' }}
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Khối Thống kê Doanh thu --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border p-4" style="border-radius: var(--gh-radius);">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em;">Doanh thu hôm nay</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-calendar-event me-1"></i>Hôm nay</span>
            </div>
            <div class="fs-2 fw-bold text-danger mb-1" style="letter-spacing: -0.02em;">
                {{ \App\Support\Money::format($revenueToday) }}
            </div>
            <div class="text-muted small">Doanh số thu từ các đơn hoàn tất và đang giao trong ngày.</div>
        </div>
    </div>

    <div class="col-md-6">
        <a href="{{ route('admin.reports.revenue') }}" class="text-decoration-none">
            <div class="card shadow-sm border p-4 h-100" style="border-radius: var(--gh-radius);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em;">Doanh thu tháng này</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-graph-up-arrow me-1"></i>Xem báo cáo</span>
                </div>
                <div class="fs-2 fw-bold text-body mb-1" style="letter-spacing: -0.02em;">
                    {{ \App\Support\Money::format($revenueMonth) }}
                </div>
                <div class="text-muted small">Nhấn để xem phân tích biểu đồ doanh số và xuất file Excel/CSV.</div>
            </div>
        </a>
    </div>
</div>

{{-- Các lối tắt quản trị (Quick Actions) --}}
<div class="card shadow-sm border mb-4" style="border-radius: var(--gh-radius);">
    <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
        <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">
            <i class="bi bi-grid-3x3-gap-fill text-danger me-2"></i>Lối tắt thao tác nhanh
        </h5>
        <span class="text-muted small">Chọn phân hệ bạn muốn quản lý</span>
    </div>

    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-receipt-cutoff fs-3 text-danger"></i>
                    <span class="fw-semibold">Quản lý Đơn hàng</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Xác nhận, giao hàng, đổi trả</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-box-seam fs-3 text-primary"></i>
                    <span class="fw-semibold">Quản lý Sản phẩm</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Thêm mới, sửa giá, kho hàng</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-tags fs-3 text-success"></i>
                    <span class="fw-semibold">Danh mục & Grade</span>
                    <small class="text-muted" style="font-size: 0.75rem;">HG, RG, MG, PG, Phụ kiện</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-award fs-3 text-warning"></i>
                    <span class="fw-semibold">Thương hiệu</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Bandai, Kotobukiya, Daban</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-ticket-perforated fs-3 text-info"></i>
                    <span class="fw-semibold">Mã giảm giá</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Tạo khuyến mãi & voucher</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-people fs-3 text-secondary"></i>
                    <span class="fw-semibold">Khách hàng</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Danh sách & Blacklist</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-bar-chart-line fs-3 text-danger"></i>
                    <span class="fw-semibold">Báo cáo Doanh thu</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Thống kê theo ngày & tháng</small>
                </a>
            </div>

            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-dark w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 text-decoration-none">
                    <i class="bi bi-journal-text fs-3 text-dark"></i>
                    <span class="fw-semibold">Nhật ký Audit Log</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Lịch sử thao tác hệ thống</small>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
