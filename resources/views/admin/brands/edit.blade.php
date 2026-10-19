@extends('layouts.app')

@section('title', 'Sửa thương hiệu - GunplaHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4">Sửa thương hiệu #{{ $brand->id }}</h4>

                <form method="POST" action="{{ route('admin.brands.update', $brand) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thương hiệu</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Mã thương hiệu (tiền tố SKU)</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $brand->code) }}" maxlength="10"
                               class="form-control text-uppercase @error('code') is-invalid @enderror"
                               @if($codeLocked) readonly @endif required>
                        @if($codeLocked)
                            <div class="form-text">Thương hiệu đã có sản phẩm nên không thể đổi mã (để SKU không bị lệch).</div>
                        @endif
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" id="sort_order" name="sort_order" min="0" max="999"
                               value="{{ old('sort_order', $brand->sort_order) }}"
                               class="form-control @error('sort_order') is-invalid @enderror">
                        <div class="form-text">Số nhỏ hiển thị trước trong bộ lọc.</div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark">Lưu</button>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
