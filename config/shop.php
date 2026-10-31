<?php

// Thông tin cửa hàng, khai báo trong file .env (SHOP_ADDRESS, SHOP_EMAIL...)
return [
    'name' => env('SHOP_NAME', 'GunplaHub'),
    'address' => env('SHOP_ADDRESS', ''),
    'phone' => env('SHOP_PHONE', '0815432780'),
    'email' => env('SHOP_EMAIL', ''),

    // Các cam kết hiển thị cạnh nút "Danh sách sản phẩm" ở trang chủ (không bấm được).
    // Sửa nội dung tại đây; icon lấy từ https://icons.getbootstrap.com
    'commitments' => [
        ['icon' => 'badge-check', 'text' => 'Sản phẩm chính hãng'],
        ['icon' => 'package-open', 'text' => 'Kiểm tra khi nhận hàng'],
        ['icon' => 'refresh-cw', 'text' => 'Hoàn tiền nếu sản phẩm không đúng mô tả'],
    ],
];
