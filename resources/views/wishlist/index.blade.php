@extends('layouts.app')

@section('title', 'Danh sách yêu thích - GunplaHub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Danh sách yêu thích</h3>
    <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Tiếp tục mua sắm</a>
</div>

@if($items->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-heart fs-1 text-muted"></i>
            <p class="text-muted mt-3 mb-0">Bạn chưa lưu sản phẩm nào.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($items as $item)
            @if($item->product)
                <div class="col-6 col-md-3">
                    @include('shop._card', ['product' => $item->product])
                    <form method="POST" action="{{ route('wishlist.destroy', $item->product) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-heartbreak me-1"></i>Xóa khỏi yêu thích
                        </button>
                    </form>
                </div>
            @endif
        @endforeach
    </div>
@endif
@endsection
