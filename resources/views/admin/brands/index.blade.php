@extends('layouts.app')

@section('title', 'Hãng - GunplaHub')

@section('content')
<h3 class="mb-3">Quản lý hãng</h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.brands.store') }}" class="row g-2">
            @csrf
            <div class="col-md-5">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Tên hãng (ví dụ: Bandai)"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <input type="text" name="code" value="{{ old('code') }}" placeholder="Mã (BAN)" maxlength="10"
                       class="form-control text-uppercase @error('code') is-invalid @enderror" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <input type="number" name="sort_order" value="{{ old('sort_order') }}" min="0" max="999" placeholder="Thứ tự"
                       class="form-control @error('sort_order') is-invalid @enderror">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Thêm hãng</button>
            </div>
        </form>
        <div class="form-text mt-2">
            Mã hãng là tiền tố của SKU. Ví dụ hãng Bandai có mã <code>BAN</code> thì SKU là <code>BAN-HG-UC-001</code>.
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 70px;">ID</th>
                <th style="width: 90px;" class="text-center">Thứ tự</th>
                <th>Tên hãng</th>
                <th style="width: 110px;">Mã</th>
                <th style="width: 130px;" class="text-center">Số sản phẩm</th>
                <th style="width: 180px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td class="text-center">{{ $brand->sort_order }}</td>
                    <td>{{ $brand->name }}</td>
                    <td><code>{{ $brand->code }}</code></td>
                    <td class="text-center">{{ $brand->products_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="d-inline"
                              onsubmit="return confirm('Xóa hãng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    @disabled($brand->products_count > 0)>Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Chưa có hãng nào.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
