{{-- Thẻ sản phẩm dùng chung cho trang chủ, cửa hàng, sản phẩm liên quan --}}
<div class="card h-100 shadow-sm">
    <a href="{{ route('products.show', $product) }}" class="text-decoration-none position-relative">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}"
                 style="height: 170px; object-fit: contain; background: #fff;">
        @else
            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="height: 170px;">
                <span class="text-muted small">{{ $product->grade }} {{ $product->scale }}</span>
            </div>
        @endif
        @if($product->status === 'Hết hàng')
            <span class="badge text-bg-warning position-absolute top-0 end-0 m-2">Hết hàng</span>
        @endif
    </a>
    <div class="card-body d-flex flex-column">
        <div class="mb-2">
            <span class="badge text-bg-dark">{{ $product->grade }}</span>
            <span class="badge text-bg-secondary">{{ $product->series }}</span>
        </div>
        <h6 class="card-title">
            <a href="{{ route('products.show', $product) }}" class="text-body text-decoration-none">{{ $product->name }}</a>
        </h6>
        <p class="text-danger fw-bold mt-auto mb-2">{{ \App\Support\Money::format($product->price) }}</p>
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-dark btn-sm">Xem chi tiết</a>
    </div>
</div>
