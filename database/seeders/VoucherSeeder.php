<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::create([
            'code' => 'GUNPLA20K',
            'discount_amount' => 20000,
            'min_order_value' => 300000,
            'usage_limit' => 100,
            'expiry_date' => now()->addMonths(6),
        ]);

        Voucher::create([
            'code' => 'WELCOME50K',
            'discount_amount' => 50000,
            'min_order_value' => 700000,
            'usage_limit' => 50,
            'expiry_date' => now()->addMonths(3),
        ]);

        // Mã đã hết hạn để thử nghiệm quy tắc kiểm tra hạn sử dụng
        Voucher::create([
            'code' => 'HETHAN',
            'discount_amount' => 10000,
            'min_order_value' => 100000,
            'usage_limit' => 10,
            'expiry_date' => now()->subDay(),
        ]);
    }
}
