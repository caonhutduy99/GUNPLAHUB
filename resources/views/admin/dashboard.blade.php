@extends('layouts.app')

@section('title', 'Quản trị - GunplaHub')

@section('content')
<h3 class="mb-4">Trang quản trị</h3>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="display-6">{{ $userCount }}</div><div class="text-muted">Khách hàng</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="display-6">{{ $productCount }}</div><div class="text-muted">Sản phẩm</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="display-6">{{ $orderCount }}</div><div class="text-muted">Đơn hàng</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm {{ $pendingCount > 0 ? 'border-danger' : '' }}"><div class="card-body">
                <div class="display-6 {{ $pendingCount > 0 ? 'text-danger' : 'text-body' }}">{{ $pendingCount }}</div>
                <div class="text-muted">Đơn cần xử lý</div>
            </div></div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 fw-semibold">{{ \App\Support\Money::format($revenueToday) }}</div>
            <div class="text-muted">Doanh thu hôm nay</div>
        </div></div>
    </div>
    <div class="col-md-6">
        <a href="{{ route('admin.reports.revenue') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm"><div class="card-body">
                <div class="fs-3 fw-semibold text-body">{{ \App\Support\Money::format($revenueMonth) }}</div>
                <div class="text-muted">Doanh thu tháng này</div>
            </div></div>
        </a>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-dark">Quản lý đơn hàng</a>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-dark">Quản lý danh mục</a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-dark">Quản lý sản phẩm</a>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-dark">Quản lý thương hiệu</a>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-dark">Quản lý khách hàng</a>
    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-dark">Báo cáo doanh thu</a>
    <a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline-dark">Báo cáo tồn kho</a>
    <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-dark">Nhật ký hệ thống</a>
</div>
@endsection
