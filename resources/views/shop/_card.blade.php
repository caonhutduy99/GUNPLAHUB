{{-- Thẻ sản phẩm: màu thanh nhấn bên trái thể hiện tình trạng kho --}}
@php
    $typeLabel = $product->grade ?: \Illuminate\Support\Str::before($product->category->name, ' – ');
    $isCustomer = auth()->check() && ! auth()->user()->isAdmin();
    $inWishlist = $isCustomer && auth()->user()->wishlistItems()->where('product_sku', $product->sku)->exists();
    $outOfStock = $product->status === 'Hết hàng' || $product->stock_quantity <= 0;
    $lowStock = ! $outOfStock && $product->stock_quantity <= 5;
    $barClass = $outOfStock ? 'bar-muted' : ($lowStock ? 'bar-warning' : 'bar-accent');
@endphp
<div class="card h-100 card-product {{ $barClass }} d-flex flex-column overflow-hidden product-card-link"
     data-product-url="{{ route('products.show', $product) }}">
    {{-- Khung ảnh --}}
    <div class="product-media-wrapper">
        <div class="position-absolute top-0 start-0 m-2 ms-3 z-2 d-flex flex-column gap-1 align-items-start">
            <span class="badge bg-dark">{{ $typeLabel }}</span>
            @if($product->scale)
                <span class="badge bg-light border fw-normal">{{ $product->scale }}</span>
            @endif
        </div>
        <div class="position-absolute top-0 end-0 m-2 z-2">
            @if($outOfStock)
                <span class="badge bg-secondary-subtle text-secondary border fw-semibold">Hết hàng</span>
            @elseif($lowStock)
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold">Còn {{ $product->stock_quantity }}</span>
            @else
                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle fw-semibold">Còn hàng</span>
            @endif
        </div>

        <a href="{{ route('products.show', $product) }}" class="text-decoration-none d-flex align-items-center justify-content-center" style="height: 185px;">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="img-fluid w-100 h-100 object-fit-contain product-img" loading="lazy">
            @else
                <div class="d-flex flex-column align-items-center justify-content-center text-center p-3 text-secondary">
                    <x-icon name="package-open" size="2rem" class="mb-1 opacity-50" />
                    <span class="small">{{ $product->grade ? $product->grade . ' ' . $product->scale : $product->category->short_name }}</span>
                </div>
            @endif
        </a>

        {{-- Thao tác nhanh khi rê chuột vào thẻ --}}
        <div class="product-hover-overlay">
            @unless($outOfStock)
                @if($product->status === 'Còn hàng')
                    <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $product->sku }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn-hover-action" title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                            <x-icon name="shopping-cart" size="1.1rem" />
                        </button>
                    </form>
                @endif
            @endunless
            <button type="button"
                    class="btn-hover-action btn-quick-view"
                    data-bs-toggle="modal"
                    data-bs-target="#quickViewModal"
                    data-sku="{{ $product->sku }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ \App\Support\Money::format($product->price) }}"
                    data-image="{{ $product->image_url ?? '' }}"
                    data-brand="{{ $product->brand?->name ?? 'BANDAI' }}"
                    data-grade="{{ $product->grade ?: $product->category->short_name }}"
                    data-scale="{{ $product->scale ?? '' }}"
                    data-series="{{ $product->series ?? '' }}"
                    data-status="{{ $product->status }}"
                    data-stock="{{ $product->stock_quantity }}"
                    data-detail-url="{{ route('products.show', $product) }}"
                    title="Xem nhanh" aria-label="Xem nhanh">
                <x-icon name="eye" size="1.1rem" />
            </button>
        </div>
    </div>

    {{-- Thông tin --}}
    <div class="card-body p-3 ps-4 d-flex flex-column flex-grow-1">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-1 small text-muted">
            <span class="fw-semibold">{{ $product->brand?->name ?? 'BANDAI' }}</span>
            @if($product->series)
                <span class="text-truncate" style="max-width: 130px;" title="{{ $product->series }}">{{ $product->series }}</span>
            @endif
        </div>
        <h6 class="card-title mb-2" style="font-size: 0.92rem; line-height: 1.35; height: 2.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <a href="{{ route('products.show', $product) }}" class="text-body text-decoration-none fw-semibold">{{ $product->name }}</a>
        </h6>
        <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
            <span class="product-price" style="font-size: 1.05rem;">{{ \App\Support\Money::format($product->price) }}</span>
            @if($isCustomer)
                <form method="POST" action="{{ route($inWishlist ? 'wishlist.destroy' : 'wishlist.store', $product) }}" class="d-inline">
                    @csrf
                    @if($inWishlist)
                        @method('DELETE')
                    @endif
                    <button type="submit" class="btn btn-sm btn-outline-secondary p-0 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                            title="{{ $inWishlist ? 'Bỏ thích' : 'Yêu thích' }}" aria-label="Yêu thích">
                        <x-icon name="heart" size="0.9rem" class="{{ $inWishlist ? 'text-danger' : '' }}" />
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
