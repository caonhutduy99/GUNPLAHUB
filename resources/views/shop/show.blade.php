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
                @if($product->images->isNotEmpty())
                    <div class="border rounded bg-white p-2 text-center">
                        <img id="productImagePreview" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                             class="img-fluid" style="height: 420px; width: 100%; object-fit: contain;">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($product->images as $image)
                                <button type="button" class="btn p-0 border rounded bg-white product-image-thumb"
                                        data-image-url="{{ $image->url }}" aria-label="Xem ảnh sản phẩm">
                                    <img src="{{ $image->url }}" alt="" style="height: 64px; width: 64px; object-fit: contain;">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @elseif($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="img-fluid rounded border bg-white" style="height: 420px; width: 100%; object-fit: contain;">
                @else
                    <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" style="height: 320px;">
                        <span class="text-muted">Chưa có ảnh</span>
                    </div>
                @endif
            </div>

            <div class="col-md-7">
                <h3 class="mb-2">{{ $product->name }}</h3>
                @auth
                    @if(! auth()->user()->isAdmin())
                        <form method="POST" action="{{ route($isWishlisted ? 'wishlist.destroy' : 'wishlist.store', $product) }}" class="mb-3">
                            @csrf
                            @if($isWishlisted)
                                @method('DELETE')
                            @endif
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle"
                                    title="{{ $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                                    aria-label="{{ $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                                    style="width: 38px; height: 38px;">
                                <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                            </button>
                        </form>
                    @endif
                @endauth
                <div class="mb-3">
                    <span class="badge text-bg-dark">{{ $product->grade ?: $product->category->short_name }}</span>
                    @if($product->scale)
                        <span class="badge text-bg-secondary">{{ $product->scale }}</span>
                    @endif
                    @if($product->series)
                        <span class="badge text-bg-secondary">{{ $product->series }}</span>
                    @endif
                </div>

                <h2 class="text-danger mb-3">{{ \App\Support\Money::format($product->price) }}</h2>

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
                    @if($product->brand)
                        <tr><th class="pe-4">Thương hiệu</th><td>{{ $product->brand->name }}</td></tr>
                    @endif
                    <tr><th class="pe-4">Cấp độ / Loại</th><td>{{ $product->category->short_name }}</td></tr>
                    @if($product->scale)
                        <tr><th class="pe-4">Tỷ lệ</th><td>{{ $product->scale }}</td></tr>
                    @endif
                    @if($product->series)
                        <tr><th class="pe-4">Series</th><td>{{ $product->series }}</td></tr>
                    @endif
                </table>

                @if($product->status === 'Còn hàng' && ! (auth()->check() && auth()->user()->isAdmin()))
                    <form method="POST" action="{{ route('cart.add') }}" class="d-flex align-items-center gap-2 mt-3">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $product->sku }}">
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                               class="form-control" style="width: 90px;">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-cart-plus me-1"></i>Thêm vào giỏ hàng
                        </button>
                    </form>
                    @guest
                        <div class="form-text">Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.</div>
                    @endguest
                @endif
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

@push('scripts')
<script>
    document.querySelectorAll('.product-image-thumb').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('productImagePreview').src = button.dataset.imageUrl;
        });
    });
</script>
@endpush
