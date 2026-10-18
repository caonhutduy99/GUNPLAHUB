@extends('layouts.app')

@section('title', 'Sản phẩm - GunplaHub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Quản lý sản phẩm</h3>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark">+ Thêm sản phẩm</a>
</div>

<form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên hoặc SKU">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">-- Tất cả danh mục --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">-- Tất cả trạng thái --</option>
            @foreach(\App\Models\Product::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-secondary flex-fill">Lọc</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>SKU</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th class="text-end">Giá</th>
                    <th class="text-center">Tồn</th>
                    <th>Trạng thái</th>
                    <th style="width: 190px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="" style="width: 56px; height: 56px; object-fit: contain;">
                            @else
                                <div class="bg-secondary-subtle rounded" style="width: 56px; height: 56px;"></div>
                            @endif
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td class="text-end">{{ number_format($product->price, 0, ',', '.') }} ₫</td>
                        <td class="text-center">{{ $product->stock_quantity }}</td>
                        <td>
                            @php
                                $badge = match($product->status) {
                                    'Còn hàng' => 'success',
                                    'Hết hàng' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge text-bg-{{ $badge }}">{{ $product->status }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                            @if($product->status !== 'Ngừng kinh doanh')
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                      onsubmit="return confirm('Chuyển sản phẩm này sang Ngừng kinh doanh?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Ngừng bán</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Không có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection
