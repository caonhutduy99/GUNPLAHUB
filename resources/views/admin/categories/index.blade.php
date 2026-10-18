@extends('layouts.app')

@section('title', 'Danh mục - GunplaHub')

@section('content')
<h3 class="mb-3">Quản lý danh mục</h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2">
            @csrf
            <div class="col-md-8">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Tên danh mục mới (ví dụ: Gunpla SD)"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100">Thêm danh mục</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Tên danh mục</th>
                <th style="width: 140px;" class="text-center">Số sản phẩm</th>
                <th style="width: 180px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td class="text-center">{{ $category->products_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                              onsubmit="return confirm('Xóa danh mục này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    @disabled($category->products_count > 0)>Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có danh mục.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
