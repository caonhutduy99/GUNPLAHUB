@extends('layouts.app')

@section('title', 'Sửa mã giảm giá - Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><x-icon name="settings" class="me-2" />Sửa mã giảm giá: <code class="text-primary">{{ $voucher->code }}</code></h4>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <x-icon name="arrow-left" class="me-1" />Quay lại
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label">Mã giảm giá (Code)</label>
                            <input type="text" id="code" value="{{ $voucher->code }}"
                                   class="form-control text-uppercase font-monospace bg-light" readonly>
                            <div class="form-text">Mã giảm giá là định danh, không thể thay đổi.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="discount_amount" class="form-label">Số tiền giảm (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" id="discount_amount" name="discount_amount" min="1000" step="1000"
                                   value="{{ old('discount_amount', (int)$voucher->discount_amount) }}"
                                   class="form-control @error('discount_amount') is-invalid @enderror" required>
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" id="min_order_value" name="min_order_value" min="0" step="1000"
                                   value="{{ old('min_order_value', (int)$voucher->min_order_value) }}"
                                   class="form-control @error('min_order_value') is-invalid @enderror" required>
                            @error('min_order_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="usage_limit" class="form-label">Số lượt sử dụng tối đa</label>
                            <input type="number" id="usage_limit" name="usage_limit" min="{{ $voucher->used_count }}" step="1"
                                   value="{{ old('usage_limit', $voucher->usage_limit) }}"
                                   class="form-control @error('usage_limit') is-invalid @enderror"
                                   placeholder="Bỏ trống = không giới hạn">
                            <div class="form-text">Đã sử dụng: <strong>{{ $voucher->used_count }}</strong> lượt.</div>
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="expiry_date" name="expiry_date"
                                   value="{{ old('expiry_date', $voucher->expiry_date->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('expiry_date') is-invalid @enderror" required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', $voucher->is_active ?? true))>
                                <label class="form-check-label fs-6 align-middle" for="is_active">Kích hoạt mã giảm giá</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-dark">
                            <x-icon name="check" class="me-1" />Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary ms-2">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
