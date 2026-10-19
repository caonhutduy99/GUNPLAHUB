{{-- Bộ lọc + sắp xếp + danh sách sản phẩm. Dùng cho Trang chủ và Cửa hàng. Cần biến $action. --}}
<div class="row">
    {{-- Bộ lọc --}}
    <div class="col-lg-3 mb-4">
        <form method="GET" action="{{ $action }}" class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên hoặc SKU">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Danh mục</label>

                    <div class="small text-muted mb-1">Mô hình Gunpla</div>
                    @foreach($gunplaCategories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                   id="cat{{ $category->id }}" @checked(in_array($category->id, (array) request('category_id', [])))>
                            <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                    @endforeach

                    @if($accessoryCategories->isNotEmpty())
                        <div class="small text-muted mt-2 mb-1">Phụ kiện</div>
                        @foreach($accessoryCategories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                       id="cat{{ $category->id }}" @checked(in_array($category->id, (array) request('category_id', [])))>
                                <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    @endif
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
                    <label class="form-label fw-semibold">Mức giá</label>
                    @foreach($priceRanges as $key => $range)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="price_range[]" value="{{ $key }}"
                                   id="price_{{ $key }}" @checked(in_array($key, (array) request('price_range', [])))>
                            <label class="form-check-label" for="price_{{ $key }}">{{ $range['label'] }}</label>
                        </div>
                    @endforeach

                    <div class="small text-muted mt-2 mb-1">Hoặc tự nhập khoảng giá (đ)</div>
                    <div class="d-flex gap-1">
                        <input type="number" name="price_min" min="0" value="{{ request('price_min') }}" class="form-control form-control-sm" placeholder="Từ">
                        <input type="number" name="price_max" min="0" value="{{ request('price_max') }}" class="form-control form-control-sm" placeholder="Đến">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="in_stock" @checked(request()->boolean('in_stock'))>
                    <label class="form-check-label" for="in_stock">Chỉ hiện sản phẩm còn hàng</label>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">

                <button type="submit" class="btn btn-dark w-100 mb-2">Lọc</button>
                <a href="{{ $action }}" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    {{-- Sắp xếp + danh sách --}}
    <div class="col-lg-9">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="small text-muted me-1">Sắp xếp:</span>
            @foreach($sorts as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}"
                   class="btn btn-sm {{ $sort === $key ? 'btn-dark' : 'btn-outline-dark' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="text-muted small mb-3">{{ $products->total() }} sản phẩm</div>

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
