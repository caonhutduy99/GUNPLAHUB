@extends('layouts.app')

@section('title', 'Thêm mã giảm giá - Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><x-icon name="plus" class="me-2" />Thêm mã giảm giá mới</h4>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <x-icon name="arrow-left" class="me-1" />Quay lại
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.vouchers.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label">Mã giảm giá (Code) <span class="text-danger">*</span></label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}"
                                   class="form-control text-uppercase font-monospace @error('code') is-invalid @enderror"
                                   placeholder="Ví dụ: GUNPLA20K, SALE50..." required>
                            <div class="form-text">Viết liền không dấu, hệ thống tự động in hoa.</div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="discount_amount" class="form-label">Số tiền giảm (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" id="discount_amount" name="discount_amount" min="1000" step="1000"
                                   value="{{ old('discount_amount') }}"
                                   class="form-control @error('discount_amount') is-invalid @enderror"
                                   placeholder="Ví dụ: 20000" required>
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" id="min_order_value" name="min_order_value" min="0" step="1000"
                                   value="{{ old('min_order_value', 0) }}"
                                   class="form-control @error('min_order_value') is-invalid @enderror"
                                   placeholder="0 nếu không yêu cầu" required>
                            <div class="form-text">Khách hàng cần có tổng tiền hàng đạt mức này mới được áp dụng.</div>
                            @error('min_order_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="usage_limit" class="form-label">Số lượt sử dụng tối đa</label>
                            <input type="number" id="usage_limit" name="usage_limit" min="1" step="1"
                                   value="{{ old('usage_limit') }}"
                                   class="form-control @error('usage_limit') is-invalid @enderror"
                                   placeholder="Bỏ trống = không giới hạn lượt dùng">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="expiry_date" name="expiry_date"
                                   value="{{ old('expiry_date') }}"
                                   class="form-control @error('expiry_date') is-invalid @enderror" required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', true))>
                                <label class="form-check-label fs-6 align-middle" for="is_active">Kích hoạt mã ngay sau khi tạo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-dark">
                            <x-icon name="check" class="me-1" />Lưu mã giảm giá
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary ms-2">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
