<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.products.index', [
            'products' => $query->orderBy('sku')->paginate(10)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), ValidationMessages::common(), $this->attributes());

        unset($data['image']);
        $data['status'] = $this->syncStatus($data['status'], (int) $data['stock_quantity']);
        if ($path = $this->saveImage($request)) {
            $data['image'] = $path;
        }

        $product = Product::create($data);
        AuditLog::record("Thêm sản phẩm {$product->sku}: {$product->name}");

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->rules($product), ValidationMessages::common(), $this->attributes());

        unset($data['image']);
        $data['status'] = $this->syncStatus($data['status'], (int) $data['stock_quantity']);

        if ($path = $this->saveImage($request)) {
            $this->deleteImage($product->image); // xóa ảnh cũ
            $data['image'] = $path;
        }

        $oldPrice = $product->price;
        $oldStock = $product->stock_quantity;
        $product->update($data);

        $note = '';
        if ((float) $oldPrice !== (float) $product->price) {
            $note .= " | giá {$oldPrice} -> {$product->price}";
        }
        if ($oldStock !== $product->stock_quantity) {
            $note .= " | tồn kho {$oldStock} -> {$product->stock_quantity}";
        }
        AuditLog::record("Sửa sản phẩm {$product->sku}{$note}");

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    /**
     * "Xóa" sản phẩm = chuyển sang Ngừng kinh doanh (xóa mềm).
     * Không xóa thật để không làm hỏng lịch sử đơn hàng.
     * Muốn bán lại: vào Sửa và đổi trạng thái.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['status' => 'Ngừng kinh doanh']);
        AuditLog::record("Ngừng kinh doanh sản phẩm {$product->sku}");

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã chuyển sang "Ngừng kinh doanh".');
    }

    // ---------------- Hàm phụ ----------------

    private function rules(?Product $product = null): array
    {
        return [
            // SKU là khóa chính nên chỉ nhập khi thêm mới, không cho sửa
            'sku' => $product ? [] : [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:products,sku',
            ],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'grade' => ['required', Rule::in(Product::GRADES)],
            'scale' => ['required', Rule::in(Product::SCALES)],
            'series' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function attributes(): array
    {
        return [
            'sku' => 'Mã SKU',
            'category_id' => 'Danh mục',
            'name' => 'Tên sản phẩm',
            'price' => 'Giá bán',
            'stock_quantity' => 'Số lượng tồn kho',
            'grade' => 'Cấp độ (Grade)',
            'scale' => 'Tỷ lệ',
            'series' => 'Series',
            'status' => 'Trạng thái',
            'image' => 'Ảnh sản phẩm',
        ];
    }

    /**
     * Trạng thái Còn hàng / Hết hàng tự động theo tồn kho,
     * trừ khi admin chọn Ngừng kinh doanh.
     */
    private function syncStatus(string $status, int $stock): string
    {
        if ($status === 'Ngừng kinh doanh') {
            return $status;
        }

        return $stock > 0 ? 'Còn hàng' : 'Hết hàng';
    }

    private function saveImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::uuid() . '.' . $file->extension();
        $file->move(public_path('images/products'), $name);

        return 'images/products/' . $name;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
