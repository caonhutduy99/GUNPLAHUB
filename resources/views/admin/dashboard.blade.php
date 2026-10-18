@extends('layouts.app')

@section('title', 'Quản trị - GunplaHub')

@section('content')
<h3 class="mb-4">Trang quản trị</h3>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $userCount }}</div>
                <div class="text-muted">Khách hàng</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $productCount }}</div>
                <div class="text-muted">Sản phẩm</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <div class="display-6">{{ $orderCount }}</div>
                <div class="text-muted">Đơn hàng</div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.categories.index') }}" class="btn btn-dark">Quản lý danh mục</a>
<a href="{{ route('admin.products.index') }}" class="btn btn-dark">Quản lý sản phẩm</a>
@endsection
