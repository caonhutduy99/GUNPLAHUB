@extends('layouts.app')

@section('title', 'Cửa hàng - GunplaHub')

@section('content')
<h3 class="mb-3">Cửa hàng</h3>

@include('shop._listing', ['action' => route('products.index')])
@endsection
