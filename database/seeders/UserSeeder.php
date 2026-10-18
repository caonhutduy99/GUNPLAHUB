<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mật khẩu được tự động băm nhờ cast 'hashed' trong Model User.
        // CHỈ DÙNG ĐỂ PHÁT TRIỂN/DEMO, đổi mật khẩu khi đưa lên mạng.
        User::create([
            'full_name' => 'Quản trị viên',
            'email' => 'admin@gunplahub.test',
            'phone' => '0900000001',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        User::create([
            'full_name' => 'Nguyễn Văn A',
            'email' => 'khach1@gunplahub.test',
            'phone' => '0900000002',
            'password' => 'khach123',
            'role' => 'customer',
        ]);

        User::create([
            'full_name' => 'Trần Thị B',
            'email' => 'khach2@gunplahub.test',
            'phone' => '0900000003',
            'password' => 'khach123',
            'role' => 'customer',
        ]);

        // Tài khoản bị Blacklist để thử nghiệm quy tắc chặn đặt hàng
        User::create([
            'full_name' => 'Lê Văn C (Blacklist)',
            'email' => 'blacklist@gunplahub.test',
            'phone' => '0900000004',
            'password' => 'khach123',
            'role' => 'customer',
            'is_blacklisted' => true,
        ]);
    }
}
