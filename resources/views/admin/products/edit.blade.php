@extends('layouts.app')

@section('title', 'Sửa sản phẩm - GunplaHub')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-4">Sửa sản phẩm <code>{{ $product->sku }}</code></h4>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-dark">Lưu thay đổi</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
