@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla')

@push('styles')
<style>
    .mega-wrap { position: relative; }
    .mega-panel {
        display: none; position: absolute; top: 100%; left: 0; z-index: 1040; width: 240px;
        background: var(--bs-body-bg); border: 1px solid var(--bs-border-color);
        border-radius: .5rem; box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .25);
    }
    .mega-wrap:hover .mega-panel, .mega-wrap:focus-within .mega-panel { display: block; }
    .mega-left { padding: .4rem 0; margin: 0; }
    .mega-left > li > a {
        display: flex; align-items: center; gap: .6rem; padding: .55rem 1rem;
        color: var(--bs-body-color); text-decoration: none;
    }
    .mega-left > li:hover > a { background: var(--bs-tertiary-bg); color: #e8501e; }
    .mega-sub {
        display: none; position: absolute; top: 0; left: 240px; width: min(780px, 75vw); min-height: 100%;
        padding: 1rem 1.25rem; background: var(--bs-body-bg); border: 1px solid var(--bs-border-color);
        border-radius: 0 .5rem .5rem 0; box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .25);
        grid-template-columns: repeat(3, 1fr); gap: 1.25rem;
    }
    .mega-left li.has-sub:hover .mega-sub { display: grid; }
    .mega-sub h6 { font-weight: 700; margin-bottom: .5rem; }
    .mega-sub a { display: block; padding: .15rem 0; font-size: .9rem; color: var(--bs-body-color); text-decoration: none; }
    .mega-sub a:hover { color: #e8501e; }
    .commit { display: inline-flex; align-items: center; gap: .4rem; cursor: default; user-select: none; }
    .commit i { color: #e8501e; }
    @media (max-width: 767.98px) {
        .mega-sub { position: static; width: auto; border: 0; box-shadow: none; grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="p-5 mb-3 bg-dark text-white rounded-3">
    <h1 class="display-6 fw-bold">{{ config('shop.name') }}</h1>
    <p class="mb-0">Cửa hàng mô hình Gunpla chính hãng.</p>
</div>

<div class="d-flex flex-wrap align-items-center gap-3 mb-4">
    {{-- Nút Danh sách sản phẩm: rê chuột vào để mở bảng --}}
    <div class="mega-wrap">
        <a href="{{ route('products.index') }}" class="btn btn-dark">
            <i class="bi bi-list me-2"></i>Danh sách sản phẩm
        </a>

        <div class="mega-panel">
            <ul class="mega-left list-unstyled">
                <li><a href="{{ route('home') }}"><i class="bi bi-house-door"></i> Trang chủ</a></li>
                <li class="has-sub">
                    <a href="{{ route('products.index') }}">
                        <i class="bi bi-bag"></i> Sản phẩm <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                    <div class="mega-sub">
                        <div>
                            <h6>Cấp độ</h6>
                            @foreach($categories->filter(fn ($c) => $c->isGunpla()) as $category)
                                <a href="{{ route('products.index', ['category_id' => [$category->id]]) }}">{{ $category->short_name }}</a>
                            @endforeach
                        </div>
                        <div>
                            <h6>Phụ kiện</h6>
                            @foreach($categories->reject(fn ($c) => $c->isGunpla()) as $category)
                                <a href="{{ route('products.index', ['category_id' => [$category->id]]) }}">{{ $category->short_name }}</a>
                            @endforeach
                        </div>
                        <div>
                            <h6>Thương hiệu</h6>
                            @foreach($brands as $brand)
                                <a href="{{ route('products.index', ['brand_id' => [$brand->id]]) }}">{{ $brand->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </li>
                <li><a href="{{ route('contact') }}"><i class="bi bi-envelope"></i> Liên hệ</a></li>
            </ul>
        </div>
    </div>

    {{-- Các cam kết (không bấm được) --}}
    @foreach(config('shop.commitments') as $commitment)
        <span class="commit">
            <i class="bi {{ $commitment['icon'] }}"></i>{{ $commitment['text'] }}
        </span>
    @endforeach

    <a href="tel:{{ preg_replace('/\D/', '', config('shop.phone')) }}" class="btn btn-danger ms-lg-auto">
        <i class="bi bi-telephone-fill me-2"></i>Hỗ trợ khách hàng: {{ config('shop.phone') }}
    </a>
</div>

<h4 class="mb-3">Sản phẩm</h4>

@include('shop._listing', ['action' => route('home')])
@endsection
