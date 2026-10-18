@extends('layouts.app')

@section('title', 'Thêm sản phẩm - GunplaHub')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-4">Thêm sản phẩm</h4>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-dark">Lưu sản phẩm</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
