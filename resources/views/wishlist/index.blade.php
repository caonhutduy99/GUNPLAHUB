@extends('layouts.app')

@section('title', 'Danh sách yêu thích - GunplaHub')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="letter-spacing: -0.01em;">
            <i class="bi bi-heart-fill text-danger me-2"></i>Danh sách yêu thích của bạn
        </h4>
        <p class="text-muted small mb-0">Các mẫu mô hình bạn đã đánh dấu để theo dõi hoặc mua sau.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Tiếp tục khám phá
    </a>
</div>

@if($items->isEmpty())
    <div class="card shadow-sm border text-center py-5" style="border-radius: var(--gh-radius);">
        <div class="card-body">
            <i class="bi bi-heart text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mb-2">Chưa có sản phẩm nào trong danh sách yêu thích</h5>
            <p class="text-muted small mb-4">Nhấn vào biểu tượng trái tim ở góc thẻ sản phẩm để lưu lại mô hình bạn yêu thích.</p>
            <a href="{{ route('products.index') }}" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold">
                Xem tất cả mô hình
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($items as $item)
            @if($item->product)
                <div class="col-6 col-md-3">
                    @include('shop._card', ['product' => $item->product])
                </div>
            @endif
        @endforeach
    </div>
@endif
@endsection
