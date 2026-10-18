@extends('layouts.app')

@section('title', 'Cửa hàng - GunplaHub')

@section('content')
<h3 class="mb-3">Cửa hàng</h3>

<div class="row">
    {{-- Bộ lọc --}}
    <div class="col-lg-3 mb-4">
        <form method="GET" action="{{ route('products.index') }}" class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên hoặc SKU">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Cấp độ (Grade)</label>
                    <select name="grade" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\Product::GRADES as $grade)
                            <option value="{{ $grade }}" @selected(request('grade') === $grade)>{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tỷ lệ</label>
                    <select name="scale" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\Product::SCALES as $scale)
                            <option value="{{ $scale }}" @selected(request('scale') === $scale)>{{ $scale }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Series</label>
                    <select name="series" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($seriesList as $series)
                            <option value="{{ $series }}" @selected(request('series') === $series)>{{ $series }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Khoảng giá (₫)</label>
                    <div class="d-flex gap-1">
                        <input type="number" name="price_min" min="0" value="{{ request('price_min') }}" class="form-control" placeholder="Từ">
                        <input type="number" name="price_max" min="0" value="{{ request('price_max') }}" class="form-control" placeholder="Đến">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="in_stock" @checked(request()->boolean('in_stock'))>
                    <label class="form-check-label" for="in_stock">Chỉ hiện sản phẩm còn hàng</label>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">

                <button type="submit" class="btn btn-dark w-100 mb-2">Lọc</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    {{-- Danh sách --}}
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">{{ $products->total() }} sản phẩm</span>

            <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-2">
                @foreach(request()->except(['sort', 'page']) as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="sort" class="small text-muted mb-0">Sắp xếp:</label>
                <select id="sort" name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($sorts as $key => $label)
                        <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-6 col-md-4">
                    @include('shop._card', ['product' => $product])
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">Không tìm thấy sản phẩm phù hợp. Hãy thử bỏ bớt bộ lọc.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
