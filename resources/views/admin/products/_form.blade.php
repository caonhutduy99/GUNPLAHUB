@php($editing = isset($product))

<div class="row g-3">
    <div class="col-md-6">
        <label for="brand_id" class="form-label">Thương hiệu</label>
        <select id="brand_id" name="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
            <option value="">-- Chọn thương hiệu --</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" data-code="{{ $brand->code }}"
                    @selected(old('brand_id', $editing ? $product->brand_id : '') == $brand->id)>
                    {{ $brand->name }} ({{ $brand->code }})
                </option>
            @endforeach
        </select>
        @error('brand_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="category_id" class="form-label">Cấp độ / Loại sản phẩm</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Chọn --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $editing ? $product->category_id : '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="sku" class="form-label">Mã SKU</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $editing ? $product->sku : '') }}"
               class="form-control text-uppercase @error('sku') is-invalid @enderror"
               placeholder="Ví dụ: BAN-HG-UC-005" @if($editing) readonly @endif required>
        @if($editing)
            <div class="form-text">SKU là mã định danh, không thể sửa.</div>
        @else
            <div class="form-text">Bắt đầu bằng mã của thương hiệu đã chọn (chọn thương hiệu xong, hệ thống tự điền sẵn phần đầu).</div>
        @endif
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">Tên sản phẩm</label>
        <input type="text" id="name" name="name" value="{{ old('name', $editing ? $product->name : '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Phần thiết lập giá & quy tắc: giá bán không thấp hơn giá nhập --}}
    <div class="col-md-4">
        <label for="cost_price" class="form-label">Giá nhập / Giá vốn (VNĐ) <span class="text-danger">*</span></label>
        <input type="number" id="cost_price" name="cost_price" min="0" step="1000"
               value="{{ old('cost_price', $editing ? (int) $product->cost_price : '') }}"
               class="form-control @error('cost_price') is-invalid @enderror" required
               placeholder="Ví dụ: 250000">
        <div class="form-text">Giá vốn khi nhập kho.</div>
        @error('cost_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="price" class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
        <input type="number" id="price" name="price" min="1" step="1000"
               value="{{ old('price', $editing ? (int) $product->price : '') }}"
               class="form-control @error('price') is-invalid @enderror" required
               placeholder="Ví dụ: 350000">
        <div class="form-text text-danger" id="priceRuleNotice">
            <i class="bi bi-shield-exclamation me-1"></i>Quy tắc: Giá bán phải lớn hơn hoặc bằng giá nhập.
        </div>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="price_type" class="form-label">Loại giá <span class="text-danger">*</span></label>
        <select id="price_type" name="price_type" class="form-select @error('price_type') is-invalid @enderror" required>
            @foreach(\App\Models\Product::PRICE_TYPES as $ptype)
                <option value="{{ $ptype }}" @selected(old('price_type', $editing ? ($product->price_type ?? 'Giá bán lẻ') : 'Giá bán lẻ') === $ptype)>
                    {{ $ptype }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Phân loại giá áp dụng hiện tại.</div>
        @error('price_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="price_note" class="form-label">Ghi chú điều chỉnh giá (tùy chọn)</label>
        <input type="text" id="price_note" name="price_note" value="{{ old('price_note') }}"
               class="form-control @error('price_note') is-invalid @enderror"
               placeholder="Ví dụ: Nhập lô hàng mới, Chương trình khuyến mãi hè, Thay đổi giá nhà cung cấp...">
        <div class="form-text">Nội dung này sẽ được ghi vào <strong>Lịch sử giá</strong> của sản phẩm để theo dõi.</div>
        @error('price_note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
        <input type="number" id="stock_quantity" name="stock_quantity" min="0" step="1"
               value="{{ old('stock_quantity', $editing ? $product->stock_quantity : 0) }}"
               class="form-control @error('stock_quantity') is-invalid @enderror" required>
        @error('stock_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="scale" class="form-label">Tỷ lệ</label>
        <select id="scale" name="scale" class="form-select @error('scale') is-invalid @enderror">
            <option value="">-- Không có --</option>
            @foreach(\App\Models\Product::SCALES as $scale)
                <option value="{{ $scale }}" @selected(old('scale', $editing ? $product->scale : '') === $scale)>{{ $scale }}</option>
            @endforeach
        </select>
        <div class="form-text">Bắt buộc với mô hình Gunpla, bỏ trống với phụ kiện.</div>
        @error('scale')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="series" class="form-label">Series</label>
        <input type="text" id="series" name="series" value="{{ old('series', $editing ? $product->series : '') }}"
               class="form-control @error('series') is-invalid @enderror" placeholder="UC, SEED, 00...">
        <div class="form-text">Bắt buộc với mô hình Gunpla, bỏ trống với phụ kiện.</div>
        @error('series')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Trạng thái</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(\App\Models\Product::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $editing ? $product->status : 'Còn hàng') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <div class="form-text">"Còn hàng" và "Hết hàng" tự động theo số lượng tồn kho. Chọn "Ngừng kinh doanh" để ẩn sản phẩm khỏi cửa hàng.</div>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="images" class="form-label">Ảnh sản phẩm (có thể chọn tối đa 8 ảnh, mỗi ảnh tối đa 2MB)</label>
        <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple
               class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
        @error('images')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @error('images.*')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($editing && $product->images->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mt-2">
                @foreach($product->images as $image)
                    <img src="{{ $image->url }}" alt="" style="height: 80px; width: 80px; object-fit: contain;"
                         class="border rounded bg-white p-1">
                @endforeach
            </div>
            <div class="form-text">Ảnh mới sẽ được thêm vào bộ sưu tập hiện tại.</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function () {
        // Kiểm tra quy tắc giá: giá bán >= giá nhập
        var costInput = document.getElementById('cost_price');
        var priceInput = document.getElementById('price');
        var notice = document.getElementById('priceRuleNotice');

        function validatePriceRule() {
            if (!costInput || !priceInput || !notice) return;
            var cost = parseFloat(costInput.value) || 0;
            var price = parseFloat(priceInput.value) || 0;

            if (price > 0 && cost > 0 && price < cost) {
                notice.className = 'form-text text-danger fw-bold';
                notice.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>LỖI: Giá bán (' + price.toLocaleString() + ' đ) đang THẤP HƠN giá nhập (' + cost.toLocaleString() + ' đ)!';
                priceInput.classList.add('is-invalid');
            } else if (price > 0 && cost > 0) {
                var diff = price - cost;
                var percent = cost > 0 ? Math.round((diff / cost) * 100) : 0;
                notice.className = 'form-text text-success';
                notice.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Hợp lệ. Chênh lệch lợi nhuận: +' + diff.toLocaleString() + ' đ (+' + percent + '%)';
                priceInput.classList.remove('is-invalid');
            } else {
                notice.className = 'form-text text-muted';
                notice.innerHTML = '<i class="bi bi-shield-check me-1"></i>Quy tắc: Giá bán phải lớn hơn hoặc bằng giá nhập.';
                priceInput.classList.remove('is-invalid');
            }
        }

        if (costInput && priceInput) {
            costInput.addEventListener('input', validatePriceRule);
            priceInput.addEventListener('input', validatePriceRule);
            validatePriceRule();
        }

        @unless($editing)
        // Chọn thương hiệu xong thì tự điền tiền tố mã thương hiệu vào ô SKU
        var brand = document.getElementById('brand_id');
        var sku = document.getElementById('sku');
        if (brand && sku) {
            var codes = Array.prototype.map.call(brand.options, function (o) { return o.getAttribute('data-code'); })
                .filter(function (c) { return c; });

            brand.addEventListener('change', function () {
                var option = brand.options[brand.selectedIndex];
                var code = option ? option.getAttribute('data-code') : null;
                if (!code) { return; }

                var rest = sku.value;
                for (var i = 0; i < codes.length; i++) {
                    if (rest.toUpperCase().indexOf(codes[i] + '-') === 0) {
                        rest = rest.substring(codes[i].length + 1);
                        break;
                    }
                }
                sku.value = code + '-' + rest;
            });
        }
        @endunless
    })();
</script>
@endpush
