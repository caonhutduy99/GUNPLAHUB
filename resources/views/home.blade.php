@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla')

@section('content')
<div class="p-5 mb-4 bg-dark text-white rounded-3">
    <h1 class="display-6 fw-bold">GunplaHub</h1>
    <p>Cửa hàng mô hình Gunpla chính hãng.</p>
    <a href="{{ route('products.index') }}" class="btn btn-light">Xem tất cả sản phẩm</a>
</div>

<div class="mb-4">
    @foreach($categories as $category)
        <a href="{{ route('products.index', ['category_id' => $category->id]) }}"
           class="btn btn-outline-dark btn-sm mb-1">{{ $category->name }}</a>
    @endforeach
</div>

<h4 class="mb-3">Sản phẩm mới</h4>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-6 col-md-3">
            @include('shop._card', ['product' => $product])
        </div>
    @empty
        <p class="text-muted">Chưa có sản phẩm nào.</p>
    @endforelse
</div>
<div class="text-center mt-4">
    <a href="{{ route('products.index') }}" class="btn btn-dark">Xem thêm sản phẩm</a>
</div>
@endsection
