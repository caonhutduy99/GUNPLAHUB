@extends('layouts.app')

@section('title', 'GunplaHub - Cửa hàng mô hình Gunpla')

@section('content')
<div class="p-5 mb-3 bg-dark text-white rounded-3">
    <h1 class="display-6 fw-bold">{{ config('shop.name') }}</h1>
    <p class="mb-0">Cửa hàng mô hình Gunpla chính hãng.</p>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-dark">Xem tất cả sản phẩm</a>
    <a href="tel:{{ preg_replace('/\D/', '', config('shop.phone')) }}" class="btn btn-danger">
        <i class="bi bi-telephone-fill me-2"></i>Hỗ trợ khách hàng: {{ config('shop.phone') }}
    </a>
</div>

<h4 class="mb-3">Sản phẩm</h4>

@include('shop._listing', ['action' => route('home')])
@endsection
