@php($editing = isset($product))

<div class="row g-3">
    <div class="col-md-6">
        <label for="sku" class="form-label">Mã SKU</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $editing ? $product->sku : '') }}"
               class="form-control @error('sku') is-invalid @enderror"
               placeholder="Ví dụ: HG-UC-005" @if($editing) readonly @endif required>
        @if($editing)
            <div class="form-text">SKU là mã định danh, không thể sửa.</div>
        @endif
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="category_id" class="form-label">Danh mục</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Chọn danh mục --</option>
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

    <div class="col-12">
        <label for="name" class="form-label">Tên sản phẩm</label>
        <input type="text" id="name" name="name" value="{{ old('name', $editing ? $product->name : '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="price" class="form-label">Giá bán (VNĐ)</label>
        <input type="number" id="price" name="price" min="1" step="1"
               value="{{ old('price', $editing ? (int) $product->price : '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price')
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

    <div class="col-md-4">
        <label for="grade" class="form-label">Cấp độ (Grade)</label>
        <select id="grade" name="grade" class="form-select @error('grade') is-invalid @enderror" required>
            @foreach(\App\Models\Product::GRADES as $grade)
                <option value="{{ $grade }}" @selected(old('grade', $editing ? $product->grade : 'HG') === $grade)>{{ $grade }}</option>
            @endforeach
        </select>
        @error('grade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="scale" class="form-label">Tỷ lệ</label>
        <select id="scale" name="scale" class="form-select @error('scale') is-invalid @enderror" required>
            @foreach(\App\Models\Product::SCALES as $scale)
                <option value="{{ $scale }}" @selected(old('scale', $editing ? $product->scale : '1/144') === $scale)>{{ $scale }}</option>
            @endforeach
        </select>
        @error('scale')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="series" class="form-label">Series</label>
        <input type="text" id="series" name="series" value="{{ old('series', $editing ? $product->series : '') }}"
               class="form-control @error('series') is-invalid @enderror" placeholder="UC, SEED, 00..." required>
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

    <div class="col-md-6">
        <label for="image" class="form-label">Ảnh sản phẩm (jpg, png, webp, tối đa 2MB)</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp"
               class="form-control @error('image') is-invalid @enderror">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($editing && $product->image_url)
            <div class="mt-2">
                <img src="{{ $product->image_url }}" alt="" style="height: 80px; object-fit: contain;" class="border rounded bg-white">
                <span class="text-muted small ms-2">Ảnh hiện tại (chọn ảnh mới để thay)</span>
            </div>
        @endif
    </div>
</div>
