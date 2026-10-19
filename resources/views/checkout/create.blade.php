@extends('layouts.app')

@section('title', 'Đặt hàng - GunplaHub')

@section('content')
<h3 class="mb-3">Đặt hàng</h3>

<form method="POST" action="{{ route('checkout.store') }}">
    @csrf

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- Thông tin giao hàng --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body p-4">
                    <h5 class="mb-3">Thông tin giao hàng</h5>

                    <div class="mb-3">
                        <label for="receiver_name" class="form-label">Tên người nhận</label>
                        <input type="text" id="receiver_name" name="receiver_name"
                               value="{{ old('receiver_name', $user->full_name) }}"
                               class="form-control @error('receiver_name') is-invalid @enderror" required>
                        @error('receiver_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="receiver_phone" class="form-label">Số điện thoại</label>
                        <input type="text" id="receiver_phone" name="receiver_phone"
                               value="{{ old('receiver_phone', $user->phone) }}"
                               class="form-control @error('receiver_phone') is-invalid @enderror" required>
                        @error('receiver_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="shipping_address" class="form-label">Địa chỉ giao hàng</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3"
                                  class="form-control @error('shipping_address') is-invalid @enderror"
                                  placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Phương thức thanh toán --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body p-4">
                    <h5 class="mb-3">Phương thức thanh toán</h5>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                        <label class="form-check-label" for="pay_cod">Thanh toán khi nhận hàng (COD)</label>
                    </div>
                    <div class="form-check text-muted">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_vnpay" value="VNPAY" disabled>
                        <label class="form-check-label" for="pay_vnpay">Thanh toán online qua VNPay (đang tích hợp)</label>
                    </div>
                    @error('payment_method')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Mã giảm giá --}}
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Mã giảm giá</h5>

                    <div class="input-group">
                        <input type="text" name="voucher_code" value="{{ $code }}" placeholder="Nhập mã giảm giá"
                               class="form-control text-uppercase @error('voucher_code') is-invalid @enderror">
                        <button type="submit" formaction="{{ route('checkout.voucher.apply') }}" formnovalidate
                                class="btn btn-outline-dark">Áp dụng</button>
                        @if($voucher)
                            <button type="submit" formaction="{{ route('checkout.voucher.remove') }}" formnovalidate
                                    class="btn btn-outline-danger">Bỏ mã</button>
                        @endif
                        @error('voucher_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($voucher)
                        <div class="text-success small mt-2">
                            Đã áp dụng mã <strong>{{ $voucher->code }}</strong>: giảm {{ \App\Support\Money::format($discount) }}.
                        </div>
                    @elseif($voucherError && ! $errors->has('voucher_code'))
                        <div class="text-danger small mt-2">{{ $voucherError }}</div>
                    @endif
                    <div class="form-text">Mỗi đơn hàng chỉ áp dụng tối đa 1 mã giảm giá.</div>
                </div>
            </div>
        </div>

        {{-- Tóm tắt đơn hàng --}}
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Đơn hàng của bạn</h5>

                    @foreach($lines as $line)
                        <div class="d-flex justify-content-between mb-2">
                            <div class="pe-3">
                                {{ $line->product->name }}
                                <span class="text-muted">× {{ $line->quantity }}</span>
                            </div>
                            <div class="text-nowrap">{{ \App\Support\Money::format($line->product->price * $line->quantity) }}</div>
                        </div>
                    @endforeach

                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span>{{ \App\Support\Money::format($subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        @if($shippingFee > 0)
                            <span>{{ \App\Support\Money::format($shippingFee) }}</span>
                        @else
                            <span class="text-success">Miễn phí</span>
                        @endif
                    </div>
                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Giảm giá</span>
                            <span class="text-success">-{{ \App\Support\Money::format($discount) }}</span>
                        </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Tổng cộng</span>
                        <span class="text-danger">{{ \App\Support\Money::format($total) }}</span>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Đặt hàng</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">Quay lại giỏ hàng</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
