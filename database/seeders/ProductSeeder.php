<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $brands = Brand::pluck('id', 'name');

        // [sku, danh mục, hãng, tên, giá (VNĐ), tồn kho, grade, tỷ lệ, series, trạng thái]
        // SKU bắt đầu bằng mã hãng. Giá và tồn kho chỉ là DỮ LIỆU MẪU để phát triển/demo.
        // Phụ kiện (decal, sơn, dụng cụ) không có grade, tỷ lệ, series.
        $items = [
            ['BAN-HG-UC-001', 'Gunpla HG', 'Bandai', 'HG 1/144 RX-78-2 Gundam (Revive)', 350000, 25, 'HG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-HG-UC-002', 'Gunpla HG', 'Bandai', 'HG 1/144 Zaku II (Char Aznable)', 320000, 18, 'HG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-HG-UC-003', 'Gunpla HG', 'Bandai', 'HG 1/144 Gundam Barbatos', 380000, 20, 'HG', '1/144', 'IBO', 'Còn hàng'],
            ['BAN-HG-SEED-001', 'Gunpla HG', 'Bandai', 'HG 1/144 Strike Freedom Gundam', 420000, 12, 'HG', '1/144', 'SEED', 'Còn hàng'],
            ['BAN-HG-00-001', 'Gunpla HG', 'Bandai', 'HG 1/144 00 Raiser', 450000, 8, 'HG', '1/144', '00', 'Còn hàng'],
            ['BAN-HG-WFM-001', 'Gunpla HG', 'Bandai', 'HG 1/144 Gundam Aerial', 400000, 30, 'HG', '1/144', 'Witch from Mercury', 'Còn hàng'],
            ['BAN-HG-UC-004', 'Gunpla HG', 'Bandai', 'HG 1/144 Gelgoog', 330000, 0, 'HG', '1/144', 'UC', 'Hết hàng'],
            ['BAN-RG-UC-001', 'Gunpla RG', 'Bandai', 'RG 1/144 RX-78-2 Gundam', 750000, 10, 'RG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-RG-UC-002', 'Gunpla RG', 'Bandai', 'RG 1/144 Zaku II', 680000, 9, 'RG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-RG-SEED-001', 'Gunpla RG', 'Bandai', 'RG 1/144 Strike Freedom Gundam', 950000, 6, 'RG', '1/144', 'SEED', 'Còn hàng'],
            ['BAN-RG-UC-003', 'Gunpla RG', 'Bandai', 'RG 1/144 Unicorn Gundam', 890000, 7, 'RG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-MG-UC-001', 'Gunpla MG', 'Bandai', 'MG 1/100 RX-78-2 Gundam Ver.3.0', 1250000, 8, 'MG', '1/100', 'UC', 'Còn hàng'],
            ['BAN-MG-SEED-001', 'Gunpla MG', 'Bandai', 'MG 1/100 Freedom Gundam Ver.2.0', 1650000, 5, 'MG', '1/100', 'SEED', 'Còn hàng'],
            ['BAN-MG-UC-002', 'Gunpla MG', 'Bandai', 'MG 1/100 Sazabi Ver.Ka', 2450000, 3, 'MG', '1/100', 'UC', 'Còn hàng'],
            ['BAN-MG-00-001', 'Gunpla MG', 'Bandai', 'MG 1/100 Exia', 1100000, 6, 'MG', '1/100', '00', 'Còn hàng'],
            ['BAN-PG-UC-001', 'Gunpla PG', 'Bandai', 'PG 1/60 Unicorn Gundam', 5200000, 2, 'PG', '1/60', 'UC', 'Còn hàng'],
            ['BAN-PG-UC-002', 'Gunpla PG', 'Bandai', 'PG 1/60 RX-78-2 Gundam', 4800000, 2, 'PG', '1/60', 'UC', 'Còn hàng'],
            ['BAN-EG-UC-001', 'Gunpla EG', 'Bandai', 'EG 1/144 RX-78-2 Gundam', 180000, 40, 'EG', '1/144', 'UC', 'Còn hàng'],
            ['BAN-EG-SEED-001', 'Gunpla EG', 'Bandai', 'EG 1/144 Strike Gundam', 190000, 35, 'EG', '1/144', 'SEED', 'Còn hàng'],
            ['BAN-EG-UC-002', 'Gunpla EG', 'Bandai', 'EG 1/144 Zaku II', 170000, 15, 'EG', '1/144', 'UC', 'Ngừng kinh doanh'],
            ['DAB-MG-001', 'Gunpla MG', 'Daban', 'MG 1/100 Strike Freedom Gundam (Daban)', 790000, 10, 'MG', '1/100', 'SEED', 'Còn hàng'],
            ['SHS-MG-001', 'Gunpla MG', 'ShenShou model', 'MG 1/100 Hi-Nu Gundam (ShenShou)', 850000, 6, 'MG', '1/100', 'UC', 'Còn hàng'],
            ['BAN-DEC-001', 'Decal nước – WaterSlide Decal', 'Bandai', 'Decal nước Gundam đa dụng 1/144', 45000, 30, null, null, null, 'Còn hàng'],
            ['SUP-PAINT-001', 'Sơn – Paint Color', 'SUPA model', 'Sơn mô hình SUPA màu xám Gundam 20ml', 35000, 50, null, null, null, 'Còn hàng'],
            ['CHL-TOOL-001', 'Dụng cụ – Tools', 'ChangLong', 'Kìm cắt runner ChangLong', 120000, 25, null, null, null, 'Còn hàng'],
            ['GAO-TOOL-001', 'Dụng cụ – Tools', 'GaoGao', 'Bộ giấy nhám đánh bóng GaoGao', 60000, 40, null, null, null, 'Còn hàng'],
        ];

        foreach ($items as [$sku, $categoryName, $brandName, $name, $price, $stock, $grade, $scale, $series, $status]) {
            Product::create([
                'sku' => $sku,
                'category_id' => $categories[$categoryName],
                'brand_id' => $brands[$brandName],
                'name' => $name,
                'price' => $price,
                'stock_quantity' => $stock,
                'grade' => $grade,
                'scale' => $scale,
                'series' => $series,
                'status' => $status,
            ]);
        }
    }
}
