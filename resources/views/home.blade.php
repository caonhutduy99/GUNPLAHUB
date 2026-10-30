@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla chính hãng')

@push('styles')
<style>
    .mega-wrap { position: relative; }
    .mega-panel {
        display: none; position: absolute; top: calc(100% + 8px); left: 0; z-index: 1040; width: 250px;
        background: var(--gh-card-bg); border: 1px solid var(--gh-border);
        border-radius: var(--gh-radius); box-shadow: var(--gh-shadow-hover);
        overflow: visible;
    }
    .mega-wrap:hover .mega-panel, .mega-wrap:focus-within .mega-panel { display: block; }
    .mega-left { padding: 0.5rem 0; margin: 0; }
    .mega-left > li > a {
        display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1.15rem;
        color: var(--bs-body-color); text-decoration: none; font-size: 0.92rem; font-weight: 500;
        transition: all 0.15s ease;
    }
    .mega-left > li:hover > a { background: var(--gh-primary-subtle); color: var(--gh-primary); }
    .mega-sub {
        display: none; position: absolute; top: 0; left: 250px; width: min(720px, 75vw); min-height: 100%;
        padding: 1.5rem; background: var(--gh-card-bg); border: 1px solid var(--gh-border);
        border-radius: 0 var(--gh-radius) var(--gh-radius) 0; box-shadow: var(--gh-shadow-hover);
        grid-template-columns: repeat(3, 1fr); gap: 1.5rem;
    }
    .mega-left li.has-sub:hover .mega-sub { display: grid; }
    .mega-sub h6 {
        font-weight: 700; margin-bottom: 0.75rem; font-size: 0.85rem;
        text-transform: uppercase; letter-spacing: 0.05em; color: var(--gh-primary);
        border-bottom: 1px solid var(--gh-border); padding-bottom: 0.35rem;
    }
    .mega-sub a {
        display: block; padding: 0.35rem 0; font-size: 0.88rem; color: var(--bs-body-color);
        text-decoration: none; transition: color 0.15s ease, transform 0.15s ease;
    }
    .mega-sub a:hover { color: var(--gh-primary); transform: translateX(3px); }
    
    .hero-banner {
        background: linear-gradient(135deg, #120458 0%, #7a04eb 52%, #ff00a0 100%);
        border-radius: var(--gh-radius-lg);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .hero-grid-pattern {
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.6;
        pointer-events: none;
    }
    .hero-glow {
        position: absolute; right: -50px; top: -50px; width: 350px; height: 350px;
        background: radial-gradient(circle, rgba(254, 117, 254, 0.3) 0%, rgba(254, 117, 254, 0) 70%);
        border-radius: 50%; pointer-events: none;
    }
    
    .grade-chip {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.9rem; border-radius: 8px;
        background-color: var(--gh-card-bg); border: 1px solid var(--gh-border);
        font-size: 0.82rem; font-weight: 600; color: var(--bs-body-color);
        text-decoration: none; transition: all 0.2s ease;
    }
    .grade-chip:hover {
        border-color: var(--gh-primary); color: var(--gh-primary);
        transform: translateY(-2px); box-shadow: var(--gh-shadow);
    }

    @media (max-width: 767.98px) {
        .mega-sub { position: static; width: auto; border: 0; box-shadow: none; grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
{{--  --}}
<div class="hero-banner p-4 p-md-5 mb-4 text-white shadow-lg">
    <div class="hero-grid-pattern"></div>
    <div class="hero-glow"></div>
    <div class="position-relative z-1 row align-items-center g-4">
        <div class="col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 mb-3 small">
                <span class="badge bg-danger text-uppercase px-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Original</span>
                <span class="text-white-50 small">プレミアム ガンプラ · BANDAI NAMCO</span>
            </div>
            <h1 class="display-5 fw-bold mb-3 text-white" style="letter-spacing: -0.02em;">
                Thế giới Gunpla & Mô hình Mecha
            </h1>
            <p class="lead text-white-50 mb-4" style="max-width: 640px; font-size: 1.05rem; line-height: 1.6;">
                Khám phá bộ sưu tập đầy đủ các dòng High Grade (HG), Real Grade (RG), Master Grade (MG), Perfect Grade (PG) cùng dụng cụ và phụ kiện dành riêng cho Gunpla Builders.
            </p>
        </div>

        <div class="col-lg-4 d-none d-lg-block text-end">
            <div class="d-inline-block text-start p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-15 backdrop-blur" style="max-width: 290px;">
                <div class="small text-white-50 text-uppercase fw-bold mb-2" style="letter-spacing: 0.08em; font-size: 0.7rem;">Cấp độ phổ biến</div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('products.index', ['grade' => 'RG']) }}" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10">
                        <span><strong class="text-danger">HG</strong> 1/144 Hight Grade</span>
                        <i class="bi bi-chevron-right text-white-50 small"></i>
                    </a>
                    <a href="{{ route('products.index', ['grade' => 'MG']) }}" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10">
                        <span><strong class="text-danger">RG</strong> 1/144 Real Grade</span>
                        <i class="bi bi-chevron-right text-white-50 small"></i>
                    </a>
                    <a href="{{ route('products.index', ['grade' => 'PG']) }}" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10">
                        <span><strong class="text-danger">MG</strong> 1/100 Master Grade</span>
                        <i class="bi bi-chevron-right text-white-50 small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Thanh thanh điều hướng nhanh --}}

    <div class="d-flex flex-wrap align-items-center gap-2">
        {{-- Nút Danh mục sản phẩm (Mega Menu) --}}
        <div class="mega-wrap">
            <button class="btn btn-dark rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" type="button">
                <i class="bi bi-grid-fill text-danger"></i>
                <span>Danh mục sản phẩm</span>
                <i class="bi bi-chevron-down small text-white-50"></i>
            </button>

            <div class="mega-panel">
                <ul class="mega-left list-unstyled mb-0">
                    <li>
                        <a href="{{ route('home') }}"><i class="bi bi-house-door text-muted"></i> Trang chủ</a>
                    </li>
                    <li class="has-sub">
                        <a href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam text-muted"></i> Cấp độ Gunpla 
                            <i class="bi bi-chevron-right ms-auto small text-muted"></i>
                        </a>
                        <div class="mega-sub">
                            <div>
                                <h6>Mô hình Gundam</h6>
                                @foreach($categories->filter(fn ($c) => $c->isGunpla()) as $category)
                                    <a href="{{ route('products.index', ['category_id' => [$category->id]]) }}">
                                        {{ $category->short_name }}
                                    </a>
                                @endforeach
                            </div>
                            <div>
                                <h6>Dụng cụ & Phụ kiện</h6>
                                @foreach($categories->reject(fn ($c) => $c->isGunpla()) as $category)
                                    <a href="{{ route('products.index', ['category_id' => [$category->id]]) }}">
                                        {{ $category->short_name }}
                                    </a>
                                @endforeach
                            </div>
                            <div>
                                <h6>Thương hiệu</h6>
                                @foreach($brands as $brand)
                                    <a href="{{ route('products.index', ['brand_id' => [$brand->id]]) }}">
                                        {{ $brand->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"><i class="bi bi-tags text-muted"></i> Tất cả mô hình</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"><i class="bi bi-envelope text-muted"></i> Hỗ trợ & Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Thanh chips lọc nhanh các cấp độ (Grades) --}}
        <a href="{{ route('products.index', ['grade' => 'HG']) }}" class="grade-chip">HG 1/144</a>
        <a href="{{ route('products.index', ['grade' => 'RG']) }}" class="grade-chip">RG 1/144</a>
        <a href="{{ route('products.index', ['grade' => 'MG']) }}" class="grade-chip">MG 1/100</a>
        <a href="{{ route('products.index', ['grade' => 'PG']) }}" class="grade-chip">PG 1/60</a>
    </div>

</div>

{{-- Danh sách sản phẩm kèm bộ lọc --}}
<div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--gh-border) !important;">
    <h4 class="mb-0 fw-bold d-flex align-items-center gap-2" style="letter-spacing: -0.01em;">
        <i class="bi bi-stars text-danger"></i> Bộ sưu tập mô hình
    </h4>
    <a href="{{ route('products.index') }}" class="small text-decoration-none text-muted fw-semibold">
        Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

@include('shop._listing', ['action' => route('home')])
@endsection
