<?php

namespace App\Support;

/**
 * Quy tắc tính tiền theo báo cáo (mục 1.2.4):
 * - Phí vận chuyển mặc định 25.000 VNĐ toàn quốc.
 * - Miễn phí vận chuyển cho đơn có tiền hàng từ 1.000.000 VNĐ.
 * - Tổng tiền = Tiền hàng + Phí vận chuyển - Giảm giá.
 */
class Pricing
{
    public const SHIPPING_FEE = 25000;
    public const FREE_SHIPPING_FROM = 1000000;

    public static function shippingFee(float|int $subtotal): int
    {
        return $subtotal >= self::FREE_SHIPPING_FROM ? 0 : self::SHIPPING_FEE;
    }
}
