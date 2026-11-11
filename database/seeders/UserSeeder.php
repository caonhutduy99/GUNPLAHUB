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
        // 'role' và 'is_blacklisted' không nằm trong $fillable nên gán bằng forceFill.
        $accounts = [
            ['Quản trị viên', 'admin@gunplahub.test', '0900000001', 'admin123', 'admin', false],
            ['Nguyễn Văn A', 'khach1@gunplahub.test', '0900000002', 'khach123', 'customer', false],
            ['Trần Thị B', 'khach2@gunplahub.test', '0900000003', 'khach123', 'customer', false],
            // Tài khoản bị Blacklist để thử nghiệm quy tắc chặn đặt hàng
            ['Lê Văn C (Blacklist)', 'blacklist@gunplahub.test', '0900000004', 'khach123', 'customer', true],
        ];

        foreach ($accounts as [$fullName, $email, $phone, $password, $role, $blacklisted]) {
            User::firstOrNew(['email' => $email])->forceFill([
                'full_name' => $fullName,
                'phone' => $phone,
                'password' => $password,
                'role' => $role,
                'is_blacklisted' => $blacklisted,
            ])->save();
        }
    }
}
