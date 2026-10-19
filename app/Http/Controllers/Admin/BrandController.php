<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Brand;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private function messages(): array
    {
        return array_merge(ValidationMessages::common(), [
            'code.regex' => 'Mã thương hiệu gồm 2-10 chữ hoặc số, viết liền, không dấu (ví dụ: BAN).',
        ]);
    }

    private function attributes(): array
    {
        return ['name' => 'Tên thương hiệu', 'code' => 'Mã thương hiệu', 'sort_order' => 'Thứ tự hiển thị'];
    }

    public function index()
    {
        $brands = Brand::withCount('products')->ordered()->get();

        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
            'code' => ['required', 'regex:/^[A-Z0-9]{2,10}$/', 'unique:brands,code'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], $this->messages(), $this->attributes());
        $data['sort_order'] = $data['sort_order'] ?? 100;

        $brand = Brand::create($data);
        AuditLog::record("Thêm thương hiệu #{$brand->id}: {$brand->name} ({$brand->code})");

        return redirect()->route('admin.brands.index')->with('success', 'Đã thêm thương hiệu.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', [
            'brand' => $brand,
            'codeLocked' => $brand->products()->exists(),
        ]);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        // Thương hiệu đã có sản phẩm thì giữ nguyên mã để SKU không bị lệch
        $codeLocked = $brand->products()->exists();
        $request->merge([
            'code' => $codeLocked ? $brand->code : strtoupper(trim((string) $request->input('code'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name,' . $brand->id],
            'code' => ['required', 'regex:/^[A-Z0-9]{2,10}$/', 'unique:brands,code,' . $brand->id],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], $this->messages(), $this->attributes());
        $data['sort_order'] = $data['sort_order'] ?? 100;

        $oldName = $brand->name;
        $brand->update($data);
        AuditLog::record("Sửa thương hiệu #{$brand->id}: {$oldName} -> {$brand->name} ({$brand->code})");

        return redirect()->route('admin.brands.index')->with('success', 'Đã cập nhật thương hiệu.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Không thể xóa thương hiệu đang có sản phẩm.');
        }

        $name = $brand->name;
        $brand->delete();
        AuditLog::record("Xóa thương hiệu: {$name}");

        return redirect()->route('admin.brands.index')->with('success', 'Đã xóa thương hiệu.');
    }
}
