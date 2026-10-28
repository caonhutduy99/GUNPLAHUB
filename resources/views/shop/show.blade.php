@extends('layouts.app')

@section('title', $product->name . ' - GunplaHub')

@push('styles')
<style>
    .product-thumb-btn {
        width: 68px; height: 68px; padding: 4px; border-radius: 8px;
        border: 2px solid var(--gh-border); background: #ffffff;
        cursor: pointer; transition: all 0.2s ease;
    }
    .product-thumb-btn.active, .product-thumb-btn:hover {
        border-color: var(--gh-primary);
        box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.2);
    }
    .stock-pulse {
        display: inline-block; width: 8px; height: 8px; border-radius: 50%;
        background-color: #10b981; margin-right: 6px;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }
    .stock-pulse.warning {
        background-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    }
    .stock-pulse.danger {
        background-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
    }
    .spec-table th {
        font-weight: 600; color: var(--gh-muted); font-size: 0.88rem; width: 140px;
    }
    .spec-table td {
        font-size: 0.92rem;
    }
</style>
@endpush

@section('content')
{{-- Breadcrumb Clean & Sắc nét --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Cửa hàng</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}" class="text-decoration-none text-muted">
                {{ $product->category->short_name }}
            </a>
        </li>
        <li class="breadcrumb-item active text-truncate fw-semibold" aria-current="page" style="max-width: 320px;">
            {{ $product->name }}
        </li>
    </ol>
</nav>

{{-- Chi tiết sản phẩm kiểu Clean & Premium --}}
<div class="card shadow-sm border mb-5" style="border-radius: var(--gh-radius);">
    <div class="card-body p-4 p-md-5">
        <div class="row g-5">
            {{-- Cột hình ảnh sản phẩm --}}
            <div class="col-lg-5">
                <div class="border rounded-4 bg-white p-3 text-center position-relative shadow-sm" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    @if($product->images->isNotEmpty())
                        <img id="productImagePreview" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                             class="img-fluid object-fit-contain" style="max-height: 420px; width: 100%;">
                    @elseif($product->image_url)
                        <img id="productImagePreview" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="img-fluid object-fit-contain" style="max-height: 420px; width: 100%;">
                    @else
                        <div class="text-muted d-flex flex-column align-items-center justify-content-center py-5">
                            <i class="bi bi-box-seam fs-1 text-secondary opacity-50 mb-2"></i>
                            <span>Chưa có hình ảnh mô hình</span>
                        </div>
                    @endif
                </div>

                {{-- Bộ sưu tập ảnh nhỏ (Thumbnails) --}}
                @if($product->images->count() > 1)
                    <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center">
                        @foreach($product->images as $index => $image)
                            <button type="button" class="product-thumb-btn {{ $index === 0 ? 'active' : '' }}"
                                    data-image-url="{{ $image->url }}" aria-label="Xem ảnh {{ $index + 1 }}">
                                <img src="{{ $image->url }}" alt="" class="w-100 h-100 object-fit-contain">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Cột thông tin chi tiết & Đặt mua --}}
            <div class="col-lg-7">
                {{-- Badges phân loại --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-dark text-white fw-bold px-3 py-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        {{ $product->grade ?: $product->category->short_name }}
                    </span>
                    @if($product->scale)
                        <span class="badge bg-secondary-subtle text-secondary border fw-semibold px-2 py-1" style="font-size: 0.75rem;">
                            {{ $product->scale }}
                        </span>
                    @endif
                    @if($product->brand)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2 py-1" style="font-size: 0.75rem;">
                            {{ $product->brand->name }}
                        </span>
                    @endif
                    @if($product->series)
                        <span class="badge bg-body-tertiary text-muted border fw-normal px-2 py-1" style="font-size: 0.75rem;">
                            {{ $product->series }}
                        </span>
                    @endif
                </div>

                {{-- Tên sản phẩm --}}
                <h2 class="fw-bold mb-3" style="font-size: 1.6rem; letter-spacing: -0.01em; line-height: 1.35;">
                    {{ $product->name }}
                </h2>

                {{-- Khối Giá & Tồn kho --}}
                <div class="p-3 rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3" 
                     style="background: var(--gh-body-bg); border: 1px solid var(--gh-border);">
                    <div>
                        <div class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.75rem;">Giá bán chính hãng</div>
                        <div class="text-danger fw-bold" style="font-size: 1.85rem; letter-spacing: -0.02em; line-height: 1;">
                            {{ \App\Support\Money::format($product->price) }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.75rem;">Trạng thái kho</div>
                        <div>
                            @if($product->status === 'Còn hàng' && $product->stock_quantity > 0)
                                @if($product->stock_quantity <= 5)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1">
                                        <span class="stock-pulse warning"></span>Còn hàng ({{ $product->stock_quantity }})
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1">
                                        <span class="stock-pulse"></span>Còn hàng
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">
                                    <span class="stock-pulse danger"></span>Hết hàng
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Thao tác Mua hàng / Giỏ hàng: Đưa lên ngay dưới khối giá --}}
                @if($product->status === 'Còn hàng' && $product->stock_quantity > 0 && ! (auth()->check() && auth()->user()->isAdmin()))
                    <form method="POST" action="{{ route('cart.add') }}" class="mb-4">
                        @csrf
                        <input type="hidden" name="sku" value="{{ $product->sku }}">
                        
                        {{-- Hàng 1: Số lượng (SL) + Nút "Thêm vào giỏ hàng" + Nút Yêu thích --}}
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="input-group" style="width: 120px;">
                                <span class="input-group-text bg-body-tertiary border-end-0 small text-muted fw-bold">SL</span>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                       class="form-control text-center fw-bold" required>
                            </div>

                            <button type="submit" name="buy_now" value="0" 
                                    class="btn btn-outline-danger btn-lg px-4 py-2 rounded-pill fw-bold flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2" 
                                    style="font-size: 0.95rem;">
                                <i class="bi bi-cart-plus-fill"></i>
                                <span>Thêm vào giỏ hàng</span>
                            </button>

                            @auth
                                <button type="submit" formaction="{{ route($isWishlisted ? 'wishlist.destroy' : 'wishlist.store', $product) }}"
                                        formmethod="POST"
                                        class="btn btn-outline-secondary btn-lg rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0"
                                        title="{{ $isWishlisted ? 'Bỏ thích' : 'Yêu thích' }}"
                                        style="width: 48px; height: 48px;">
                                    @if($isWishlisted)
                                        @method('DELETE')
                                    @endif
                                    <i class="bi {{ $isWishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' }}" style="font-size: 1.25rem;"></i>
                                </button>
                            @endauth
                        </div>

                        {{-- Hàng 2: Nút ở dưới là "Tiến hành mua hàng luôn" --}}
                        <button type="submit" name="buy_now" value="1" 
                                class="btn btn-danger btn-lg w-100 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" 
                                style="font-size: 1.05rem;">
                            <i class="bi bi-bag-check-fill"></i>
                            <span>Tiến hành mua hàng luôn</span>
                        </button>

                        @guest
                            <div class="form-text small text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i>Bạn cần <a href="{{ route('login') }}" class="text-danger fw-semibold">đăng nhập</a> để mua hàng hoặc lưu vào giỏ.
                            </div>
                        @endguest
                    </form>
                @endif

                {{-- Thông số sản phẩm --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing: 0.06em;">Thông số kỹ thuật</h6>
                    <table class="table table-sm spec-table mb-0">
                        <tbody>
                            <tr>
                                <th>Mã sản phẩm</th>
                                <td><code class="fw-bold text-danger">{{ $product->sku }}</code></td>
                            </tr>
                            @if($product->brand)
                                <tr>
                                    <th>Thương hiệu</th>
                                    <td>{{ $product->brand->name }} (Chính hãng)</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Cấp độ mô hình</th>
                                <td>{{ $product->category->name }}</td>
                            </tr>
                            @if($product->scale)
                                <tr>
                                    <th>Tỷ lệ</th>
                                    <td>{{ $product->scale }}</td>
                                </tr>
                            @endif
                            @if($product->series)
                                <tr>
                                    <th>Dòng phim / Series</th>
                                    <td>{{ $product->series }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Cam kết của GunplaHub dành cho Builder --}}
                <div class="row g-2 pt-3 border-top" style="border-color: var(--gh-border) !important;">
                    <div class="col-sm-4">
                        <div class="p-2 border rounded-3 text-center bg-body-tertiary">
                            <i class="bi bi-patch-check-fill text-danger fs-5 d-block mb-1"></i>
                            <div class="small fw-semibold">100% Chính hãng</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Nhập trực tiếp Bandai</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-2 border rounded-3 text-center bg-body-tertiary">
                            <i class="bi bi-box-seam-fill text-primary fs-5 d-block mb-1"></i>
                            <div class="small fw-semibold">Đóng gói chuẩn</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Bọc 3 lớp chống móp</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-2 border rounded-3 text-center bg-body-tertiary">
                            <i class="bi bi-arrow-repeat text-success fs-5 d-block mb-1"></i>
                            <div class="small fw-semibold">Đổi trả 7 ngày</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Nếu lỗi từ runner/hộp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sản phẩm liên quan --}}
@if($related->isNotEmpty())
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-collection text-danger"></i> Sản phẩm cùng danh mục
            </h5>
            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}" class="small text-decoration-none text-muted fw-semibold">
                Xem thêm <i class="bi bi-chevron-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($related as $item)
                <div class="col-6 col-md-3">
                    @include('shop._card', ['product' => $item])
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.product-thumb-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.product-thumb-btn').forEach(function(b) { b.classList.remove('active'); });
            button.classList.add('active');
            var img = document.getElementById('productImagePreview');
            if (img) {
                img.src = button.dataset.imageUrl;
            }
        });
    });
</script>
@endpush
