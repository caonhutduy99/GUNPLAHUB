{{-- Bộ lọc + sắp xếp + danh sách sản phẩm. Dùng cho Trang chủ và Cửa hàng. Cần biến $action. --}}
@php
    $selectedBrands = (array) request('brand_id', []);
    $brandVisible = 6;
    $hiddenBrands = $brands->slice($brandVisible);
    $expandBrands = $hiddenBrands->contains(fn ($b) => in_array($b->id, $selectedBrands));
@endphp

<div class="row">
    {{-- Bộ lọc --}}
    <div class="col-lg-3 mb-4">
        <form method="GET" action="{{ $action }}" class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên hoặc SKU">
                </div>

                {{-- Thương hiệu --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Thương hiệu</label>

                    @foreach($brands->take($brandVisible) as $brand)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                   id="brand{{ $brand->id }}" @checked(in_array($brand->id, $selectedBrands))>
                            <label class="form-check-label" for="brand{{ $brand->id }}">{{ $brand->name }}</label>
                        </div>
                    @endforeach

                    @if($hiddenBrands->isNotEmpty())
                        <div class="collapse {{ $expandBrands ? 'show' : '' }}" id="moreBrands">
                            @foreach($hiddenBrands as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                           id="brand{{ $brand->id }}" @checked(in_array($brand->id, $selectedBrands))>
                                    <label class="form-check-label" for="brand{{ $brand->id }}">{{ $brand->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <a href="#moreBrands" id="brandToggle" class="small d-inline-block mt-1" role="button"
                           data-bs-toggle="collapse" aria-controls="moreBrands"
                           aria-expanded="{{ $expandBrands ? 'true' : 'false' }}">{{ $expandBrands ? 'Thu gọn' : 'Xem thêm (' . $hiddenBrands->count() . ')' }}</a>
                        <script>
                            (function () {
                                var panel = document.getElementById('moreBrands');
                                var toggle = document.getElementById('brandToggle');
                                if (!panel || !toggle) { return; }
                                var more = 'Xem thêm ({{ $hiddenBrands->count() }})';
                                panel.addEventListener('shown.bs.collapse', function () { toggle.textContent = 'Thu gọn'; });
                                panel.addEventListener('hidden.bs.collapse', function () { toggle.textContent = more; });
                            })();
                        </script>
                    @endif
                </div>

                {{-- Cấp độ (Grade): EG, HG, RG, MG, PG và các loại phụ kiện --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cấp độ (Grade)</label>
                    @foreach($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                   id="cat{{ $category->id }}" @checked(in_array($category->id, (array) request('category_id', [])))>
                            <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->short_name }}</label>
                        </div>
                    @endforeach
                </div>

                {{-- Mức giá --}}
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

                {{-- Series: dạng sổ xuống --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Series</label>
                    <select name="series" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($seriesList as $series)
                            <option value="{{ $series }}" @selected(request('series') === $series)>{{ $series }}</option>
                        @endforeach
                    </select>
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
