@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla')

@section('content')
<div class="p-5 mb-4 bg-dark text-white rounded-3">
    <h1 class="display-6 fw-bold">GunplaHub</h1>
    <p class="mb-0">Cửa hàng mô hình Gunpla chính hãng.</p>
</div>

<h4 class="mb-3">Sản phẩm mới</h4>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}"
                         style="height: 160px; object-fit: contain; background: #fff;">
                @else
                    <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="height: 160px;">
                        <span class="text-muted small">{{ $product->grade }} {{ $product->scale }}</span>
                    </div>
                @endif
                <div class="card-body">
                    <span class="badge text-bg-secondary mb-2">{{ $product->category->name }}</span>
                    <h6 class="card-title">{{ $product->name }}</h6>
                    <p class="text-danger fw-bold mb-0">{{ number_format($product->price, 0, ',', '.') }} ₫</p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Chưa có sản phẩm nào.</p>
    @endforelse
</div>
@endsection
