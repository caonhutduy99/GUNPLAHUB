@extends('layouts.app')

@section('title', $product->name . ' - GunplaHub')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Cửa hàng</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}">{{ $product->category->name }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $product->sku }}</li>
    </ol>
</nav>

<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-5">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="img-fluid rounded border bg-white" style="max-height: 420px; width: 100%; object-fit: contain;">
                @else
                    <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" style="height: 320px;">
                        <span class="text-muted">Chưa có ảnh</span>
                    </div>
                @endif
            </div>

            <div class="col-md-7">
                <h3 class="mb-2">{{ $product->name }}</h3>
                <div class="mb-3">
                    <span class="badge text-bg-dark">{{ $product->grade }}</span>
                    <span class="badge text-bg-secondary">{{ $product->scale }}</span>
                    <span class="badge text-bg-secondary">{{ $product->series }}</span>
                </div>

                <h2 class="text-danger mb-3">{{ number_format($product->price, 0, ',', '.') }} ₫</h2>

                <p class="mb-1">
                    <strong>Tình trạng:</strong>
                    @if($product->status === 'Còn hàng')
                        <span class="text-success">Còn hàng</span>
                        @if($product->stock_quantity <= 5)
                            <span class="text-muted">(chỉ còn {{ $product->stock_quantity }} sản phẩm)</span>
                        @endif
                    @else
                        <span class="text-warning">Hết hàng</span>
                    @endif
                </p>

                <table class="table table-sm w-auto mt-3">
                    <tr><th class="pe-4">Mã SKU</th><td><code>{{ $product->sku }}</code></td></tr>
                    <tr><th class="pe-4">Danh mục</th><td>{{ $product->category->name }}</td></tr>
                    <tr><th class="pe-4">Cấp độ</th><td>{{ $product->grade }}</td></tr>
                    <tr><th class="pe-4">Tỷ lệ</th><td>{{ $product->scale }}</td></tr>
                    <tr><th class="pe-4">Series</th><td>{{ $product->series }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($related->isNotEmpty())
    <h5 class="mb-3">Sản phẩm cùng danh mục</h5>
    <div class="row g-3">
        @foreach($related as $item)
            <div class="col-6 col-md-3">
                @include('shop._card', ['product' => $item])
            </div>
        @endforeach
    </div>
@endif
@endsection
