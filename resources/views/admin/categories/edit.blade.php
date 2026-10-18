@extends('layouts.app')

@section('title', 'Sửa danh mục - GunplaHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4">Sửa danh mục #{{ $category->id }}</h4>

                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark">Lưu</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
