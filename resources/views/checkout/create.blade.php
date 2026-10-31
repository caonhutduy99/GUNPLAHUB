@extends('layouts.app')

@section('title', 'Đặt hàng & Thanh toán - GunplaHub')

@push('styles')
<style>
    .payment-option-card {
        border: 2px solid var(--gh-border);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .payment-option-card:hover {
        border-color: rgba(225, 29, 72, 0.4);
        background: var(--gh-body-bg);
    }
    .payment-option-card input[type="radio"]:checked + .payment-option-content {
        color: var(--gh-primary);
    }
    .payment-option-card:has(input[type="radio"]:checked) {
        border-color: var(--gh-primary);
        background-color: var(--gh-primary-subtle);
    }
</style>
@endpush

@section('content')
{{-- Thanh tiến trình các bước mua hàng --}}
<div class="row justify-content-center mb-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center justify-content-between position-relative">
            <div class="text-center position-relative z-1">
                <a href="{{ route('cart.index') }}" class="text-decoration-none">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem;">
                        <x-icon name="check" />
                    </div>
                    <div class="small fw-semibold mt-1 text-muted">Giỏ hàng</div>
                </a>
            </div>
            <div class="flex-grow-1 border-top border-2 border-danger mx-2"></div>
            <div class="text-center position-relative z-1">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem;">2</div>
                <div class="small fw-bold mt-1 text-danger">Đặt hàng & Thanh toán</div>
            </div>
            <div class="flex-grow-1 border-top border-2 mx-2" style="border-color: var(--gh-border) !important;"></div>
            <div class="text-center position-relative z-1 opacity-50">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-secondary text-secondary fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">3</div>
                <div class="small fw-semibold mt-1 text-muted">Hoàn tất</div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('checkout.store') }}" class="needs-constraint-validation" novalidate>
    @csrf

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- Thông tin giao hàng --}}
            <div class="card shadow-sm border mb-4" style="border-radius: var(--gh-radius);">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2" style="border-color: var(--gh-border) !important;">
                    <x-icon name="map-pin" class="text-danger fs-5" />
                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">1. Thông tin người nhận hàng</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="receiver_name" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Họ và tên người nhận</label>
                            <input type="text" id="receiver_name" name="receiver_name"
                                   value="{{ old('receiver_name', $user->full_name) }}"
                                   class="form-control @error('receiver_name') is-invalid @enderror" 
                                   placeholder="Ví dụ: Nguyễn Văn A" required>
                            @error('receiver_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="receiver_phone" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Số điện thoại liên hệ</label>
                            <input type="tel" id="receiver_phone" name="receiver_phone"
                                   value="{{ old('receiver_phone', $user->phone) }}"
                                   class="form-control @error('receiver_phone') is-invalid @enderror" 
                                   placeholder="Ví dụ: 0912345678" required>
                            @error('receiver_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="shipping_address" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Địa chỉ chi tiết nhận hàng</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3"
                                      class="form-control @error('shipping_address') is-invalid @enderror"
                                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required>{{ old('shipping_address', $user->address) }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phương thức thanh toán --}}
            <div class="card shadow-sm border mb-4" style="border-radius: var(--gh-radius);">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2" style="border-color: var(--gh-border) !important;">
                    <x-icon name="credit-card" class="text-danger fs-5" />
                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">2. Phương thức thanh toán</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        {{-- COD --}}
                        <label class="payment-option-card">
                            <input class="form-check-input mt-0 flex-shrink-0" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                            <div class="d-flex align-items-center justify-content-between flex-grow-1 payment-option-content">
                                <div>
                                    <div class="fw-bold"><x-icon name="wallet" class="text-success me-2" />Thanh toán khi nhận hàng (COD)</div>
                                    <div class="small text-muted">Nhận mô hình, kiểm tra hộp nguyên seal rồi mới thanh toán cho shipper.</div>
                                </div>
                                <span class="badge bg-secondary-subtle text-secondary small">Phổ biến</span>
                            </div>
                        </label>

                        {{-- VietQR --}}
                        <label class="payment-option-card">
                            <input class="form-check-input mt-0 flex-shrink-0" type="radio" name="payment_method" id="pay_vietqr" value="VIETQR"
                                   @checked(old('payment_method') === 'VIETQR')>
                            <div class="d-flex align-items-center justify-content-between flex-grow-1 payment-option-content">
                                <div>
                                    <div class="fw-bold"><x-icon name="scan-qr-code" class="text-primary me-2" />VietQR Chuyển khoản (Sandbox)</div>
                                    <div class="small text-muted">Quét mã QR chuyển khoản nhanh qua ứng dụng mọi ngân hàng tại Việt Nam.</div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary small">Nhanh chóng</span>
                            </div>
                        </label>

                        {{-- VNPay --}}
                        <label class="payment-option-card">
                            <input class="form-check-input mt-0 flex-shrink-0" type="radio" name="payment_method" id="pay_vnpay" value="VNPAY"
                                   @checked(old('payment_method') === 'VNPAY')>
                            <div class="d-flex align-items-center justify-content-between flex-grow-1 payment-option-content">
                                <div>
                                    <div class="fw-bold"><x-icon name="shield-check" class="text-info me-2" />Cổng thanh toán VNPay Sandbox</div>
                                    <div class="small text-muted">Mô phỏng thanh toán trực tuyến qua cổng VNPay điện tử.</div>
                                </div>
                                <span class="badge bg-info-subtle text-info small">Online</span>
                            </div>
                        </label>
                    </div>

                    @error('payment_method')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Mã giảm giá Voucher --}}
            <div class="card shadow-sm border mb-4" style="border-radius: var(--gh-radius);">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2" style="border-color: var(--gh-border) !important;">
                    <x-icon name="ticket" class="text-danger fs-5" />
                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">3. Mã ưu đãi / Voucher</h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><x-icon name="tag" class="text-muted" /></span>
                        <input type="text" name="voucher_code" value="{{ $code }}" placeholder="Nhập mã voucher (Ví dụ: GUNPLA10)..."
                               class="form-control text-uppercase @error('voucher_code') is-invalid @enderror">
                        <button type="submit" formaction="{{ route('checkout.voucher.apply') }}" formnovalidate
                                class="btn btn-dark px-3 fw-semibold">Áp dụng</button>
                        @if($voucher)
                            <button type="submit" formaction="{{ route('checkout.voucher.remove') }}" formnovalidate
                                    class="btn btn-outline-danger px-3">Gỡ mã</button>
                        @endif
                    </div>

                    @error('voucher_code')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @if($voucher)
                        <div class="alert alert-success mt-3 py-2 px-3 small d-flex align-items-center mb-0">
                            <x-icon name="circle-check" class="me-2 fs-6" />
                            <div>Đã áp dụng mã ưu đãi <strong>{{ $voucher->code }}</strong>: Giảm ngay <strong>{{ \App\Support\Money::format($discount) }}</strong>.</div>
                        </div>
                    @elseif($voucherError && ! $errors->has('voucher_code'))
                        <div class="alert alert-danger mt-3 py-2 px-3 small d-flex align-items-center mb-0">
                            <x-icon name="circle-x" class="me-2 fs-6" />
                            <div>{{ $voucherError }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cột Tóm tắt đơn hàng --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border" style="border-radius: var(--gh-radius); position: sticky; top: 84px;">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Đơn hàng của bạn ({{ $lines->count() }})</h5>
                    <a href="{{ route('cart.index') }}" class="small text-danger text-decoration-none fw-semibold">Sửa giỏ hàng</a>
                </div>

                <div class="card-body p-4">
                    {{-- Danh sách rút gọn các sản phẩm --}}
                    <div class="d-flex flex-column gap-3 mb-4" style="max-height: 280px; overflow-y: auto;">
                        @foreach($lines as $line)
                            <div class="d-flex align-items-center justify-content-between gap-2 pb-2 border-bottom" style="border-color: var(--gh-border) !important;">
                                <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                                    @if($line->product->image_url)
                                        <img src="{{ $line->product->image_url }}" alt="" 
                                             style="width: 44px; height: 44px; object-fit: contain; border-radius: 6px;" 
                                             class="border bg-white p-1 flex-shrink-0">
                                    @endif
                                    <div class="overflow-hidden">
                                        <div class="text-truncate fw-semibold small" style="font-size: 0.85rem;" title="{{ $line->product->name }}">
                                            {{ $line->product->name }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            SL: <strong>{{ $line->quantity }}</strong> × {{ \App\Support\Money::format($line->product->price) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="fw-bold text-nowrap small" style="font-size: 0.9rem;">
                                    {{ \App\Support\Money::format($line->product->price * $line->quantity) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Chi tiết tiền --}}
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tiền hàng (tạm tính)</span>
                        <span class="text-body fw-semibold">{{ \App\Support\Money::format($subtotal) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Phí giao hàng</span>
                        @if($shippingFee > 0)
                            <span class="text-body fw-semibold">{{ \App\Support\Money::format($shippingFee) }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success">Miễn phí</span>
                        @endif
                    </div>

                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 small text-success">
                            <span>Voucher giảm giá</span>
                            <span class="fw-bold">-{{ \App\Support\Money::format($discount) }}</span>
                        </div>
                    @endif

                    <hr class="my-3" style="border-color: var(--gh-border) !important;">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-6">Tổng thanh toán:</span>
                        <span class="fw-bold fs-3 text-danger" style="letter-spacing: -0.02em;">
                            {{ \App\Support\Money::format($total) }}
                        </span>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="font-size: 1.05rem;">
                        <x-icon name="lock" />
                        <span>Xác nhận & Hoàn tất đặt hàng</span>
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-muted small text-decoration-none">
                            <x-icon name="chevron-left" class="me-1" />Quay lại chỉnh sửa giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
