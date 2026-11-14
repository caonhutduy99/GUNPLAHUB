@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla chính hãng')

@section('content')
{{--  --}}
<section class="card bar-accent mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h1 class="gh-hero-title mb-3">Thế giới Gunpla &amp; Mô hình Mecha</h1>
                <p class="gh-hero-text mb-4">
                    Khám phá bộ sưu tập đến từ các thương hiệu lớn, đa dạng dòng mô hình cùng dụng cụ và phụ kiện dành riêng cho Gunpla Builders.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-primary px-4">Xem cửa hàng</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary px-4">Liên hệ tư vấn</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="brand-showcase ms-auto">
                    <div class="gh-foot-title">Thương hiệu phân phối</div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="brand-logo" title="Bandai Spirits"><img src="{{ asset('logos/bandaispirits.svg') }}" alt="Bandai Spirits"></span>
                        <span class="brand-logo" title="Motor Nuclear"><img src="{{ asset('logos/motornuclear.svg') }}" alt="Motor Nuclear"></span>
                        <span class="brand-logo" title="Blokees"><img src="{{ asset('logos/Blokees.svg') }}" alt="Blokees"></span>
                        <span class="brand-logo" title="Daban Model"><img src="{{ asset('logos/dabanmodel.svg') }}" alt="Daban Model"></span>
                        <span class="brand-logo" title="Moshow Toy"><img src="{{ asset('logos/moshowtoy.svg') }}" alt="Moshow Toy"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- Thanh thanh điều hướng nhanh --}}

    <div class="d-flex flex-wrap align-items-center gap-2">
        {{-- Nút Danh mục sản phẩm (Mega Menu) --}}
        <div class="mega-wrap">
            <button class="btn btn-dark px-3 py-2 d-inline-flex align-items-center gap-2" type="button">
                <i class="bi bi-grid-fill"></i>
                <span>Danh mục sản phẩm</span>
                <i class="bi bi-chevron-down small"></i>
            </button>

            <div class="mega-panel">
                <ul class="mega-left list-unstyled mb-0">
                    <li>
                        <a href="{{ route('home') }}"><i class="bi bi-house-door text-muted"></i> Trang chủ</a>
                    </li>
                    <li class="has-sub">
                        <a href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam text-muted"></i> Gundam / Mecha
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

{{-- Danh sách sản phẩm kèm bộ lọc --}}
<div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
    <a href="{{ route('products.index') }}" class="small text-decoration-none text-muted fw-semibold">
        Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

@include('shop._listing', ['action' => route('home')])
@endsection
