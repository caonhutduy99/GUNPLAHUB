@extends('layouts.app')

@section('title', 'Quản trị - GunplaHub')

@section('content')
<h3 class="mb-4">Trang quản trị</h3>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $userCount }}</div>
                <div class="text-muted">Khách hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $productCount }}</div>
                <div class="text-muted">Sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $orderCount }}</div>
                <div class="text-muted">Đơn hàng</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm {{ $pendingCount > 0 ? 'border-danger' : '' }}">
                <div class="card-body">
                    <div class="display-6 {{ $pendingCount > 0 ? 'text-danger' : 'text-body' }}">{{ $pendingCount }}</div>
                    <div class="text-muted">Đơn cần xử lý</div>
                </div>
            </div>
        </a>
    </div>
</div>

<a href="{{ route('admin.orders.index') }}" class="btn btn-dark">Quản lý đơn hàng</a>
<a href="{{ route('admin.categories.index') }}" class="btn btn-dark">Quản lý danh mục</a>
<a href="{{ route('admin.products.index') }}" class="btn btn-dark">Quản lý sản phẩm</a>
<a href="{{ route('admin.brands.index') }}" class="btn btn-dark">Quản lý thương hiệu</a>
@endsection
