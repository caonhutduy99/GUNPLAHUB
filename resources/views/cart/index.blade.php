@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - GunplaHub')

@section('content')
{{-- Thanh tiến trình các bước mua hàng --}}
<div class="row justify-content-center mb-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center justify-content-between position-relative">
            <div class="text-center position-relative z-1">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem;">1</div>
                <div class="small fw-bold mt-1 text-danger">Giỏ hàng</div>
            </div>
            <div class="flex-grow-1 border-top border-2 mx-2" style="border-color: var(--gh-border) !important;"></div>
            <div class="text-center position-relative z-1 opacity-50">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-secondary text-secondary fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">2</div>
                <div class="small fw-semibold mt-1 text-muted">Đặt hàng & Thanh toán</div>
            </div>
            <div class="flex-grow-1 border-top border-2 mx-2" style="border-color: var(--gh-border) !important;"></div>
            <div class="text-center position-relative z-1 opacity-50">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-secondary text-secondary fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">3</div>
                <div class="small fw-semibold mt-1 text-muted">Hoàn tất</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="fw-bold mb-0" style="letter-spacing: -0.01em;">
        <i class="bi bi-cart3 text-danger me-2"></i>Giỏ hàng của bạn
    </h4>
    <span class="text-muted small">({{ $lines->count() }} loại sản phẩm)</span>
</div>

@foreach($warnings as $warning)
    <div class="alert alert-warning mb-3">{{ $warning }}</div>
@endforeach

@if($blacklisted)
    <div class="alert alert-danger mb-3">
        <i class="bi bi-shield-x me-2"></i>Tài khoản của bạn đang nằm trong danh sách hạn chế nên không thể đặt hàng. Vui lòng liên hệ cửa hàng để được hỗ trợ.
    </div>
@endif

@if($lines->isEmpty())
    <div class="card shadow-sm border text-center py-5 my-4" style="border-radius: var(--gh-radius);">
        <div class="card-body">
            <i class="bi bi-cart-x text-muted opacity-50 mb-3" style="font-size: 3.5rem;"></i>
            <h5 class="fw-bold mb-2">Giỏ hàng của bạn đang trống</h5>
            <p class="text-muted small mb-4">Hãy khám phá các mẫu Gunpla Bandai chính hãng mới nhất để bổ sung vào bộ sưu tập!</p>
            <a href="{{ route('products.index') }}" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-grid me-2"></i>Khám phá cửa hàng ngay
            </a>
        </div>
    </div>
@else
    <div class="row g-4">
        {{-- Danh sách các mặt hàng trong giỏ --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border mb-3" style="border-radius: var(--gh-radius); overflow: hidden;">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-body-tertiary">
                            <tr class="small text-muted text-uppercase" style="letter-spacing: 0.04em;">
                                <th class="ps-3 py-3">Sản phẩm</th>
                                <th class="text-end py-3">Đơn giá</th>
                                <th style="width: 150px;" class="text-center py-3">Số lượng</th>
                                <th class="text-end py-3">Thành tiền</th>
                                <th style="width: 44px;" class="pe-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $line)
                                @php($product = $line->product)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="" 
                                                     style="width: 65px; height: 65px; object-fit: contain; border-radius: 8px;" 
                                                     class="border bg-white p-1 flex-shrink-0">
                                            @else
                                                <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 65px; height: 65px;">
                                                    <i class="bi bi-box-seam text-secondary"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $product) }}" class="text-body fw-bold text-decoration-none d-block mb-1" style="font-size: 0.95rem;">
                                                    {{ $product->name }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2 small">
                                                    <code class="text-danger small">Mã: {{ $product->sku }}</code>
                                                    <span class="text-muted">·</span>
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Kho: {{ $product->stock_quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold" style="font-size: 0.95rem;">
                                        {{ \App\Support\Money::format($product->price) }}
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('cart.update', $product->sku) }}" class="d-flex align-items-center gap-1 justify-content-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" max="{{ $product->stock_quantity }}"
                                                   class="form-control form-control-sm text-center fw-bold" style="width: 60px;">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm p-1" title="Cập nhật số lượng">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold text-danger" style="font-size: 1rem;">
                                        {{ \App\Support\Money::format($product->price * $line->quantity) }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <form method="POST" action="{{ route('cart.remove', $product->sku) }}"
                                              onsubmit="return confirm('Xóa mô hình này khỏi giỏ hàng?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Xóa">
                                                <i class="bi bi-trash fs-6"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Tiếp tục chọn mô hình
                </a>
                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Xóa toàn bộ giỏ hàng?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-trash me-1"></i>Xóa sạch giỏ hàng
                    </button>
                </form>
            </div>
        </div>

        {{-- Cột tóm tắt hóa đơn & Thanh toán --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border" style="border-radius: var(--gh-radius); position: sticky; top: 84px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                        <span>Tóm tắt thanh toán</span>
                        <i class="bi bi-receipt text-danger"></i>
                    </h5>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tiền hàng (tạm tính)</span>
                        <span class="fw-semibold text-body">{{ \App\Support\Money::format($subtotal) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Phí vận chuyển dự kiến</span>
                        @if($shippingFee > 0)
                            <span class="fw-semibold text-body">{{ \App\Support\Money::format($shippingFee) }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success fw-bold">Miễn phí giao hàng</span>
                        @endif
                    </div>

                    @if($shippingFee > 0 && isset($freeShippingFrom))
                        <div class="p-2 rounded-2 bg-body-tertiary small text-muted mb-3 border">
                            <i class="bi bi-truck text-danger me-1"></i>Mua thêm <strong>{{ \App\Support\Money::format($freeShippingFrom - $subtotal) }}</strong> để được <strong>FREESHIP</strong> toàn quốc!
                        </div>
                    @endif

                    <hr class="my-3" style="border-color: var(--gh-border) !important;">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-6">Tổng thanh toán:</span>
                        <span class="fw-bold fs-4 text-danger" style="letter-spacing: -0.02em;">
                            {{ \App\Support\Money::format($total) }}
                        </span>
                    </div>

                    @if($blacklisted)
                        <button type="button" class="btn btn-secondary w-100 py-2 rounded-pill fw-semibold" disabled>
                            Tài khoản bị hạn chế đặt hàng
                        </button>
                    @elseif(Route::has('checkout.create'))
                        <a href="{{ route('checkout.create') }}" class="btn btn-danger w-100 py-2 rounded-pill fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size: 1rem;">
                            <span>Tiến hành đặt hàng</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @else
                        <button type="button" class="btn btn-dark w-100 py-2 rounded-pill" disabled>Tiến hành đặt hàng</button>
                    @endif

                    <div class="text-center small text-muted mt-3">
                        <i class="bi bi-shield-lock text-success me-1"></i>Thông tin đơn hàng được bảo mật tuyệt đối
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
