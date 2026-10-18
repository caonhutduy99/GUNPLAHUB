<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'name');

        // [sku, danh mục, tên, giá (VNĐ), tồn kho, grade, tỷ lệ, series, trạng thái]
        // Giá và tồn kho chỉ là DỮ LIỆU MẪU để phát triển/demo.
        $items = [
            ['HG-UC-001', 'Gunpla HG', 'HG 1/144 RX-78-2 Gundam (Revive)', 350000, 25, 'HG', '1/144', 'UC', 'Còn hàng'],
            ['HG-UC-002', 'Gunpla HG', 'HG 1/144 Zaku II (Char Aznable)', 320000, 18, 'HG', '1/144', 'UC', 'Còn hàng'],
            ['HG-UC-003', 'Gunpla HG', 'HG 1/144 Gundam Barbatos', 380000, 20, 'HG', '1/144', 'IBO', 'Còn hàng'],
            ['HG-SEED-001', 'Gunpla HG', 'HG 1/144 Strike Freedom Gundam', 420000, 12, 'HG', '1/144', 'SEED', 'Còn hàng'],
            ['HG-00-001', 'Gunpla HG', 'HG 1/144 00 Raiser', 450000, 8, 'HG', '1/144', '00', 'Còn hàng'],
            ['HG-WFM-001', 'Gunpla HG', 'HG 1/144 Gundam Aerial', 400000, 30, 'HG', '1/144', 'Witch from Mercury', 'Còn hàng'],
            ['HG-UC-004', 'Gunpla HG', 'HG 1/144 Gelgoog', 330000, 0, 'HG', '1/144', 'UC', 'Hết hàng'],

            ['RG-UC-001', 'Gunpla RG', 'RG 1/144 RX-78-2 Gundam', 750000, 10, 'RG', '1/144', 'UC', 'Còn hàng'],
            ['RG-UC-002', 'Gunpla RG', 'RG 1/144 Zaku II', 680000, 9, 'RG', '1/144', 'UC', 'Còn hàng'],
            ['RG-SEED-001', 'Gunpla RG', 'RG 1/144 Strike Freedom Gundam', 950000, 6, 'RG', '1/144', 'SEED', 'Còn hàng'],
            ['RG-UC-003', 'Gunpla RG', 'RG 1/144 Unicorn Gundam', 890000, 7, 'RG', '1/144', 'UC', 'Còn hàng'],

            ['MG-UC-001', 'Gunpla MG', 'MG 1/100 RX-78-2 Gundam Ver.3.0', 1250000, 8, 'MG', '1/100', 'UC', 'Còn hàng'],
            ['MG-SEED-001', 'Gunpla MG', 'MG 1/100 Freedom Gundam Ver.2.0', 1650000, 5, 'MG', '1/100', 'SEED', 'Còn hàng'],
            ['MG-UC-002', 'Gunpla MG', 'MG 1/100 Sazabi Ver.Ka', 2450000, 3, 'MG', '1/100', 'UC', 'Còn hàng'],
            ['MG-00-001', 'Gunpla MG', 'MG 1/100 Exia', 1100000, 6, 'MG', '1/100', '00', 'Còn hàng'],

            ['PG-UC-001', 'Gunpla PG', 'PG 1/60 Unicorn Gundam', 5200000, 2, 'PG', '1/60', 'UC', 'Còn hàng'],
            ['PG-UC-002', 'Gunpla PG', 'PG 1/60 RX-78-2 Gundam', 4800000, 2, 'PG', '1/60', 'UC', 'Còn hàng'],

            ['EG-UC-001', 'Gunpla EG', 'EG 1/144 RX-78-2 Gundam', 180000, 40, 'EG', '1/144', 'UC', 'Còn hàng'],
            ['EG-SEED-001', 'Gunpla EG', 'EG 1/144 Strike Gundam', 190000, 35, 'EG', '1/144', 'SEED', 'Còn hàng'],
            ['EG-UC-002', 'Gunpla EG', 'EG 1/144 Zaku II', 170000, 15, 'EG', '1/144', 'UC', 'Ngừng kinh doanh'],
        ];

        foreach ($items as [$sku, $catName, $name, $price, $stock, $grade, $scale, $series, $status]) {
            Product::create([
                'sku' => $sku,
                'category_id' => $cat[$catName],
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
