{{-- Bộ lọc + sắp xếp + danh sách sản phẩm kiểu Clean & Premium Storefront --}}
@php
    $selectedBrands = (array) request('brand_id', []);
    $brandVisible = 6;
    $hiddenBrands = $brands->slice($brandVisible);
    $expandBrands = $hiddenBrands->contains(fn ($b) => in_array($b->id, $selectedBrands));
@endphp

<div class="row g-4">
    {{-- Sidebar Bộ lọc thông minh --}}
    <div class="col-lg-3 mb-4">
        <form method="GET" action="{{ $action }}" class="card shadow-sm border" style="border-radius: var(--gh-radius); position: sticky; top: 84px;">
            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
                <span class="fw-bold d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                    <x-icon name="funnel" class="text-danger" /> Bộ lọc tìm kiếm
                </span>
                @if(request()->hasAny(['q', 'brand_id', 'category_id', 'price_range', 'price_min', 'price_max', 'series', 'in_stock']))
                    <a href="{{ $action }}" class="badge bg-danger-subtle text-danger text-decoration-none border border-danger-subtle px-2 py-1">
                        Xóa lọc
                    </a>
                @endif
            </div>

            <div class="card-body p-3" style="max-height: calc(100vh - 160px); overflow-y: auto;">
                {{-- Từ khóa --}}
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Từ khóa</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary border-end-0"><x-icon name="search" class="text-muted" /></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Tên hoặc Mã sản phẩm...">
                    </div>
                </div>

                {{-- Thương hiệu --}}
                <div class="mb-3 border-top pt-3" style="border-color: var(--gh-border) !important;">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Thương hiệu</label>

                    @foreach($brands->take($brandVisible) as $brand)
                        <div class="form-check form-check-sm mb-1">
                            <input class="form-check-input" type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                   id="brand{{ $brand->id }}" @checked(in_array($brand->id, $selectedBrands))>
                            <label class="form-check-label small" for="brand{{ $brand->id }}">{{ $brand->name }}</label>
                        </div>
                    @endforeach

                    @if($hiddenBrands->isNotEmpty())
                        <div class="collapse {{ $expandBrands ? 'show' : '' }}" id="moreBrands">
                            @foreach($hiddenBrands as $brand)
                                <div class="form-check form-check-sm mb-1">
                                    <input class="form-check-input" type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                           id="brand{{ $brand->id }}" @checked(in_array($brand->id, $selectedBrands))>
                                    <label class="form-check-label small" for="brand{{ $brand->id }}">{{ $brand->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <a href="#moreBrands" id="brandToggle" class="small text-danger text-decoration-none d-inline-block mt-1 fw-semibold" role="button"
                           data-bs-toggle="collapse" aria-controls="moreBrands"
                           aria-expanded="{{ $expandBrands ? 'true' : 'false' }}">{{ $expandBrands ? 'Thu gọn' : '+ Xem thêm ' . $hiddenBrands->count() . ' hãng' }}</a>
                        <script>
                            (function () {
                                var panel = document.getElementById('moreBrands');
                                var toggle = document.getElementById('brandToggle');
                                if (!panel || !toggle) { return; }
                                var more = '+ Xem thêm {{ $hiddenBrands->count() }} hãng';
                                panel.addEventListener('shown.bs.collapse', function () { toggle.textContent = 'Thu gọn'; });
                                panel.addEventListener('hidden.bs.collapse', function () { toggle.textContent = more; });
                            })();
                        </script>
                    @endif
                </div>

                {{-- Cấp độ (Grade) --}}
                <div class="mb-3 border-top pt-3" style="border-color: var(--gh-border) !important;">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Cấp độ (Grade)</label>
                    @foreach($categories as $category)
                        <div class="form-check form-check-sm mb-1">
                            <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                   id="cat{{ $category->id }}" @checked(in_array($category->id, (array) request('category_id', [])))>
                            <label class="form-check-label small" for="cat{{ $category->id }}">{{ $category->short_name }}</label>
                        </div>
                    @endforeach
                </div>

                {{-- Mức giá --}}
                <div class="mb-3 border-top pt-3" style="border-color: var(--gh-border) !important;">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Khoảng giá</label>
                    @foreach($priceRanges as $key => $range)
                        <div class="form-check form-check-sm mb-1">
                            <input class="form-check-input" type="checkbox" name="price_range[]" value="{{ $key }}"
                                   id="price_{{ $key }}" @checked(in_array($key, (array) request('price_range', [])))>
                            <label class="form-check-label small" for="price_{{ $key }}">{{ $range['label'] }}</label>
                        </div>
                    @endforeach

                    <div class="small text-muted mt-2 mb-1" style="font-size: 0.75rem;">Hoặc nhập khoảng giá (VNĐ):</div>
                    <div class="d-flex align-items-center gap-1">
                        <input type="number" name="price_min" min="0" value="{{ request('price_min') }}" class="form-control form-control-sm" placeholder="Từ">
                        <span class="text-muted">–</span>
                        <input type="number" name="price_max" min="0" value="{{ request('price_max') }}" class="form-control form-control-sm" placeholder="Đến">
                    </div>
                </div>

                {{-- Series --}}
                <div class="mb-3 border-top pt-3" style="border-color: var(--gh-border) !important;">
                    <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Dòng phim / Series</label>
                    <select name="series" class="form-select form-select-sm">
                        <option value="">Tất cả Series</option>
                        @foreach($seriesList as $series)
                            <option value="{{ $series }}" @selected(request('series') === $series)>{{ $series }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tồn kho --}}
                <div class="form-check form-switch mb-3 border-top pt-3" style="border-color: var(--gh-border) !important;">
                    <input class="form-check-input" type="checkbox" role="switch" name="in_stock" value="1" id="in_stock" @checked(request()->boolean('in_stock'))>
                    <label class="form-check-label small fw-semibold" for="in_stock">Chỉ hiện còn hàng</label>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill fw-semibold py-2">
                        <x-icon name="check" class="me-1" /> Áp dụng bộ lọc
                    </button>
                    @if(request()->hasAny(['q', 'brand_id', 'category_id', 'price_range', 'price_min', 'price_max', 'series', 'in_stock']))
                        <a href="{{ $action }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            Xóa tất cả bộ lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Danh sách sản phẩm & Toolbar sắp xếp --}}
    <div class="col-lg-9">
        {{-- Toolbar phía trên --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 bg-body rounded-3 border" style="border-color: var(--gh-border) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary fw-semibold px-2 py-1" style="font-size: 0.8rem;">
                    {{ $products->total() }} sản phẩm
                </span>
                @if(request('q'))
                    <span class="small text-muted">Kết quả cho: <strong>"{{ request('q') }}"</strong></span>
                @endif
            </div>

            <div class="d-flex flex-wrap align-items-center gap-1">
                <span class="small text-muted me-1 fw-semibold">Sắp xếp:</span>
                @foreach($sorts as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}"
                       class="btn btn-sm rounded-pill px-3 py-1 {{ $sort === $key ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}"
                       style="font-size: 0.8rem;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Lưới thẻ sản phẩm --}}
        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-6 col-md-4">
                    @include('shop._card', ['product' => $product])
                </div>
            @empty
                <div class="col-12">
                    <div class="card border text-center p-5" style="border-radius: var(--gh-radius);">
                        <x-icon name="box" size="3rem" class="text-muted opacity-50 mb-3" />
                        <h5 class="fw-bold mb-2">Không tìm thấy sản phẩm phù hợp</h5>
                        <p class="text-muted small mb-3">Vui lòng thử tìm với từ khóa khác hoặc bỏ bớt các tiêu chí lọc đang chọn.</p>
                        <div>
                            <a href="{{ $action }}" class="btn btn-danger btn-sm rounded-pill px-4">
                                Xem tất cả sản phẩm
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Phân trang chuẩn --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
