{{-- Thẻ sản phẩm Clean & Premium Japanese Storefront --}}
@php
    $typeLabel = $product->grade ?: \Illuminate\Support\Str::before($product->category->name, ' – ');
    $isCustomer = auth()->check() && ! auth()->user()->isAdmin();
    $inWishlist = $isCustomer && auth()->user()->wishlistItems()->where('product_sku', $product->sku)->exists();
@endphp
<div class="card h-100 card-product border position-relative d-flex flex-column overflow-hidden" 
     style="border-radius: 14px; transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;">
    
    {{-- Khung hiển thị ảnh với hiệu ứng zoom và popup thao tác nhanh --}}
    <div class="product-media-wrapper position-relative d-block overflow-hidden" 
         style="background: #ffffff; padding: 1.25rem 1rem 0.5rem;">
        
        {{-- Huy hiệu góc trên bên trái: Grade / Cấp độ --}}
        <div class="position-absolute top-0 start-0 m-2 z-2 d-flex flex-column gap-1">
            <span class="badge bg-dark text-white fw-bold shadow-sm" style="font-size: 0.68rem; letter-spacing: 0.05em; border-radius: 6px;">
                {{ $typeLabel }}
            </span>
            @if($product->scale)
                <span class="badge bg-light text-secondary border fw-normal shadow-sm" style="font-size: 0.65rem; border-radius: 6px;">
                    {{ $product->scale }}
                </span>
            @endif
        </div>

        {{-- Huy hiệu góc trên bên phải: Tình trạng tồn kho (chỉ hiện số lượng khi <= 5) --}}
        <div class="position-absolute top-0 end-0 m-2 z-2">
            @if($product->status === 'Hết hàng' || $product->stock_quantity <= 0)
                <span class="badge bg-secondary-subtle text-secondary border fw-semibold" style="font-size: 0.68rem;">
                    Hết hàng
                </span>
            @elseif($product->stock_quantity <= 5)
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold" style="font-size: 0.68rem;">
                    Còn {{ $product->stock_quantity }}
                </span>
            @else
                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle fw-semibold" style="font-size: 0.68rem;">
                    Còn hàng
                </span>
            @endif
        </div>

        {{-- Ảnh sản phẩm --}}
        <a href="{{ route('products.show', $product) }}" class="text-decoration-none d-flex align-items-center justify-content-center" style="height: 185px;">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                     class="img-fluid w-100 h-100 object-fit-contain product-img"
                     loading="lazy"
                     style="transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);">
            @else
                <div class="text-muted d-flex flex-column align-items-center justify-content-center text-center p-3">
                    <i class="bi bi-box-seam fs-2 mb-1 text-secondary opacity-50"></i>
                    <span class="small text-muted">{{ $product->grade ? $product->grade . ' ' . $product->scale : $product->category->short_name }}</span>
                </div>
            @endif
        </a>

        {{-- Popup nổi khi đưa chuột vào (Hover Actions) --}}
        <div class="product-hover-overlay position-absolute top-50 start-50 translate-middle d-flex align-items-center gap-2 z-3">
            {{-- Ô màu vàng: Thêm vào giỏ hàng --}}
            @if($product->status === 'Còn hàng' && $product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="sku" value="{{ $product->sku }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" 
                            class="btn-hover-action btn-hover-yellow shadow-lg"
                            title="Thêm vào giỏ hàng" aria-label="Thêm vào giỏ hàng">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span>Thêm giỏ</span>
                    </button>
                </form>
            @endif

            {{-- Ô màu hồng: Xem nhanh trang chi tiết mà không cần bấm vào sản phẩm --}}
            <button type="button" 
                    class="btn-hover-action btn-hover-pink shadow-lg btn-quick-view"
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
                    title="Xem nhanh chi tiết" aria-label="Xem nhanh chi tiết">
                <i class="bi bi-eye-fill"></i>
                <span>Xem nhanh</span>
            </button>
        </div>
    </div>

    {{-- Phần thân thẻ sản phẩm --}}
    <div class="card-body p-3 d-flex flex-column flex-grow-1">
        {{-- Thương hiệu & Series --}}
        <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                {{ $product->brand?->name ?? 'BANDAI' }}
            </span>
            @if($product->series)
                <span class="text-muted small text-truncate" style="max-width: 130px; font-size: 0.72rem;" title="{{ $product->series }}">
                    {{ $product->series }}
                </span>
            @endif
        </div>

        {{-- Tên sản phẩm --}}
        <h6 class="card-title mb-2" style="font-size: 0.92rem; line-height: 1.35; height: 2.7rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
            <a href="{{ route('products.show', $product) }}" class="text-body text-decoration-none fw-semibold">
                {{ $product->name }}
            </a>
        </h6>

        {{-- Giá và nút bấm hành động --}}
        <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
            <div>
                <span class="text-danger fw-bold" style="font-size: 1.05rem; letter-spacing: -0.01em;">
                    {{ \App\Support\Money::format($product->price) }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-1">
                @if($isCustomer)
                    <form method="POST" action="{{ route($inWishlist ? 'wishlist.destroy' : 'wishlist.store', $product) }}" class="d-inline">
                        @csrf
                        @if($inWishlist)
                            @method('DELETE')
                        @endif
                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;"
                                title="{{ $inWishlist ? 'Bỏ thích' : 'Yêu thích' }}"
                                aria-label="Yêu thích">
                            <i class="bi {{ $inWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }}" style="font-size: 0.85rem;"></i>
                        </button>
                    </form>
                @endif

                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1" style="font-size: 0.8rem; font-weight: 600;">
                    Xem
                </a>
            </div>
        </div>
    </div>
</div>
